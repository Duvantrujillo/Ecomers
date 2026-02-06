<?php

namespace App\Filament\Resources\AdminSales\Pages;

use App\Filament\Resources\AdminSales\AdminSaleResource;
use App\Models\Inventory;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditAdminSale extends EditRecord
{
    protected static string $resource = AdminSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            //este es el boton de eliminar
           // DeleteAction::make(),
        ];
    }

    /**
     * 🔹 Escucha el evento del QR y agrega el producto a saleItems
     */
    #[On('qr-scanned')]
    public function addProductByQr(string $code): void
    {
        $code = trim($code);

        // Buscar producto por qr_reference
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

        // Calcular stock total
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

        // Estado actual del formulario
        $state = $this->form->getState();
        $items = $state['saleItems'] ?? [];

        $found = false;

        // Si el producto ya existe, sumar cantidad
        foreach ($items as $i => $item) {
            if ((int) ($item['product_id'] ?? 0) === (int) $product->id) {

                $newQty = (int) ($item['quantity'] ?? 0) + 1;
                if ($newQty > $stock) {
                    $newQty = $stock;
                }

                $unit = (float) ($item['unit_price'] ?? $product->price);

                $items[$i]['quantity'] = $newQty;
                $items[$i]['stock_cached'] = $stock;
                $items[$i]['unit_price'] = $unit;
                $items[$i]['subtotal'] = round($newQty * $unit, 2);

                // Mover este item al inicio (último escaneado arriba)
                $row = $items[$i];
                unset($items[$i]);
                $items = array_values($items);
                array_unshift($items, $row);

                $found = true;
                break;
            }
        }

        // Si no existe, agregarlo como nuevo item
        if (! $found) {
            array_unshift($items, [
                'product_id'   => $product->id,
                'stock_cached' => $stock,
                'quantity'     => 1,
                'unit_price'   => (float) $product->price,
                'subtotal'     => round((float) $product->price, 2),
            ]);
        }

        // Actualizar el formulario
        $state['saleItems'] = array_values($items);
        $this->form->fill($state);

        Notification::make()
            ->title("Agregado: {$product->name}")
            ->success()
            ->send();
    }
}
