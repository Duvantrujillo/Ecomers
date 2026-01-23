<?php

namespace App\Filament\Resources\InventoryMovements\Tables;

use App\Models\InventoryMovement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 🧾 Producto
                TextColumn::make('product.name')
                    ->label('Producto')
                    ->searchable(
                        query: fn (Builder $query, string $search) =>
                            $query->whereHas('product', fn ($q) =>
                                $q->where('name', 'like', "%{$search}%")
                            )
                    )
                    ->sortable(
                        query: fn (Builder $query, string $direction) =>
                            $query->join('products', 'inventory_movements.product_id', '=', 'products.id')
                                  ->orderBy('products.name', $direction)
                    )
                    ->weight('bold'),

                // 🔄 Tipo de movimiento
                TextColumn::make('movement_type')
                    ->label('Movimiento')
                    ->badge()
                    ->formatStateUsing(fn (string $state) =>
                        match ($state) {
                            'entrada' => 'Entrada',
                            'salida'  => 'Salida',
                            default   => ucfirst($state),
                        }
                    )
                    ->colors([
                        'success' => 'entrada',
                        'danger'  => 'salida',
                    ])
                    ->icons([
                        'heroicon-o-arrow-down-circle' => 'entrada',
                        'heroicon-o-arrow-up-circle'   => 'salida',
                    ]),

                // 🔢 Cantidad
                TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->movement_type === 'salida' ? '- ' : '+ ') . $state
                    )
                    ->color(fn ($record) =>
                        $record->movement_type === 'salida' ? 'danger' : 'success'
                    ),

                // 📝 Comentario
                TextColumn::make('comment')
                    ->label('Detalle')
                    ->wrap()
                    ->limit(40)
                    ->toggleable(),

                // 📅 Fecha
                TextColumn::make('movement_date')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc') // Últimos registros primero
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

    /**
     * Query base de la tabla
     * - Precarga la relación product para evitar N+1
     * - Ordena por ID descendente (últimos primero)
     */
    public static function getEloquentQuery(): Builder
    {
        return InventoryMovement::query()
            ->with('product')   // Precarga relación product
            ->orderBy('id', 'desc'); // Últimos registros primero
    }
}
