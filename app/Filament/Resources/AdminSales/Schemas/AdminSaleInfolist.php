<?php

namespace App\Filament\Resources\AdminSales\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AdminSaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de la venta')
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextEntry::make('admin.name')->label('Admin'),
                    TextEntry::make('sale_date')->label('Fecha')->dateTime(),

                    TextEntry::make('total_amount')
                        ->label('Total')
                        ->money('COP') // o ->numeric()
                        ->placeholder('-'),

                    TextEntry::make('status')->label('Estado')->badge(),
                    TextEntry::make('customer_name')->label('Cliente')->placeholder('-'),

                    TextEntry::make('notes')
                        ->label('Notas')
                        ->placeholder('-')
                        ->columnSpanFull(),

                    TextEntry::make('created_at')->label('Creado')->dateTime()->placeholder('-'),
                    TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
                ]),

            Section::make('Productos vendidos')
                ->description('Listado de productos incluidos en esta venta.')
                ->schema([
                    RepeatableEntry::make('saleItems')
                        ->label('')
                        ->columns(['default' => 1, 'md' => 12])
                        ->schema([
                            TextEntry::make('product.name')
                                ->label('Producto')
                                ->columnSpan(['md' => 6])
                                ->placeholder('—'),

                            TextEntry::make('quantity')
                                ->label('Cant.')
                                ->columnSpan(['md' => 2]),

                            TextEntry::make('unit_price')
                                ->label('Precio unit.')
                                ->money('COP')
                                ->columnSpan(['md' => 2]),

                            TextEntry::make('subtotal')
                                ->label('Subtotal')
                                ->money('COP')
                                ->columnSpan(['md' => 2]),
                        ])
                        ->contained(false),
                ]),
        ]);
    }
}
