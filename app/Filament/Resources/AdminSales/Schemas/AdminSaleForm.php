<?php

namespace App\Filament\Resources\AdminSales\Schemas;

use App\Models\Inventory;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class AdminSaleForm
{
    /** stock total por producto (sumando inventarios) */

    /**
     * Opciones filtradas: solo productos activos con stock total > 0.
     */
  protected static ?array $productOptionsCache = null;
protected static array $productMeta = []; // [id => ['price' => x, 'stock' => y]]

protected static function productOptions(): array
{
    if (self::$productOptionsCache !== null) {
        return self::$productOptionsCache;
    }

    $rows = DB::table('products')
        ->join('inventories', 'inventories.product_id', '=', 'products.id')
        ->where('products.active', 1)
        ->groupBy('products.id', 'products.name', 'products.price')
        ->havingRaw('SUM(inventories.quantity) > 0')
        ->orderBy('products.name')
        ->selectRaw('products.id, products.name, products.price, SUM(inventories.quantity) as stock_total')
        ->get();

    $out = [];

    foreach ($rows as $r) {
        $id = (int) $r->id;

        // guardamos meta para NO volver a consultar al seleccionar
        self::$productMeta[$id] = [
            'price' => (float) $r->price,
            'stock' => (int) $r->stock_total,
        ];

        $out[$id] = "{$r->name} — Stock: {$r->stock_total} — $" . number_format((float) $r->price, 2);
    }

    return self::$productOptionsCache = $out;
}


    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            // =======================
            // ARRIBA: Datos de la venta
            // =======================
            Section::make('Datos de la venta')
                ->columns(['default' => 1, 'md' => 12])
                ->schema([
                    Hidden::make('admin_id')
                        ->default(fn () => auth()->id())
                        ->dehydrated(true)
                        ->required(),

                    DateTimePicker::make('sale_date')
                        ->label('Fecha')
                        ->default(now())
                        ->required()
                        ->columnSpan(['md' => 4]),

                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'pending' => 'Pendiente',
                            'paid' => 'Pagada',
                            'shipped' => 'Enviada',
                            'cancelled' => 'Cancelada',
                        ])
                        ->default('pending')
                        ->required()
                        ->columnSpan(['md' => 4]),

                    TextInput::make('customer_name')
                        ->label('Cliente')
                        ->default(fn () => auth()->user()?->name)
                        ->readOnly()
                        ->columnSpan(['md' => 4]),

                    Textarea::make('notes')
                        ->label('Notas')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            // =======================
            // ABAJO: Productos
            // =======================
            Section::make('Productos')
                ->description('Agrega productos. El último agregado queda arriba. Cantidad no puede superar stock.')
                ->columnSpanFull()
                ->schema([

                    // ===== Barra superior: BOTONES (Agregar + Escanear QR) =====
                    Grid::make(['default' => 1, 'md' => 12])->schema([

                        Placeholder::make('top_actions_left')
                            ->label('')
                            ->content('')
                            ->dehydrated(false)
                            ->columnSpan(['md' => 6])
                            ->hintAction(
                                Action::make('addNewItem')
                                    ->label('Agregar producto')
                                    ->action(function (callable $get, callable $set) {
                                        $items = $get('saleItems') ?? [];

                                        // Insertar al inicio (último arriba)
                                        array_unshift($items, [
                                            'product_id' => null,
                                            'stock_cached' => null,
                                            'quantity' => 1,
                                            'unit_price' => null,
                                            'subtotal' => 0,
                                        ]);

                                        $set('saleItems', $items);
                                    })
                            ),

                        // Botón QR (abre modal con JS)
                        Placeholder::make('top_actions_right')
                            ->label('')
                            ->dehydrated(false)
                            ->columnSpan(['md' => 6])
                            ->content(new HtmlString(<<<'HTML'
<div class="flex justify-end">
    <button
        type="button"
        class="fi-btn fi-btn-size-md fi-btn-color-primary"
        onclick="window.dispatchEvent(new CustomEvent('open-qr-scanner'))"
    >
        Escanear QR
    </button>
</div>

<!-- Modal / contenedor del scanner -->
<div id="qrScannerModal" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-lg rounded-xl bg-white p-4 shadow">
            <div class="flex items-center justify-between gap-2">
                <div class="text-lg font-semibold">Escanear código QR</div>
                <button type="button" class="fi-btn fi-btn-size-sm" onclick="window.dispatchEvent(new CustomEvent('close-qr-scanner'))">
                    Cerrar
                </button>
            </div>

            <div class="mt-3 text-sm text-gray-600">
                Apunta la cámara al QR del producto. Cuando lo detecte, se agregará automáticamente a la venta.
            </div>

            <div class="mt-4">
                <div id="qr-reader" class="w-full"></div>
            </div>

            <div class="mt-3 text-xs text-gray-500">
                Nota: En iPhone/Safari debes permitir acceso a la cámara.
            </div>
        </div>
    </div>
</div>
HTML)),
                    ]),

                    // ===== Items (último arriba) =====
               Repeater::make('saleItems')
                ->relationship('saleItems')
    ->dehydrated(true) 
    ->minItems(1)
    ->required()
    ->defaultItems(0)
    ->addable(false)
    ->deletable()
    ->deleteAction(fn ($action) => $action->action(function (array $arguments, callable $get, callable $set): void {
        $items = $get('saleItems') ?? [];

        $index = $arguments['item'] ?? null;
        if ($index === null || ! array_key_exists($index, $items)) {
            return;
        }

        unset($items[$index]);

        // Reindexar + setear state REAL (esto hace que el contador y total bajen)
        $set('saleItems', array_values($items));
    }))
    ->reorderable()
    ->collapsible()
    ->collapsed(false)
    ->live()

                        ->schema([

                            // Grid interno para alinear perfecto
                            Grid::make(['default' => 1, 'md' => 12])->schema([

                                Hidden::make('stock_cached')
                                    ->dehydrated(false),

                                Select::make('product_id')
                                    ->label('Producto')
                                    ->required()
                                    ->options(fn () => self::productOptions())
                                    ->searchable()
                                    ->columnSpan(['md' => 6])
                                    ->reactive()
                              ->afterStateUpdated(function ($state, callable $set, callable $get) {
    if (! $state) {
        $set('stock_cached', null);
        $set('unit_price', null);
        $set('quantity', 1);
        $set('subtotal', 0);
        return;
    }

    // Asegura que el cache/meta esté cargado
    self::productOptions();

    $id = (int) $state;

    $meta = self::$productMeta[$id] ?? null;

    // si no está en meta, es porque no cumple (activo + stock>0)
    if (! $meta || $meta['stock'] <= 0) {
        $set('product_id', null);
        return;
    }

    $stock = (int) $meta['stock'];
    $unit  = (float) $meta['price'];

    $set('stock_cached', $stock);
    $set('unit_price', $unit);

    $qty = (int) ($get('quantity') ?? 1);
    $qty = max(1, min($qty, $stock));

    $set('quantity', $qty);
    $set('subtotal', round($qty * $unit, 2));
}),


                                Placeholder::make('stock_info')
                                    ->label('Stock disp.')
                                    ->columnSpan(['md' => 2])
                                    ->content(fn (callable $get) => ($get('stock_cached') === null) ? '—' : (string) $get('stock_cached'))
                                    ->live(),

                                TextInput::make('quantity')
                                    ->label('Cant.')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(['md' => 1])
                                    ->reactive()
                                    ->maxValue(fn (callable $get) => $get('stock_cached') ?? null)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $qty = (int) $state;
                                        $unit = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', round($qty * $unit, 2));
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Preciounit.')
                                    ->numeric()
                                    ->required()
                                    ->readOnly()
                                    ->columnSpan(['md' => 1])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $qty = (int) ($get('quantity') ?? 0);
                                        $set('subtotal', round($qty * (float) $state, 2));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->columnSpan(['md' => 2]),
                            ]),
                        ]),

                    Grid::make(['default' => 1, 'md' => 12])->schema([
                       Placeholder::make('items_count')
    ->label('Cantidad de productos')
    ->columnSpan(['md' => 4])
    ->content(function (callable $get) {
        $items = $get('saleItems') ?? [];

        return collect($items)
            ->filter(fn ($item) => is_array($item) && filled($item['product_id'] ?? null))
            ->count();
    })
    ->live(),


                        Placeholder::make('total_preview')
                            ->label('Total a pagar')
                            ->columnSpan(['md' => 8])
                            ->content(function (callable $get) {
    $items = $get('saleItems') ?? [];

    $total = collect($items)
        ->filter(fn ($item) => is_array($item) && filled($item['product_id'] ?? null))
        ->sum(fn ($item) => (float) ($item['subtotal'] ?? 0));

    return '$' . number_format($total, 2);
})

                            ->live(),
                    ]),

                    Hidden::make('total_amount'),
                ]),
        ]);
    }
}
