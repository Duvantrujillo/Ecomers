<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'sale_date',
        'total',
        'customer_name',
        'status',
    ];

    // Definir los tipos de los campos
    protected $casts = [
        'sale_date' => 'datetime',
        'total' => 'decimal:2',
        'status' => 'string',
    ];
      public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
