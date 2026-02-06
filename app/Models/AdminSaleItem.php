<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSaleItem extends Model
{
    use HasFactory;

    protected $table = 'admin_sale_items';

    protected $fillable = [
        'admin_sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function adminSale()
    {
        return $this->belongsTo(AdminSale::class, 'admin_sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
     protected static function booted(): void
    {
        // ✅ Siempre mantener subtotal correcto
        static::saving(function (self $item) {
            $qty  = (int) ($item->quantity ?? 0);
            $unit = (float) ($item->unit_price ?? 0);

            $item->subtotal = round($qty * $unit, 2);
        });

        // ✅ Cuando se guarda (create/update), refrescar total de la venta
        static::saved(function (self $item) {
            $item->adminSale?->refreshTotal();
        });

        // ✅ Cuando se elimina, refrescar total de la venta
        static::deleted(function (self $item) {
            $item->adminSale?->refreshTotal();
        });
    }
}
