<?php

namespace App\Filament\Resources\SaleItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SaleItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Muestra el nombre del cliente de la venta
                TextColumn::make('sale.customer_name')
                    ->label('Sale')
                    ->sortable(),

                // Muestra el nombre del producto
                TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('unit_price')
                    ->money('usd') // O tu moneda
                    ->sortable(),

                TextColumn::make('subtotal')
                    ->money('usd')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
