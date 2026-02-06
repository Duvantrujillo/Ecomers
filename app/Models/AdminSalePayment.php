<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSalePayment extends Model
{
    use HasFactory;

    protected $table = 'admin_sale_payments';

    protected $fillable = [
        'admin_sale_id',
        'method',
        'type',
        'amount',
        'currency',
        'status',
        'reference',
        'receipt_path',
        'created_by',
        'paid_at',
        'notes',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    public function sale()
    {
        return $this->belongsTo(AdminSale::class, 'admin_sale_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope para pagos que afectan el "paid_amount":
     * - approved
     * - payment suma
     * - refund resta (por type)
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }


    protected static function booted(): void
{
    static::saved(function (self $payment) {
        $payment->sale?->refreshPaymentTotals();
    });

    static::deleted(function (self $payment) {
        $payment->sale?->refreshPaymentTotals();
    });
    
}public function adminSale()
{
    return $this->belongsTo(\App\Models\AdminSale::class, 'admin_sale_id');
}


}
