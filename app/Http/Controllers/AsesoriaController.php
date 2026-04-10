<?php

namespace App\Http\Controllers;

use App\Models\carreras;
use App\Models\materias;
use App\Models\alumnos;
use App\Models\sesiones_asesoria;
use App\Models\User;
use App\Models\archivos_asesoria;
use App\Models\docentes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class AsesoriaController extends Controller
{
    public function agendar()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get() ?? collect();
        $docentes = docentes::with('user', 'carrera')->get();

        return view('auth.agendar', compact('carreras', 'materias', 'alumnos', 'docentes'));
    }
    public function create()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $alumnos = alumnos::with(['user', 'grupo'])->get();

        return view('auth.docentes.registro_asesorias', compact('carreras', 'materias', 'alumnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'carrera_id' => 'required|exists:carreras,id',
            'tipo_asesoria' => 'required|in:individual,grupal',
            'materia_id' => 'required|exists:materias,id',
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'alumnos' => 'required|array|min:1',
            'alumnos.*' => 'exists:alumnos,id',
        ]);

        try {
            // Preparar datos
            $carrera = carreras::find($request->carrera_id);
            $materia = materias::find($request->materia_id);

            $alumnosData = alumnos::with(['user', 'grupo'])->whereIn('id', $request->alumnos)->get();

            $data = [
                'carrera_nombre' => $carrera ? $carrera->nombre : 'No especificada',
                'materia_nombre' => $materia ? $materia->nombre : 'No especificada',
                'tipo_asesoria' => $request->tipo_asesoria,
                'tema' => $request->tema,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'alumnos' => [],
            ];

            foreach ($alumnosData as $alumno) {
                $data['alumnos'][] = [
                    'nombre' => $alumno->user->nombres . ' ' . $alumno->user->apellido_paterno . ' ' . $alumno->user->apellido_materno,
                    'grupo' => $alumno->grupo ? $alumno->grupo->nombre : 'N/A',
                ];
            }

            // ========== GENERAR Y DESCARGAR PDF ==========
            $pdf = Pdf::loadView('pdf.asesoria', ['data' => $data]);
            $pdf->setPaper('A4', 'portrait');

            registrar_log('CREAR', 'Asesoría registrada: ' . $data['tema'], 'asesorias');

            // Descargar PDF y luego redirigir (no se puede, la descarga debe ser lo último)
            // Por eso primero descargamos y después mostramos alerta con JavaScript
            return $pdf->download('asesoria_' . date('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
