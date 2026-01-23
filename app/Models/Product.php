<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'qr_reference',
        'active',
    ];

    // Relación con categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

       // Evento para generar un QR único
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->qr_reference)) {
                $product->qr_reference = 'QR-' . Str::uuid()->toString();
            }
        });
    }
}
