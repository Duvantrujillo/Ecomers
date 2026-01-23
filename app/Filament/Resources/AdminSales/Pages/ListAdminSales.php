<?php

namespace App\Filament\Resources\AdminSales\Pages;

use App\Filament\Resources\AdminSales\AdminSaleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSales extends ListRecords
{
    protected static string $resource = AdminSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
