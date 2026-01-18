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
    Schema::create('categories', function (Blueprint $table) {
        $table->id(); // id INT PK auto incremental
        $table->string('name'); // nombre de la categoría
        $table->text('description')->nullable(); // descripción
        $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete(); // subcategorías
        $table->boolean('active')->default(true); // mostrar/ocultar categorías
        $table->timestamps(); // fecha_creacion y fecha_actualizacion automáticos
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
