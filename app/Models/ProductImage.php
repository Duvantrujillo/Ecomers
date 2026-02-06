<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'url',
        'alt_text',
        'order',
    ];

    // Relación con producto
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

   // ✅ accesor “seguro” para usar en vistas
    public function getImageUrlAttribute(): string
    {
        // si en DB guardas: products/archivo.png
        return asset('storage/' . ltrim($this->attributes['url'] ?? '', '/'));
        // (si prefieres: return Storage::url($this->attributes['url']);
    }
}
