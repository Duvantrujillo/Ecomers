<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
  public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $user = \App\Models\User::where('email', $this->email)->first();

    if (!$user) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'form.email' => trans('auth.failed'),
        ]);
    }

    // Verificar contraseña
    if (!\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'form.email' => trans('auth.failed'),
        ]);
    }

    // Verificar rol de administrador
    if (!$user->hasRole('admin')) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'form.email' => 'No tienes permisos para acceder al panel.',
        ]);
    }

    // Login exitoso
    \Illuminate\Support\Facades\Auth::guard(config('filament.auth.guard'))->login($user);

    RateLimiter::clear($this->throttleKey());
}

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
