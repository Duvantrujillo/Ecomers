<?php

namespace App\Filament\Resources\AdminSalePayments\Pages;

use App\Filament\Resources\AdminSalePayments\AdminSalePaymentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminSalePayment extends ViewRecord
{
    protected static string $resource = AdminSalePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
