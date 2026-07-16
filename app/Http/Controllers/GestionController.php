<?php

namespace App\Http\Controllers;

use App\Models\alumnos;
use App\Models\docentes;
use App\Models\grupos;
use App\Models\carreras;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function index(Request $request)
    {
        // --- ALUMNOS con filtros ---
        $alumnosQuery = alumnos::with(['user', 'carrera', 'grupo'])
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->select('alumnos.*');

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $alumnosQuery->where(function ($q) use ($search) {
                $q->where('users.nombres', 'like', "%{$search}%")
                    ->orWhere('users.apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('alumnos.matricula', 'like', "%{$search}%");
            });
        }

        if ($request->filled('carrera')) {
            $alumnosQuery->where('alumnos.carrera_id', $request->carrera);
        }

        if ($request->filled('estado')) {
            $estado = $request->estado === 'activo' ? 1 : 0;
            $alumnosQuery->where('users.estado', $estado);
        }

        $alumnos = $alumnosQuery->orderBy('users.apellido_paterno')->paginate(10, ['*'], 'alumnos_page');

        // --- DOCENTES con filtros ---
        $docentesQuery = docentes::with(['user', 'carrera'])
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select('docentes.*');

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $docentesQuery->where(function ($q) use ($search) {
                $q->where('users.nombres', 'like', "%{$search}%")
                    ->orWhere('users.apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('docentes.numero_empleado', 'like', "%{$search}%");
            });
        }

        if ($request->filled('carrera')) {
            $docentesQuery->where('docentes.carrera_id', $request->carrera);
        }

        if ($request->filled('estado')) {
            $estado = $request->estado === 'activo' ? 1 : 0;
            $docentesQuery->where('users.estado', $estado);
        }

        $docentes = $docentesQuery->orderBy('users.apellido_paterno')->paginate(10, ['*'], 'docentes_page');

        // --- GRUPOS con filtros ---
        $gruposQuery = grupos::with(['carrera'])->withCount('alumnos');

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $gruposQuery->where('nombre', 'like', "%{$search}%");
        }

        if ($request->filled('carrera')) {
            $gruposQuery->where('carrera_id', $request->carrera);
        }

        $grupos = $gruposQuery->orderBy('nombre')->paginate(10, ['*'], 'grupos_page');

        // --- Datos para selects ---
        $carreras = carreras::orderBy('nombre')->get();

        return view('admin.gestion', compact('alumnos', 'docentes', 'grupos', 'carreras'));
    }
}