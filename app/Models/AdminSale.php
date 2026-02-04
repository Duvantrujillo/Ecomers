<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSale extends Model
{
    use HasFactory;

    protected $table = 'admin_sales';

    protected $fillable = [
        'admin_id',
        'sale_date',
        'total_amount',
        'status',
        'customer_name',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Relación con usuario que hizo la venta
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relación con los productos de la venta (sale_items)
    

        // Relación con los items de la venta
   public function saleItems()
{
    return $this->hasMany(\App\Models\AdminSaleItem::class, 'admin_sale_id');
}
/*
 protected static function booted()
    {
        static::creating(function ($sale) {
            // Sumar los subtotales de los productos antes de guardar
            $sale->total_amount = $sale->saleItems->sum(fn($i) => $i->subtotal);
        });
    }*/

}
