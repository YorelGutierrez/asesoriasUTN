<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\carreras;
use App\Models\materias;
use App\Models\grupos;
use App\Models\User;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    /**
     * Vista principal con tabla y filtros.
     */
    public function index(Request $request)
    {
        $query = User::where('rol', 'docente')
            ->with(['docente.carrera', 'materias', 'grupos']);

        // Filtro por nombre de docente
        if ($request->filled('docente')) {
            $search = $request->docente;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        // Filtro por carrera (relación en tabla docentes)
        if ($request->filled('carrera')) {
            $query->whereHas('docente', function ($q) use ($request) {
                $q->where('carrera_id', $request->carrera);
            });
        }

        // 🔥 Filtro por materia (especificar tabla 'materias')
        if ($request->filled('materia')) {
            $query->whereHas('materias', function ($q) use ($request) {
                $q->where('materias.id', $request->materia);
            });
        }

        // 🔥 Filtro por grupo (especificar tabla 'grupos')
        if ($request->filled('grupo')) {
            $query->whereHas('grupos', function ($q) use ($request) {
                $q->where('grupos.id', $request->grupo);
            });
        }

        $asignaciones = $query->orderBy('apellido_paterno')->paginate(10);
        $carreras = carreras::orderBy('nombre')->get();
        $materias = materias::orderBy('nombre')->get();
        $grupos = grupos::orderBy('nombre')->get();

        return view('admin.asignaciones', compact('asignaciones', 'carreras', 'materias', 'grupos'));
    }

    /**
     * Endpoint: Docentes por carrera.
     */
    public function docentesPorCarrera(Request $request)
    {
        $docentes = User::where('rol', 'docente')
            ->whereHas('docente', function ($q) use ($request) {
                $q->where('carrera_id', $request->carrera_id);
            })
            ->get(['id', 'nombres', 'apellido_paterno', 'apellido_materno']);

        return response()->json($docentes);
    }

    /**
     * Endpoint: Grupos por carrera.
     */
    public function gruposPorCarrera(Request $request)
    {
        $grupos = grupos::where('carrera_id', $request->carrera_id)
            ->get(['id', 'nombre']);

        return response()->json($grupos);
    }

    /**
     * Endpoint: Materias de un docente (para checkboxes).
     */
    public function materiasDocente($docenteId)
    {
        $docente = User::with('materias')->find($docenteId);
        $allMaterias = materias::orderBy('nombre')->get();

        return response()->json([
            'materiasAsignadas' => $docente ? $docente->materias->pluck('id') : [],
            'allMaterias' => $allMaterias,
        ]);
    }

    /**
     * Endpoint: Datos completos para editar (carrera, docente, grupos, materias).
     */
    public function editar($id)
    {
        $docente = User::with(['docente.carrera', 'materias', 'grupos'])->find($id);

        if (!$docente || $docente->rol !== 'docente') {
            return response()->json(['error' => 'Docente no encontrado'], 404);
        }

        $carreraId = $docente->docente->carrera_id;

        // Docentes de la misma carrera (para el select)
        $docentesCarrera = User::where('rol', 'docente')
            ->whereHas('docente', function ($q) use ($carreraId) {
                $q->where('carrera_id', $carreraId);
            })
            ->get(['id', 'nombres', 'apellido_paterno', 'apellido_materno']);

        // Grupos de la carrera
        $gruposCarrera = grupos::where('carrera_id', $carreraId)->get(['id', 'nombre']);

        // Todas las materias
        $allMaterias = materias::orderBy('nombre')->get();

        return response()->json([
            'carrera_id' => $carreraId,
            'docente_id' => $docente->id,
            'docentes' => $docentesCarrera,
            'grupos' => $gruposCarrera,
            'materias' => $allMaterias,
            'materiasAsignadas' => $docente->materias->pluck('id'),
            'gruposAsignados' => $docente->grupos->pluck('id'),
        ]);
    }

    /**
     * Guardar nueva asignación.
     */
    public function store(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'docente_id' => 'required|exists:users,id',
            'materias' => 'nullable|array',
            'materias.*' => 'exists:materias,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
        ]);

        $docente = User::find($request->docente_id);
        $docente->materias()->sync($request->materias ?? []);
        $docente->grupos()->sync($request->grupos ?? []);

        return redirect()->route('admin.asignaciones')
            ->with('success', 'Asignación creada correctamente.');
    }

    /**
     * Actualizar asignación existente.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'docente_id' => 'required|exists:users,id',
            'materias' => 'nullable|array',
            'materias.*' => 'exists:materias,id',
            'grupos' => 'nullable|array',
            'grupos.*' => 'exists:grupos,id',
        ]);

        $docente = User::find($id);
        if (!$docente || $docente->rol !== 'docente') {
            return redirect()->route('admin.asignaciones')
                ->with('error', 'Docente no válido.');
        }

        $docente->materias()->sync($request->materias ?? []);
        $docente->grupos()->sync($request->grupos ?? []);

        return redirect()->route('admin.asignaciones')
            ->with('success', 'Asignación actualizada correctamente.');
    }

    /**
     * Eliminar todas las asignaciones de un docente.
     */
    public function destroy($id)
    {
        $docente = User::find($id);
        if ($docente && $docente->rol === 'docente') {
            $docente->materias()->sync([]);
            $docente->grupos()->sync([]);
            return redirect()->route('admin.asignaciones')
                ->with('success', 'Asignaciones eliminadas correctamente.');
        }

        return redirect()->route('admin.asignaciones')
            ->with('error', 'Docente no encontrado.');
    }
}
