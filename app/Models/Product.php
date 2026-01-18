<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'active',
    ];

    // Relación con categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
