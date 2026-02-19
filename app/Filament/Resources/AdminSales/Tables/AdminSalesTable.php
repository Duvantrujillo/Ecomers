<?php

namespace App\Filament\Resources\AdminSales\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->defaultSort('id', 'desc') 
            ->columns([
                TextColumn::make('admin.name')
                    ->label('Admin')
                    ->sortable(),

                TextColumn::make('sale_date')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->sortable(),

               TextColumn::make('status')
    ->label('Estado pedido')
    ->badge()
    ->color(fn ($record) => match ($record->status) {
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'info',
        'delivered' => 'success',
        'returned' => 'gray',
        'cancelled' => 'danger',
        default => 'gray',
    })
    ->formatStateUsing(fn (string $state) => match ($state) {
        'pending' => 'Pendiente',
        'processing' => 'En preparación',
        'shipped' => 'Enviada',
        'delivered' => 'Entregada',
        'returned' => 'Devuelta',
        'cancelled' => 'Cancelada',
        default => $state,
    }),

                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('saleItems_count')
                    ->counts('saleItems')
                    ->label('# Productos')
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
                Action::make('products')
                    ->label('Ver productos')
                    ->icon('heroicon-o-shopping-bag')
                    ->modalWidth('7xl')
                    ->modalHeading(fn($record) => "Productos de la venta #{$record->id}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->action(fn() => null)
                    ->modalContent(function ($record) {
                        $sale = $record->loadMissing([
                            'saleItems' => fn($q) => $q
                                ->select(['id', 'admin_sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal'])
                                ->with([
                                    'product:id,name',
                                    // ✅ cargar SOLO 1 imagen por producto (la primera por order)
                                    'product.images' => fn($q) => $q
                                        ->select(['id', 'product_id', 'url', 'alt_text', 'order'])
                                        ->orderBy('order')
                                        ->limit(1),
                                ])
                                ->orderByDesc('id'),
                        ]);

                        if ($sale->saleItems->isEmpty()) {
                            return view('filament.AdminSales.modals.products-empty');
                        }

                        return view('filament.AdminSales.modals.products', [
                            'sale' => $sale,
                        ]);
                    }),


                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                   // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
