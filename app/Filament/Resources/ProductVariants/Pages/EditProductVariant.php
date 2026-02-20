<?php

namespace App\Filament\Resources\ProductVariants\Pages;

use App\Filament\Resources\ProductVariants\ProductVariantResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditProductVariant extends EditRecord
{
    protected static string $resource = ProductVariantResource::class;

    /**
     * ✅ Aquí precargamos size_value_id y color_value_id desde la pivote,
     * para que al editar se vean seleccionados como antes.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // IDs de atributos (una vez por request)
        $tallaAttrId = (int) (DB::table('attributes')->where('name', 'Talla')->value('id') ?? 0);
        $colorAttrId = (int) (DB::table('attributes')->where('name', 'Color')->value('id') ?? 0);

        if ($tallaAttrId && $colorAttrId) {
            $pairs = DB::table('variant_attribute_values as vav')
                ->join('attribute_values as av', 'av.id', '=', 'vav.attribute_value_id')
                ->where('vav.variant_id', $this->record->id)
                ->whereIn('av.attribute_id', [$tallaAttrId, $colorAttrId])
                ->select('av.attribute_id', 'av.id as value_id')
                ->get();

            $data['size_value_id'] = (int) optional($pairs->firstWhere('attribute_id', $tallaAttrId))->value_id;
            $data['color_value_id'] = (int) optional($pairs->firstWhere('attribute_id', $colorAttrId))->value_id;
        }

        return $data;
    }

    protected function beforeSave(): void
    {
        $productId     = (int) ($this->data['product_id'] ?? 0);
        $sizeValueId   = (int) ($this->data['size_value_id'] ?? 0);
        $colorValueId  = (int) ($this->data['color_value_id'] ?? 0);

        if (!$productId || !$sizeValueId || !$colorValueId) {
            return;
        }

        $exists = DB::table('product_variants as pv')
            ->join('variant_attribute_values as vav', 'vav.variant_id', '=', 'pv.id')
            ->where('pv.product_id', $productId)
            ->whereIn('vav.attribute_value_id', [$sizeValueId, $colorValueId])
            ->where('pv.id', '!=', $this->record->id)
            ->groupBy('pv.id')
            ->havingRaw('COUNT(DISTINCT vav.attribute_value_id) = 2')
            ->select('pv.id')   // ✅ ONLY_FULL_GROUP_BY safe
            ->limit(1)
            ->exists();

        if ($exists) {
            $this->halt();
            $this->addError('size_value_id', 'Ya existe una variante con esa Talla y Color para este producto.');
        }
    }

    protected function afterSave(): void
    {
        $sizeValueId  = (int) ($this->data['size_value_id'] ?? 0);
        $colorValueId = (int) ($this->data['color_value_id'] ?? 0);

        if (!$sizeValueId || !$colorValueId) {
            return;
        }

        DB::table('variant_attribute_values')
            ->where('variant_id', $this->record->id)
            ->delete();

        DB::table('variant_attribute_values')->insert([
            ['variant_id' => $this->record->id, 'attribute_value_id' => $sizeValueId],
            ['variant_id' => $this->record->id, 'attribute_value_id' => $colorValueId],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}