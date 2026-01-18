<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class FilamentServiceProvider extends ServiceProvider
{


public function boot(): void
{
    Filament::serving(function () {
        Filament::auth(function ($request) {

            Log::info('Intento de login Filament', [
                'email' => $request->email,
                'password_present' => !empty($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::info('Usuario no existe', ['email' => $request->email]);
                return false;
            }

            if (!Hash::check($request->password, $user->password)) {
                Log::info('Contraseña incorrecta', ['email' => $request->email]);
                return false;
            }

            if (!$user->hasRole('admin')) {
                Log::info('Usuario no es admin', ['email' => $request->email, 'roles' => $user->roles->pluck('slug')]);
                return false;
            }

            Log::info('Login permitido', ['email' => $request->email, 'roles' => $user->roles->pluck('slug')]);

            auth()->guard(config('filament.auth.guard'))->login($user);

            return true;
        });
    });
}

}
