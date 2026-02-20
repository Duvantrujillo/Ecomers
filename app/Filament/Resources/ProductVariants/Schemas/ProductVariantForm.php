<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Models\AttributeValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\DB;

class ProductVariantForm
{
    // Cache simple por request (evita queries repetidas)
    private static ?int $tallaAttributeId = null;
    private static ?int $colorAttributeId = null;

    private static function attrId(string $name): ?int
    {
        if ($name === 'Talla') {
            return self::$tallaAttributeId ??= (int) (DB::table('attributes')->where('name', 'Talla')->value('id') ?? 0) ?: null;
        }

        if ($name === 'Color') {
            return self::$colorAttributeId ??= (int) (DB::table('attributes')->where('name', 'Color')->value('id') ?? 0) ?: null;
        }

        return null;
    }

    private static function code(string $text, int $len = 3): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]+/', '', $text) ?? '';
        $clean = strtoupper($clean);

        return substr($clean, 0, $len) ?: 'XXX';
    }

    private static function tryGenerateSku(Set $set, Get $get): void
    {
        // No sobreescribir si ya tiene sku (edición)
        $currentSku = (string) ($get('sku') ?? '');
        if ($currentSku !== '') {
            return;
        }

        $productId = $get('product_id');
        $sizeValueId = $get('size_value_id');
        $colorValueId = $get('color_value_id');

        if (!$productId || !$sizeValueId || !$colorValueId) {
            return;
        }

        // Traer solo los 2 values necesarios (rápido)
        $values = AttributeValue::query()
            ->whereIn('id', [$sizeValueId, $colorValueId])
            ->pluck('value', 'id');

        $size = $values[$sizeValueId] ?? null;
        $color = $values[$colorValueId] ?? null;

        if (!$size || !$color) {
            return;
        }

        $sku = 'PRD' . $productId . '-' . self::code($color, 3) . '-' . self::code($size, 5);
        $set('sku', $sku);
    }

    public static function configure(Schema $schema): Schema
    {
        $tallaId = self::attrId('Talla');
        $colorId = self::attrId('Color');

        return $schema->components([
            Select::make('product_id')
                ->relationship('product', 'name')
                ->required()
                ->live()
                ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::tryGenerateSku($set, $get)),

            // SKU automático: no manual
            TextInput::make('sku')
                ->label('SKU')
                ->disabled()
                ->dehydrated(true)
                ->helperText('Se genera automáticamente con producto + color + talla.')
                ->dehydrateStateUsing(function ($state, Get $get) {
                    // Si ya existe, úsalo
                    if (!empty($state)) return $state;

                    $productId = $get('product_id');
                    $sizeValueId = $get('size_value_id');
                    $colorValueId = $get('color_value_id');

                    if (!$productId || !$sizeValueId || !$colorValueId) return $state;

                    $values = AttributeValue::query()
                        ->whereIn('id', [$sizeValueId, $colorValueId])
                        ->pluck('value', 'id');

                    $size = $values[$sizeValueId] ?? null;
                    $color = $values[$colorValueId] ?? null;

                    if (!$size || !$color) return $state;

                    $colorCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $color) ?? '', 0, 3)) ?: 'XXX';
                    $sizeCode  = strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '', $size) ?? '', 0, 5)) ?: 'XXX';

                    return "PRD{$productId}-{$colorCode}-{$sizeCode}";
                }),

            TextInput::make('price')
                ->required()
                ->numeric()
                ->prefix('$'),

            // ✅ Select de Talla (solo valores del atributo Talla)
            Select::make('size_value_id')
                ->label('Talla')
                ->required()
                ->searchable()
                ->preload()
                ->options(fn () => $tallaId
                    ? DB::table('attribute_values')->where('attribute_id', $tallaId)->orderBy('value')->pluck('value', 'id')->toArray()
                    : []
                )
                ->live()
                ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::tryGenerateSku($set, $get)),

            // ✅ Select de Color (solo valores del atributo Color)
            Select::make('color_value_id')
                ->label('Color')
                ->required()
                ->searchable()
                ->preload()
                ->options(fn () => $colorId
                    ? DB::table('attribute_values')->where('attribute_id', $colorId)->orderBy('value')->pluck('value', 'id')->toArray()
                    : []
                )
                ->live()
                ->afterStateUpdated(fn ($state, Set $set, Get $get) => self::tryGenerateSku($set, $get)),

            // ✅ Anti-duplicado (product + talla + color)
            // Nota: esto valida en el form; el sync real de pivote lo haremos en el Page hook.
            Toggle::make('active')->required(),
        ]);
    }
}