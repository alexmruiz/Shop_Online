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
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Comprobar si el usuario está autenticado y tiene el rol adecuado
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Si no tiene el rol, redirigir al inicio con un mensaje de error
        return redirect('/')->with('error', 'Acceso denegado');
    }
}
