<?php

namespace App\Filament\Resources\AdminSalePayments\Tables;

use App\Filament\Resources\AdminSales\AdminSaleResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdminSalePaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ✅ A qué venta pertenece (y clic para abrirla)
                TextColumn::make('adminSale.order_number')
                    ->label('Venta')
                    ->sortable()
                    ->searchable()
                    ->url(fn ($record) => AdminSaleResource::getUrl('view', ['record' => $record->adminSale]))
                    ->openUrlInNewTab(),

                // ✅ Cliente (si lo tienes en admin_sales)
                TextColumn::make('adminSale.customer_name')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('method')->label('Método')->badge(),
                TextColumn::make('type')->label('Tipo')->badge(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('COP')
                    ->sortable(),

                TextColumn::make('status')->label('Estado')->badge(),

                // ✅ Referencia solo aplica para transferencias (Nequi, etc.)
                TextColumn::make('reference')
                    ->label('Referencia')
                    ->searchable()
                    ->toggleable(),

                // ✅ Quién registró el pago (nombre)
                TextColumn::make('creator.name')
                    ->label('Registrado por')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('paid_at')->label('Fecha pago')->dateTime()->sortable(),
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
