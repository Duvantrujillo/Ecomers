<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'qr_reference',
        'active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->qr_reference)) {
                $product->qr_reference = 'QR-' . Str::uuid()->toString();
            }
        });
    }

    public function qrDataUri(): string
    {
        return Cache::remember(
            "product:qr:{$this->id}",
            now()->addDays(30),
            function () {
                $qr = new QrCode($this->qr_reference);

                $writer = new PngWriter();

                return $writer->write($qr)->getDataUri();
            }
        );
    }
}
