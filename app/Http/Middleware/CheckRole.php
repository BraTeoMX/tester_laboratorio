<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  array<int>  ...$roles // Aceptamos una lista de roles permitidos
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si el usuario no está logueado, el middleware 'auth' ya se encargó.
        // Si por alguna razón no tiene un rol, lo bloqueamos.
        if (!Auth::check() || !in_array(Auth::user()->role_id, $roles)) {
            
            // Si no tiene el rol permitido, abortamos la petición
            // con un error 403 (Prohibido).
            // Puedes personalizar la página de error 403 si lo deseas.
            abort(403, 'ACCESO NO AUTORIZADO.');
        }

        // Si el usuario tiene el rol correcto, dejamos que la petición continúe.
        return $next($request);
    }
}