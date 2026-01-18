<?php

namespace App\Filament\Resources\InventoryMovements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InventoryMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Select::make('movement_type')
                    ->options(['entrada' => 'Entrada', 'salida' => 'Salida', 'ajuste' => 'Ajuste'])
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Textarea::make('comment')
                    ->columnSpanFull(),
                DateTimePicker::make('movement_date')
                    ->required(),
            ]);
    }
}
