<?php

namespace App\Filament\Resources\ProductVariants\Pages;

use App\Filament\Resources\ProductVariants\ProductVariantResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateProductVariant extends CreateRecord
{
    protected static string $resource = ProductVariantResource::class;

    protected function beforeCreate(): void
    {
        $productId     = (int) ($this->data['product_id'] ?? 0);
        $sizeValueId   = (int) ($this->data['size_value_id'] ?? 0);
        $colorValueId  = (int) ($this->data['color_value_id'] ?? 0);

        if (!$productId || !$sizeValueId || !$colorValueId) {
            return;
        }

        // ✅ Anti-duplicado compatible con ONLY_FULL_GROUP_BY
        $exists = DB::table('product_variants as pv')
            ->join('variant_attribute_values as vav', 'vav.variant_id', '=', 'pv.id')
            ->where('pv.product_id', $productId)
            ->whereIn('vav.attribute_value_id', [$sizeValueId, $colorValueId])
            ->groupBy('pv.id')
            ->havingRaw('COUNT(DISTINCT vav.attribute_value_id) = 2')
            ->select('pv.id')   // 👈 CLAVE para evitar error SQL
            ->limit(1)
            ->exists();

        if ($exists) {
            $this->halt();
            $this->addError(
                'size_value_id',
                'Ya existe una variante con esa Talla y Color para este producto.'
            );
        }
    }

    protected function afterCreate(): void
    {
        $sizeValueId  = (int) ($this->data['size_value_id'] ?? 0);
        $colorValueId = (int) ($this->data['color_value_id'] ?? 0);

        if (!$sizeValueId || !$colorValueId) {
            return;
        }

        // Limpiar por seguridad
        DB::table('variant_attribute_values')
            ->where('variant_id', $this->record->id)
            ->delete();

        // Insertar pivote
        DB::table('variant_attribute_values')->insert([
            [
                'variant_id' => $this->record->id,
                'attribute_value_id' => $sizeValueId,
            ],
            [
                'variant_id' => $this->record->id,
                'attribute_value_id' => $colorValueId,
            ],
        ]);
    }
}