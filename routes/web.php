<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsNotAdmin;
use Illuminate\Support\Facades\Auth;

Route::view('/', 'home');
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


require __DIR__.'/auth.php';
