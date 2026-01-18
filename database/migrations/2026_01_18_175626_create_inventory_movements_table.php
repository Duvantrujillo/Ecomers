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
    Schema::create('inventory_movements', function (Blueprint $table) {
        $table->id(); // Auto-increment primary key
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete(); // FK to products
        $table->enum('movement_type', ['entrada', 'salida', 'ajuste']); // movement type
        $table->integer('quantity'); // positive for entry, negative for exit
        $table->text('comment')->nullable(); // optional comment
        $table->timestamp('movement_date')->useCurrent(); // when it happened
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
