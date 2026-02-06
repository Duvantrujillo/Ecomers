<?php

namespace App\Filament\Resources\AdminSales\Pages;

use App\Filament\Resources\AdminSales\AdminSaleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdminSale extends ViewRecord
{
    protected static string $resource = AdminSaleResource::class;

    //Este es el boton de editar

   /* protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }*/
}
