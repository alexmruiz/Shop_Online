<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // prioridad: session -> query param -> config default
        $locale = session('locale') ?? $request->query('lang') ?? config('app.locale');

        // validación simple
        if (! in_array($locale, ['es', 'en'])) {
            $locale = config('app.locale');
        }

        app()->setLocale($locale);
        return $next($request);
    }
}
