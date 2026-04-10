<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TrackNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $currentRoute = $request->route()->getName();
        
        // Rutas que NO queremos guardar en el historial
        $excludeRoutes = [
            'login', 
            'login.procesar',
            'logout', 
            'lang.switch',
            'reset.navigation',
            'respaldo.generar',
            'respaldo.automatico.form',
            'respaldo.automatico.store',
            'respaldo.descargar',
            'register',
            'registro.procesar'
        ];
        
        // Rutas principales (dashboards) - NO se guardan
        $dashboardRoutes = [
            'admin.dashboard',
            'docente.dashboard', 
            'alumno.dashboard'
        ];
        
        // Si es ruta excluida o dashboard, NO guardar
        if (in_array($currentRoute, $excludeRoutes) || in_array($currentRoute, $dashboardRoutes)) {
            return $next($request);
        }
        
        if ($currentRoute) {
            $navigationHistory = Session::get('navigation_history', []);
            
            // Si la ruta ya existe en el historial, eliminarla (para moverla al final)
            $existingIndex = array_search($currentRoute, $navigationHistory);
            if ($existingIndex !== false) {
                unset($navigationHistory[$existingIndex]);
                $navigationHistory = array_values($navigationHistory); // Reindexar
            }
            
            // Agregar la ruta al final
            $navigationHistory[] = $currentRoute;
            
            // Limitar el historial a máximo 8 niveles
            if (count($navigationHistory) > 8) {
                array_shift($navigationHistory);
            }
            
            Session::put('navigation_history', $navigationHistory);
        }
        
        return $next($request);
    }
}