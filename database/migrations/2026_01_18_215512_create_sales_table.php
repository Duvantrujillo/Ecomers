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
          Schema::create('sales', function (Blueprint $table) {
            $table->id(); // id INT PK auto incremental
            $table->dateTime('sale_date'); // fecha_venta
            $table->decimal('total', 10, 2); // total de la venta
            $table->string('customer_name', 255); // cliente_nombre
            $table->enum('status', ['pending', 'paid', 'shipped'])->default('pending'); // estado
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
