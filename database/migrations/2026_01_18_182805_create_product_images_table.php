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
    Schema::create('product_images', function (Blueprint $table) {
        $table->id(); // id INT PK auto incremental
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // FK a productos
        $table->string('url'); // URL de la imagen
        $table->string('alt_text')->nullable(); // texto alternativo opcional
        $table->integer('order')->default(0); // orden de visualización
        $table->timestamps(); // fecha_creacion y fecha_actualizacion
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
