<?php

namespace App\Filament\Resources\AdminSalePayments;

use App\Filament\Resources\AdminSalePayments\Pages\CreateAdminSalePayment;
use App\Filament\Resources\AdminSalePayments\Pages\EditAdminSalePayment;
use App\Filament\Resources\AdminSalePayments\Pages\ListAdminSalePayments;
use App\Filament\Resources\AdminSalePayments\Pages\ViewAdminSalePayment;
use App\Filament\Resources\AdminSalePayments\Schemas\AdminSalePaymentForm;
use App\Filament\Resources\AdminSalePayments\Schemas\AdminSalePaymentInfolist;
use App\Filament\Resources\AdminSalePayments\Tables\AdminSalePaymentsTable;
use App\Models\AdminSalePayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSalePaymentResource extends Resource
{
    protected static ?string $model = AdminSalePayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'amount';

    public static function form(Schema $schema): Schema
    {
        return AdminSalePaymentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdminSalePaymentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSalePaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminSalePayments::route('/'),
            'create' => CreateAdminSalePayment::route('/create'),
            'view' => ViewAdminSalePayment::route('/{record}'),
            'edit' => EditAdminSalePayment::route('/{record}/edit'),
        ];
    }
}
