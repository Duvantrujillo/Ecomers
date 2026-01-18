<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    public $timestamps = false; // desactivamos created_at y updated_at automáticos

    protected $fillable = [
        'product_id',
        'quantity',
        'location',
        'minimum_alert',
        'updated_at',
    ];

    // Relación con producto
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
