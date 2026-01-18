<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si no está autenticado o no tiene rol 'admin'
        if (!$user || !$user->hasRole('admin')) {
            Auth::logout(); // opcional, fuerza logout
            abort(403, 'No tienes permisos para acceder al panel.');
        }

        return $next($request);
    }
}
