<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si no está logueado o no tiene rol admin, aborta con 403
        if (!$user || !$user->hasRole('admin')) {
            abort(403, 'No tienes permisos para acceder al panel.');
        }

        return $next($request);
    }
}
