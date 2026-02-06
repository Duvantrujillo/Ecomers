<?php

namespace App\Filament\Resources\AdminSalePayments\Pages;

use App\Filament\Resources\AdminSalePayments\AdminSalePaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAdminSalePayment extends EditRecord
{
    protected static string $resource = AdminSalePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
