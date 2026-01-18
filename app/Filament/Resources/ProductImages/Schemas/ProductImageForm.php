<?php

namespace App\Filament\Resources\ProductImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),

              FileUpload::make('url')
    ->image()
    ->disk('public')
    ->directory('products')
    ->imagePreviewHeight(150)
    ->required(),


                TextInput::make('alt_text')
                    ->maxLength(255),

                TextInput::make('order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
