<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('location')
                    ->placeholder('-'),
                TextEntry::make('minimum_alert')
                    ->numeric(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
