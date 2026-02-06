<?php

namespace App\Filament\Resources\AdminSales\RelationManagers;

use App\Filament\Resources\AdminSalePayments\Schemas\AdminSalePaymentForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return AdminSalePaymentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')->label('Fecha')->dateTime(),
                Tables\Columns\TextColumn::make('method')->label('Método'),
                Tables\Columns\TextColumn::make('type')->label('Tipo'),
                Tables\Columns\TextColumn::make('amount')->label('Monto')->money('COP'),
                Tables\Columns\TextColumn::make('status')->label('Estado'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
