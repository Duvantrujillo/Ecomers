<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')
                    ->label('Product'),
                TextEntry::make('movement_type')
                    ->badge(),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('comment')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('movement_date')
                    ->dateTime(),
            ]);
    }
}
