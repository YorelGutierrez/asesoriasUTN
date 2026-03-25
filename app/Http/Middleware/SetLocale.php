<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Revisa si la "llave" 'locale' existe en la sesión
        if (session()->has('locale')) {
            
            // 2. Si existe, le dice a Laravel que use ese idioma
            //    (ej. 'en' o 'es') para esta carga de página.
            App::setLocale(session()->get('locale'));
        }

        // 3. Deja que la página continúe cargando
        return $next($request);
    }
}