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
    Schema::create('products', function (Blueprint $table) {
        $table->id(); // id INT PK auto incremental
        $table->string('name'); // nombre del producto
        $table->text('description')->nullable(); // descripción larga
        $table->decimal('price', 10, 2); // precio, 10 dígitos totales, 2 decimales
        $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete(); // FK a categories
        $table->boolean('active')->default(true); // mostrar/ocultar producto
        $table->timestamps(); // created_at y updated_at automáticos
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
