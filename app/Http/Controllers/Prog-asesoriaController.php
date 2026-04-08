<?php

namespace App\Http\Controllers;

use App\Models\Asesoria;
use App\Models\User;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsesoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:docente,admin'); // Solo docentes y admin
    }

    public function create()
    {
        $alumnos = User::where('rol', 'alumno')->get();
        $grupos = Grupo::all();
        
        return view('agendar', compact('alumnos', 'grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:individual,grupal',
            'alumno_id' => 'required_if:tipo,individual|nullable|exists:users,id',
            'grupo_id' => 'required_if:tipo,grupal|nullable|exists:grupos,id',
            'materia' => 'required|string|max:255',
            'tema' => 'required|string',
            'modalidad' => 'required|in:presencial,virtual',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required',
            'preguntas' => 'nullable|string'
        ]);

        $asesoria = Asesoria::create([
            'docente_id' => Auth::id(),
            'tipo' => $request->tipo,
            'alumno_id' => $request->alumno_id,
            'grupo_id' => $request->grupo_id,
            'materia' => $request->materia,
            'tema' => $request->tema,
            'modalidad' => $request->modalidad,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'preguntas' => $request->preguntas,
            'estado' => 'pendiente'
        ]);

        return redirect()->route('agenda')->with('success', 'Asesoría agendada correctamente');
    }
}
