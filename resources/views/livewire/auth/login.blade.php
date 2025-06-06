<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    // We'll rename 'email' to 'credential' to be more generic
    #[Validate('required|string')]
    public string $credential = ''; 

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        // Determine if the credential is an email or employee_number
        $fieldType = filter_var($this->credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'employee_number';

        if (! Auth::attempt([$fieldType => $this->credential, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'credential' => __('auth.failed'), // Change 'email' to 'credential' for the error message
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // 1. Obtenemos el usuario que acaba de iniciar sesión.
        $user = Auth::user();

        // 2. Usamos 'match' para determinar la ruta de destino según el role_id.
        $routeName = match ((int)$user->role_id) {
            1, 2, 3, 4 => 'dashboard',
            5           => 'vistaAuditor',
            default     => 'dashboard', // Una ruta por defecto como fallback
        };

        // 3. Redirigimos al usuario a la ruta determinada.
        // Usamos navigate: true para una transición SPA-like.
        $this->redirect(route($routeName), navigate: true);
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
            'credential' => __('auth.throttle', [ // Change 'email' to 'credential' for the error message
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
        // Use the generic 'credential' for the throttle key
        return Str::transliterate(Str::lower($this->credential).'|'.request()->ip());
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Accede con tu cuenta')" :description="__('ingresa tu numero de empleado o correo asociado')" />

    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <flux:input
            wire:model="credential"
            :label="__('Numero de empleado o correo electrónico')"
            type="text" {{-- Change type to text since it can be either email or employee_number --}}
            required
            autofocus
            autocomplete="username" {{-- Changed to 'username' as it's a generic credential --}}
            placeholder="empleado@empresa.com o 18080" {{-- Updated placeholder --}}
        />

        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Contraseña')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Contraseña')"
                viewable
            />
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Iniciar sesion') }}</flux:button>
        </div>
    </form>
</div>