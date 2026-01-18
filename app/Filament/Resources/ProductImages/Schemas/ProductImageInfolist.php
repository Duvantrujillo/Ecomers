<?php

namespace App\Filament\Resources\ProductImages\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductImageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')
                    ->label('Product'),

                ImageEntry::make('url')
    ->height(200),         // altura de la imagen en el view

                TextEntry::make('alt_text')
                    ->placeholder('-'),

                TextEntry::make('order')
                    ->numeric(),

                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
