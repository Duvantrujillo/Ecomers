<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de ventas realizadas por administradores.
     * Esta tabla representa una venta completa (pedido),
     * independiente de cómo o cuándo se paga.
     */
    public function up(): void
    {
        Schema::create('admin_sales', function (Blueprint $table) {

            /**
             * Identificador principal
             */
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT

            /**
             * Número de orden visible para el negocio / cliente.
             * Único y legible (ej: ORD-20260205-0001)
             */
            $table->string('order_number')->unique();

            /**
             * Administrador que realizó la venta
             */
            $table->foreignId('admin_id')
                ->constrained('users')
                ->onDelete('cascade');

            /**
             * Fecha y hora en que se realizó la venta
             */
            $table->dateTime('sale_date');

            /**
             * Total de la venta (suma de los subtotales de los productos)
             */
            $table->decimal('total_amount', 12, 2)->default(0);

            /**
             * Estado LOGÍSTICO del pedido
             * Describe en qué punto del flujo se encuentra la venta
             */
            $table->enum('status', [
                'pending',     // Venta creada, aún no procesada
                'processing',  // En preparación (empaque / despacho)
                'shipped',     // Enviada
                'delivered',   // Entregada al cliente
                'returned',    // Devuelta total o parcialmente
                'cancelled',   // Cancelada antes de completarse
            ])->default('pending');

            /**
             * Estado del PAGO (dinero)
             * Separado del estado logístico para permitir pagos parciales,
             * efectivo, transferencias, reembolsos, etc.
             */
            $table->enum('payment_status', [
                'unpaid',    // No se ha recibido dinero
                'partial',   // Pago parcial recibido
                'paid',      // Pago completo
                'failed',    // Pago fallido
                'refunded',  // Dinero devuelto total o parcialmente
            ])->default('unpaid');

            /**
             * Total efectivamente pagado por el cliente
             * Se recalcula a partir de los registros de pagos
             */
            $table->decimal('paid_amount', 12, 2)->default(0);

            /**
             * Moneda de la venta (ISO 4217)
             * Ej: COP, USD, EUR
             */
            $table->char('currency', 3)->default('COP');

            /**
             * Datos básicos del cliente (venta por mostrador)
             */
            $table->string('customer_name')->nullable();

            /**
             * Observaciones internas de la venta
             */
            $table->text('notes')->nullable();

            /**
             * Timestamps estándar
             */
            $table->timestamps();

            /**
             * Índices para búsquedas y filtros frecuentes
             */
            $table->index('sale_date');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    /**
     * Revierte la migración
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_sales');
    }
};
