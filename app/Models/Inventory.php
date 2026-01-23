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
    
    protected static function booted()
{
    static::updated(function ($inventory) {
        if ($inventory->isDirty('quantity')) {

            $original = $inventory->getOriginal('quantity');
            $actual = $inventory->quantity;
            $diferencia = $actual - $original;

            if ($diferencia === 0) {
                return;
            }

            InventoryMovement::create([
                'product_id' => $inventory->product_id,
                'movement_type' => $diferencia > 0 ? 'entrada' : 'salida',
                'quantity' => abs($diferencia),
                'comment' => 'Ajuste manual de inventario',
                'movement_date' => now(),
            ]);
        }
    });
}

}
