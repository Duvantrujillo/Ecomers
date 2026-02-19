<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsNotAdmin;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProductQrPdfController;
use App\Http\Controllers\Store\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

//Route::view('/', 'home');
Route::view('/prueba2', 'products');

Route::get('/logout', function () {
    Auth::logout(); // Cierra la sesión del usuario
})->name('logout');

// Rutas protegidas para usuarios normales
Route::middleware(['auth', 'verified', EnsureUserIsNotAdmin::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});


// ✅ Rutas para PDF QRs (solo usuarios autenticados)
// No toca tus rutas existentes, solo agrega estas.
Route::middleware(['auth'])->group(function () {
     // ✅ PDF MASIVO (ruta sin choque con Filament Resource)
    Route::get('/admin/product-qrs.pdf', [ProductQrPdfController::class, 'all'])
        ->name('admin.products.qr.all.pdf');

    // ✅ PDF por producto (el que ya te funcionó)
    Route::get('/admin/products/{product}/qr.pdf', [ProductQrPdfController::class, 'single'])
        ->name('admin.products.qr.pdf');
});

require __DIR__.'/auth.php';
