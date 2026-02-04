<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('admin_sale_id')
                ->constrained('admin_sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            // Para consultas rápidas
            $table->index(['admin_sale_id', 'product_id']);

            // (Recomendado) Evita el mismo producto repetido en la misma venta
            // Descomenta si quieres esta regla:
            // $table->unique(['admin_sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sale_items');
    }
};
