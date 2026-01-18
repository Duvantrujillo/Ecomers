<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    public $timestamps = false; // No created_at ni updated_at automáticos

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'comment',
        'movement_date',
    ];

    // Relación con producto
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
