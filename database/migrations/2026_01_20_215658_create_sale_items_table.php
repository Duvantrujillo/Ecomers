<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id(); // id INT PK auto incremental

            // FK opcional a la venta normal de clientes
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');

            // FK opcional a la venta de administrador
            $table->foreignId('admin_sale_id')->nullable()->constrained('admin_sales')->onDelete('cascade');

            $table->foreignId('product_id')->constrained('products')->onDelete('restrict'); // producto_id -> FK a products.id

            $table->integer('quantity'); // cantidad
            $table->decimal('unit_price', 10, 2); // precio unitario
            $table->decimal('subtotal', 10, 2); // subtotal = quantity * unit_price

            $table->timestamps();

            // Índices para consultas rápidas
            $table->index('sale_id');
            $table->index('admin_sale_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
