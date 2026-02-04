<?php

namespace App\Filament\Resources\AdminSales\Pages;

use App\Filament\Resources\AdminSales\AdminSaleResource;
use App\Models\Inventory;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
class CreateAdminSale extends CreateRecord
{
    protected static string $resource = AdminSaleResource::class;
 protected function mutateFormDataBeforeCreate(array $data): array
{
    $items = collect($data['saleItems'] ?? [])
        ->filter(fn ($i) => filled($i['product_id'] ?? null))
        ->values()
        ->all();

    if (count($items) < 1) {
        throw ValidationException::withMessages([
            'saleItems' => 'Debes agregar al menos 1 producto.',
        ]);
    }

    $data['saleItems'] = $items;

    $data['total_amount'] = collect($items)
        ->sum(fn ($i) => (float) ($i['subtotal'] ?? 0));

    return $data;
}
    #[On('qr-scanned')]
    public function addProductByQr(string $code): void
    {
        $code = trim($code);

        $product = Product::query()
            ->where('qr_reference', $code)
            ->where('active', true)
            ->first();

        if (! $product) {
            Notification::make()
                ->title("QR no encontrado o producto inactivo: {$code}")
                ->danger()
                ->send();
            return;
        }

        $stock = (int) Inventory::query()
            ->where('product_id', $product->id)
            ->sum('quantity');

        if ($stock <= 0) {
            Notification::make()
                ->title("Sin stock: {$product->name}")
                ->danger()
                ->send();
            return;
        }

        $state = $this->form->getState();
        $items = $state['saleItems'] ?? [];

        $found = false;

        foreach ($items as $i => $item) {
            if ((int) ($item['product_id'] ?? 0) === (int) $product->id) {
                $newQty = (int) ($item['quantity'] ?? 0) + 1;
                if ($newQty > $stock) $newQty = $stock;

                $unit = (float) ($item['unit_price'] ?? $product->price);

                $items[$i]['quantity'] = $newQty;
                $items[$i]['stock_cached'] = $stock;
                $items[$i]['unit_price'] = $unit;
                $items[$i]['subtotal'] = round($newQty * $unit, 2);

                // mover al inicio (último arriba)
                $row = $items[$i];
                unset($items[$i]);
                $items = array_values($items);
                array_unshift($items, $row);

                $found = true;
                break;
            }
        }

        if (! $found) {
            array_unshift($items, [
                'product_id' => $product->id,
                'stock_cached' => $stock,
                'quantity' => 1,
                'unit_price' => (float) $product->price,
                'subtotal' => round((float) $product->price, 2),
            ]);
        }

        $state['saleItems'] = array_values($items);
        $this->form->fill($state);

        Notification::make()
            ->title("Agregado: {$product->name}")
            ->success()
            ->send();
    }
}
