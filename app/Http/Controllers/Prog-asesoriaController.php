<?php

namespace App\Http\Controllers;

use App\Models\Asesoria;
use App\Models\User;
use App\Models\Grupo;
use App\Models\carreras;
use App\Models\materias;
use App\Models\alumnos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsesoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('rol:docente,admin'); // Solo docentes y admin
    }

    public function create()
    {
        // Para la vista agendar.blade.php
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get();
        $grupos = Grupo::all();
        
        return view('agendar', compact('carreras', 'materias', 'alumnos', 'grupos'));
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'tipo_asesoria' => 'required|in:individual,grupal',
            'materia_id' => 'required|exists:materias,id',
            'tema' => 'required|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'alumnos' => 'required|array|min:1',
            'alumnos.*' => 'exists:alumnos,id',
            'modalidad' => 'required|in:presencial,virtual'
        ]);

        try {
            // Crear la asesoría
            $asesoria = Asesoria::create([
                'docente_id' => Auth::id(),
                'tipo' => $request->tipo_asesoria,
                'alumno_id' => $request->tipo_asesoria == 'individual' ? ($request->alumnos[0] ?? null) : null,
                'grupo_id' => null, // Si tienes grupo, puedes asignarlo
                'materia' => materias::find($request->materia_id)->nombre,
                'tema' => $request->tema,
                'modalidad' => $request->modalidad,
                'fecha' => $request->fecha,
                'hora' => $request->hora_inicio,
                'preguntas' => null,
                'estado' => 'pendiente'
            ]);

            return redirect()->route('agenda')->with('success', 'Asesoría agendada correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage())->withInput();
        }
    }
}