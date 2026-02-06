<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_sale_payments', function (Blueprint $table) {
            $table->id();

            // Relación con la venta
            $table->foreignId('admin_sale_id')
                ->constrained('admin_sales')
                ->cascadeOnDelete();

            /**
             * Método de pago:
             * - cash: efectivo
             * - transfer: transferencia (Nequi/Daviplata/Banco/etc)
             */
            $table->enum('method', ['cash', 'transfer']);

            /**
             * Tipo de movimiento:
             * - payment: entra dinero
             * - refund: sale dinero (reembolso)
             */
            $table->enum('type', ['payment', 'refund'])->default('payment');

            /**
             * Monto SIEMPRE positivo.
             * (El tipo define si suma o resta.)
             */
            $table->decimal('amount', 12, 2);

            $table->char('currency', 3)->default('COP');

            /**
             * Estado del pago:
             * - pending: registrado pero sin validar (común en transfer)
             * - approved: válido
             * - rejected: inválido
             * - cancelled: anulado
             */
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])
                ->default('approved');

            /**
             * Transferencia:
             * reference: número/ID de transacción
             * receipt_path: foto/pdf comprobante en storage
             */
            $table->string('reference', 120)->nullable();
            $table->string('receipt_path')->nullable();

            // Auditoría
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Fecha efectiva (cuándo se recibió/devolvió)
            $table->timestamp('paid_at')->nullable();

            $table->text('notes')->nullable();

            // Meta flexible (por si luego integras gateway o guardas info extra)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Índices (rendimiento)
            $table->index(['admin_sale_id', 'status']);
            $table->index(['admin_sale_id', 'method']);
            $table->index(['admin_sale_id', 'type']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_sale_payments');
    }
};
