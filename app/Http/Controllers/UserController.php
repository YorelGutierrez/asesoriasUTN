<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['docente', 'alumno'])->get();
        return view('admin.rolesPermisos', compact('usuarios'));
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return back()->with('error', 'No puedes bloquear tu propia cuenta.');
        }
        $user->estado = !$user->estado;
        $user->save();

        $action = $user->estado ? 'desbloqueada' : 'bloqueada';
        registrar_log('BLOQUEO', "Cuenta {$action} - Usuario: {$user->email}", 'seguridad');
        return back()->with('success', "Cuenta {$action} correctamente.");
    }

    /**
     * Muestra la vista de roles y permisos con filtros y datos reales.
     */
    public function rolesPermisos(Request $request)
    {
        $query = User::with(['docente', 'alumno']);

        // Filtro por rol
        if ($request->filled('rol') && $request->rol !== 'todos') {
            $query->where('rol', $request->rol);
        }

        // Filtro por carrera (busca en alumno.carrera o docente.carrera)
        if ($request->filled('carrera') && $request->carrera !== 'todos') {
            $query->where(function ($q) use ($request) {
                $q->whereHas('alumno.carrera', function ($sub) use ($request) {
                    $sub->where('nombre', 'like', '%' . $request->carrera . '%');
                })->orWhereHas('docente.carrera', function ($sub) use ($request) {
                    $sub->where('nombre', 'like', '%' . $request->carrera . '%');
                });
            });
        }

        // Búsqueda por nombre o email
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Obtener usuarios con paginación (10 por página)
        $usuarios = $query->paginate(10);

        // Contadores por rol (para la sección "Roles del sistema")
        $totalAdministradores = User::where('rol', 'admin')->count();
        $totalDocentes = User::where('rol', 'docente')->count();
        $totalTutores = User::where('rol', 'tutor')->count();
        $totalAlumnos = User::where('rol', 'alumno')->count();

        // Lista de carreras (para el filtro)
        $carreras = \App\Models\carreras::pluck('nombre')->unique()->values();

        return view('admin.rolesPermisos', compact(
            'usuarios',
            'totalAdministradores',
            'totalDocentes',
            'totalTutores',
            'totalAlumnos',
            'carreras'
        ));
    }
}
