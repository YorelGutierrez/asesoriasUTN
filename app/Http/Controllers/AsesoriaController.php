<?php

namespace App\Http\Controllers;

use App\Models\carreras;
use App\Models\materias;
use App\Models\alumnos;
use App\Models\docentes;
use App\Models\sesiones_asesoria;
use App\Models\User;
use App\Models\archivos_asesoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class AsesoriaController extends Controller
{
    // Método para la vista NUEVA de agendar (agendar.blade.php)
    public function agendar()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get() ?? collect();
        $docentes = docentes::with('user', 'carrera')->get();
        
        return view('auth.agendar', compact('carreras', 'materias', 'alumnos', 'docentes'));
    }

    // Método para la vista ANTIGUA (registro_asesorias.blade.php)
    public function create()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get();
        
        return view('auth.docentes.registro_asesorias', compact('carreras', 'materias', 'alumnos'));
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validated = $request->validate([
                'docente_id' => 'required|exists:docentes,id',
                'fecha' => 'required|date',
                'hora_inicio' => 'required',
                'tema' => 'required|string|min:3',
                'modalidad' => 'required|in:presencial,en_linea',
                'pregunta_objetivo' => 'nullable|string'
            ]);
            
            // Obtener el docente con sus relaciones
            $docente = docentes::with('user', 'carrera')->find($request->docente_id);
            
            // Preparar datos para el PDF/Word
            $data = [
                'docente_nombre' => $docente ? $docente->user->nombres . ' ' . $docente->user->apellido_paterno : 'No especificado',
                'docente_email' => $docente ? $docente->user->email : 'No especificado',
                'carrera_nombre' => $docente && $docente->carrera ? $docente->carrera->nombre : 'No especificada',
                'tema' => $request->tema,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'modalidad' => $request->modalidad == 'presencial' ? 'Presencial' : 'En línea',
                'pregunta_objetivo' => $request->pregunta_objetivo ?? 'No especificada',
                'preguntas_previas' => [
                    'conoce_tema' => $request->has('pregunta_conocimiento'),
                    'necesita_material' => $request->has('pregunta_material'),
                    'tiene_ejercicios' => $request->has('pregunta_ejercicios')
                ]
            ];
            
            // Guardar en la base de datos (opcional - ajusta según tu tabla)
            // $sesion = sesiones_asesoria::create([
            //     'docente_id' => $request->docente_id,
            //     'fecha' => $request->fecha,
            //     'hora_inicio' => $request->hora_inicio,
            //     'tema' => $request->tema,
            //     'modalidad' => $request->modalidad,
            //     'pregunta_objetivo' => $request->pregunta_objetivo,
            //     'estado' => 'programada'
            // ]);
            
            // Generar PDF
            $pdf = Pdf::loadView('pdf.asesoria_nueva', ['data' => $data]);
            $pdf->setPaper('A4', 'portrait');
            
            return $pdf->download('asesoria_' . date('Ymd_His') . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage() . ' | Línea: ' . $e->getLine());
        }
    }
}