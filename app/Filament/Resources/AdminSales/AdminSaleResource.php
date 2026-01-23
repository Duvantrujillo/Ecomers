<?php

namespace App\Filament\Resources\AdminSales;

use App\Filament\Resources\AdminSales\Pages\CreateAdminSale;
use App\Filament\Resources\AdminSales\Pages\EditAdminSale;
use App\Filament\Resources\AdminSales\Pages\ListAdminSales;
use App\Filament\Resources\AdminSales\Pages\ViewAdminSale;
use App\Filament\Resources\AdminSales\Schemas\AdminSaleForm;
use App\Filament\Resources\AdminSales\Schemas\AdminSaleInfolist;
use App\Filament\Resources\AdminSales\Tables\AdminSalesTable;
use App\Models\AdminSale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdminSaleResource extends Resource
{
    protected static ?string $model = AdminSale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return AdminSaleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdminSaleInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdminSalesTable::configure($table);
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
            'index' => ListAdminSales::route('/'),
            'create' => CreateAdminSale::route('/create'),
            'view' => ViewAdminSale::route('/{record}'),
            'edit' => EditAdminSale::route('/{record}/edit'),
        ];
    }
}
