<?php

namespace App\Filament\Resources\AdminSalePayments\Pages;

use App\Filament\Resources\AdminSalePayments\AdminSalePaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminSalePayments extends ListRecords
{
    protected static string $resource = AdminSalePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
