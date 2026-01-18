<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'active',
    ];

    // Relación con subcategorías
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relación con categoría padre
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
