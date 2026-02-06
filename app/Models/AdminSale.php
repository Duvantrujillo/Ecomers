<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    public function refreshTotal(): void
    {
        $total = (float) $this->saleItems()->sum('subtotal');

        $this->forceFill([
            'total_amount' => $total,
        ])->saveQuietly();
    }
    public function payments()
    {
        return $this->hasMany(\App\Models\AdminSalePayment::class, 'admin_sale_id');
    }
    public function refreshPaymentTotals(): void
    {
        // Sumamos pagos aprobados (payment suma, refund resta)
        $approved = $this->payments()->where('status', 'approved');

        $paid = (float) $approved->get()->sum(function ($p) {
            $amount = (float) $p->amount;
            return $p->type === 'refund' ? -$amount : $amount;
        });

        // Nunca dejamos negativo por seguridad
        $paid = max(0, $paid);

        $total = (float) ($this->total_amount ?? 0);

        $paymentStatus = 'unpaid';
        if ($paid <= 0) {
            $paymentStatus = 'unpaid';
        } elseif ($total > 0 && $paid < $total) {
            $paymentStatus = 'partial';
        } elseif ($total > 0 && $paid >= $total) {
            $paymentStatus = 'paid';
        }

        // Si hay reembolso total (pagado llegó a 0 pero hubo refunds),
        // esto lo puedes ajustar luego con una regla más fina.
        // Por ahora mantenemos la lógica simple y consistente.

        $this->forceFill([
            'paid_amount' => $paid,
            'payment_status' => $paymentStatus,
        ])->saveQuietly();
    }
    protected static function booted(): void
    {
        static::creating(function ($sale) {
            if (empty($sale->order_number)) {
                $sale->order_number = 'ORD-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));
            }
        });
    }
    
}
