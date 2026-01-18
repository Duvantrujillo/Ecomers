<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('inventories', function (Blueprint $table) {
        $table->id(); // id INT PK auto incremental
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // FK a products
        $table->integer('quantity')->default(0); // cantidad disponible
        $table->string('location')->nullable(); // ubicación opcional
        $table->integer('minimum_alert')->default(0); // stock mínimo para alerta
        $table->timestamp('updated_at')->useCurrent(); // fecha de última actualización
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
