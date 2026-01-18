<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Marca el email como verificado
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Si ya está verificado
        if ($user->hasVerifiedEmail()) {
            return $this->redirectUser($user);
        }

        // Marcar email como verificado
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectUser($user);
    }

    /**
     * Redirige según el tipo de usuario
     */
  protected function redirectUser($user): RedirectResponse
{
    // Verificar que tenga algún rol asignado
    if ($user->roles->isEmpty()) {
        abort(403, 'No tienes rol asignado.');
    }

    // Solo clientes pueden acceder
    if (!$user->hasRole('customer')) {
        abort(403, 'No tienes permiso para acceder.');
    }

    // Cliente válido → redirigir al dashboard
    return redirect()->route('dashboard', ['verified' => 1]);
}

}
