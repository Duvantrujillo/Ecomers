<?php

namespace App\Filament\Resources\AdminSalePayments\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AdminSalePaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('admin_sale_id'),

            Hidden::make('created_by')
                ->default(fn () => auth()->id()),

            Select::make('method')
                ->label('Método')
                ->options([
                    'cash' => 'Efectivo',
                    'transfer' => 'Transferencia',
                ])
                ->required()
                ->live(),

            Select::make('type')
                ->label('Tipo')
                ->options([
                    'payment' => 'Pago',
                    'refund' => 'Reembolso',
                ])
                ->default('payment')
                ->required()
                ->live(),

            TextInput::make('amount')
                ->label('Monto')
                ->numeric()
                ->minValue(0.01)
                ->required()
               ->rule(function ($livewire, Get $get) {
    return function (string $attribute, $value, \Closure $fail) use ($livewire, $get) {

        // 1) Obtener la venta (RelationManager o Resource suelto)
        $sale = method_exists($livewire, 'getOwnerRecord')
            ? $livewire->getOwnerRecord()
            : null;

        if (! $sale) {
            $saleId = $get('admin_sale_id');
            if ($saleId) {
                $sale = \App\Models\AdminSale::query()->find($saleId);
            }
        }

        if (! $sale) return;

        $amount = (float) $value;

        $status = $get('status') ?? 'approved';
        $type   = $get('type') ?? 'payment';

        // Solo bloqueamos cuando cuenta como dinero real
        if ($status !== 'approved') return;

        // 2) Usar total bloqueado si existe (mejor práctica)
        //    Si no tienes locked_total_amount, esto igual funciona usando total_amount
        $total = (float) ($sale->locked_total_amount ?? $sale->total_amount);

        // 3) Excluir el pago actual (si estás editando) para no contarlo 2 veces
        $currentPaymentId = null;

        if (method_exists($livewire, 'getRecord')) {
            $currentPaymentId = optional($livewire->getRecord())->id;
        } elseif (property_exists($livewire, 'record')) {
            $currentPaymentId = optional($livewire->record)->id;
        }

        $paidQuery = $sale->payments()->where('status', 'approved');

        if ($currentPaymentId) {
            $paidQuery->where('id', '!=', $currentPaymentId);
        }

        $paid = (float) $paidQuery->get()
            ->sum(fn ($p) => $p->type === 'refund' ? -$p->amount : $p->amount);

        // 4) Validaciones
        if ($type === 'payment') {
            $balance = $total - $paid;

            if ($amount > $balance + 0.0001) {
                $fail('Te estás pasando. Saldo pendiente: $' . number_format($balance, 2));
            }
        }

        if ($type === 'refund') {
            if ($amount > $paid + 0.0001) {
                $fail('No puedes reembolsar más de lo pagado. Pagado neto: $' . number_format($paid, 2));
            }
        }
    };
}),

          TextInput::make('currency')
    ->label('Moneda')
    ->default('COP')
    ->maxLength(3)
    ->required()
    ->disabled()
    ->dehydrated(true),


            Select::make('status')
                ->label('Estado')
                ->options([
                    'pending' => 'Pendiente',
                    'approved' => 'Aprobado',
                    'rejected' => 'Rechazado',
                    'cancelled' => 'Cancelado',
                ])
                ->default('approved')
                ->required()
                ->live(),

            TextInput::make('reference')
                ->label('Referencia')
                ->maxLength(120)
                ->visible(fn (Get $get) => $get('method') === 'transfer')
                ->required(fn (Get $get) => $get('method') === 'transfer'),

            FileUpload::make('receipt_path')
                ->label('Comprobante')
                ->disk('public')
                ->directory('admin-sale-receipts')
                ->visible(fn (Get $get) => $get('method') === 'transfer')
                ->required(fn (Get $get) => $get('method') === 'transfer'),

            DateTimePicker::make('paid_at')
                ->label('Fecha del pago')
                ->seconds(false)
                ->default(now()),

            Textarea::make('notes')
                ->label('Notas')
                ->columnSpanFull(),

           /* KeyValue::make('meta')
                ->label('Meta')
                ->columnSpanFull(),*/
        ]);
    }
}
