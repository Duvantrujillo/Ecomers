<?php

namespace App\Filament\Resources\SaleItems\Schemas;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Inventory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('sale_id')
                    ->label('Sale')
                    ->options(fn() => Sale::where('status', 'paid')->pluck('customer_name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('product_id')
                    ->label('Product')
                    ->options(fn() => Product::where('active', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('unit_price', $product->price);
                            $set('subtotal', $product->price * ($get('quantity') ?? 0));
                        }
                    }),

                TextInput::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $unitPrice = $get('unit_price') ?? 0;
                        $set('subtotal', $state * $unitPrice);
                    })
                    ->rules(function ($get) {
                        $productId = $get('product_id');
                        if (!$productId) {
                            return ['required', 'numeric', 'min:1'];
                        }
                        $stock = Inventory::where('product_id', $productId)->value('quantity') ?? 0;
                        return ['required', 'numeric', 'min:1', "max:$stock"];
                    })
                    ->helperText(function ($get) {
                        $productId = $get('product_id');
                        $stock = $productId ? Inventory::where('product_id', $productId)->value('quantity') : 0;
                        return "Stock disponible: $stock";
                    }),

                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated() // ✅ Asegura que se envíe al submit
                    ->required(),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated() // ✅ También se envía
                    ->required(),
            ]);
    }
}
