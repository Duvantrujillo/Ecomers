<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
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

    // Relaciones

    // Relación con venta normal de cliente
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación con venta de administrador
    public function adminSale()
    {
        return $this->belongsTo(AdminSale::class);
    }

    // Relación con producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Booted para lógica al crear/eliminar
    protected static function booted()
    {
        // Cuando se crea un item, restamos inventario y registramos movimiento
        static::created(function ($saleItem) {
            DB::transaction(function () use ($saleItem) {
                $inventory = \App\Models\Inventory::where('product_id', $saleItem->product_id)->first();
                if ($inventory) {
                    $inventory->decrement('quantity', $saleItem->quantity);
                }

                \App\Models\InventoryMovement::create([
                    'product_id' => $saleItem->product_id,
                    'quantity' => $saleItem->quantity,
                    'movement_type' => 'salida',
                    'comment' => $saleItem->sale_id
                        ? "Venta Cliente ID: {$saleItem->sale_id}"
                        : "Venta Admin ID: {$saleItem->admin_sale_id}",
                    'movement_date' => now(),
                ]);
            });
        });

        // Opcional: si eliminas un item, devolver inventario
        static::deleted(function ($saleItem) {
            DB::transaction(function () use ($saleItem) {
                $inventory = \App\Models\Inventory::where('product_id', $saleItem->product_id)->first();
                if ($inventory) {
                    $inventory->increment('quantity', $saleItem->quantity);
                }

                \App\Models\InventoryMovement::create([
                    'product_id' => $saleItem->product_id,
                    'quantity' => $saleItem->quantity,
                    'movement_type' => 'entrada',
                    'comment' => $saleItem->sale_id
                        ? "Reversión Venta Cliente ID: {$saleItem->sale_id}"
                        : "Reversión Venta Admin ID: {$saleItem->admin_sale_id}",
                    'movement_date' => now(),
                ]);
            });
        });
    }
}
