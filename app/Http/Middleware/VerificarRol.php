<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarRol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles  // Lista de roles permitidos
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Obtener el rol del usuario autenticado
        $userRole = Auth::user()->rol;

        // 3. Verificar si el rol está en la lista de permitidos
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // 4. Si no tiene permiso
        abort(403, 'No tienes permisos para acceder a esta página.');
    }
}
