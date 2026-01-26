<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Support\HtmlString;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),

                TextColumn::make('price')->money()->sortable(),

                TextColumn::make('category.name')->searchable(),

                IconColumn::make('active')->boolean(),

                TextColumn::make('qr_reference')
                    ->label('QR Ref')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])

            // BOTÓN AL LADO DEL BUSCADOR
            ->headerActions([
                Action::make('pdf_all_qrs')
                    ->label('PDF QRs')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(route('admin.products.qr.all.pdf'), shouldOpenInNewTab: true),
            ])

            ->recordActions([
              Action::make('qr')
    ->label('QR')
    ->icon('heroicon-o-qr-code')
    ->modalHeading(fn ($record) => $record->name)
    ->modalWidth('sm')
    ->modalAlignment('center')
    ->modalSubmitAction(false)
    ->modalActions([
  
        // Botón PDF (derecha)
        Action::make('downloadPdf')
            ->label('PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->url(fn ($record) => route('admin.products.qr.pdf', $record), shouldOpenInNewTab: true),

                  // Botón Cerrar (izquierda)
        Action::make('closeModal')
            ->label('Cerrar')
            ->color('gray')
            ->close(),

    ])
    ->modalContent(fn ($record) => new HtmlString("
        <div class='flex flex-col items-center justify-center p-2'>
            <img
                src='{$record->qrDataUri()}'
                alt='QR {$record->qr_reference}'
                class='w-64 h-64'
            />
             <!--
            <div class='mt-2 text-[11px] text-gray-500 font-mono text-center break-all'>
                {$record->qr_reference}
            </div>-->
        </div>
    ")),


                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
