<?php

namespace App\Http\Controllers;

use App\Models\acuerdos_asesoria;
use App\Models\carreras;
use App\Models\materias;
use App\Models\alumnos;
use App\Models\sesiones_asesoria;
use App\Models\User;
use App\Models\archivos_asesoria;
use App\Models\docentes;
use App\Models\reportes_asesoria;
use App\Models\sesion_alumno;
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
        $user = auth()->user();

        // Cargar datos según el rol
        if ($user->rol == 'alumno') {
            // Alumno ve docentes
            $docentes = docentes::with(['user', 'carrera'])->get();
            $alumnos = collect(); // vacío
            $tipoVista = 'alumno';
        } elseif ($user->rol == 'docente') {
            // Docente ve alumnos
            $alumnos = alumnos::with(['user', 'grupo'])->get();
            $docentes = collect(); // vacío
            $tipoVista = 'docente';
        } else {
            // Admin ve alumnos (por defecto)
            $alumnos = alumnos::with(['user', 'grupo'])->get();
            $docentes = collect(); // vacío
            $tipoVista = 'admin';
        }

        return view('auth.agendar', compact('carreras', 'materias', 'alumnos', 'docentes', 'tipoVista'));
    }
    public function create()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        $user = auth()->user();
        $grupoActivoId = session('grupo_activo_id');

        if ($user->rol === 'admin') {
            // Admin: ve todos los alumnos (sin importar grupo)
            $alumnos = alumnos::with(['user', 'grupo'])
                ->join('users', 'alumnos.user_id', '=', 'users.id')
                ->orderBy('users.apellido_paterno')
                ->orderBy('users.nombres')
                ->select('alumnos.*')
                ->get();
        } else {
            // Docente: solo alumnos del grupo activo
            if ($grupoActivoId) {
                $alumnos = alumnos::with(['user', 'grupo'])
                    ->where('grupo_id', $grupoActivoId)
                    ->join('users', 'alumnos.user_id', '=', 'users.id')
                    ->orderBy('users.apellido_paterno')
                    ->orderBy('users.nombres')
                    ->select('alumnos.*')
                    ->get();
            } else {
                // Docente sin grupo activo → lista vacía
                $alumnos = collect(); // colección vacía
            }
        }

        return view('auth.docentes.registro_asesorias', compact('carreras', 'materias', 'alumnos'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'hora_inicio' => $request->hora_inicio ?: null,
            'hora_fin'    => $request->hora_fin ?: null,
        ]);

        $request->validate([
            'carrera_id'    => 'required|exists:carreras,id',
            'tipo_asesoria' => 'required|in:individual,grupal',
            'materia_id'    => 'required|exists:materias,id',
            'tema'          => 'required|string|max:255',
            'fecha'         => 'required|date',
            'hora_inicio'   => 'nullable|date_format:H:i',
            'hora_fin'      => 'nullable|date_format:H:i',
            'alumnos'       => 'required|array|min:1',
            'alumnos.*'     => 'exists:alumnos,id',
            'motivo'        => 'required|string|max:255',
            'modalidad'     => 'required|in:presencial,virtual',
            'acuerdo'       => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $fechaInicio = $request->fecha . ' ' . ($request->hora_inicio ?? '00:00') . ':00';
            $fechaFin    = $request->fecha . ' ' . ($request->hora_fin ?? '00:00') . ':00';

            $sesion = sesiones_asesoria::create([
                'docente_id'    => auth()->id(),
                'tema'          => $request->tema,
                'tipo_asesoria' => $request->tipo_asesoria,
                'fecha_inicio'  => $fechaInicio,
                'fecha_fin'     => $fechaFin,
                'modalidad'     => $request->modalidad,
                'estado'        => 'realizada',
                'motivo'        => $request->motivo,
            ]);

            foreach ($request->alumnos as $alumnoId) {
                $alumno = alumnos::find($alumnoId);
                if ($alumno) {
                    sesion_alumno::create([
                        'sesion_id' => $sesion->id,
                        'alumno_id' => $alumno->user_id,
                    ]);
                }
            }

            if ($request->filled('acuerdo')) {
                acuerdos_asesoria::create([
                    'sesion_id' => $sesion->id,
                    'alumno_id' => null,
                    'acuerdo'   => $request->acuerdo,
                ]);
            }

            DB::commit();

            registrar_log('CREAR', 'Asesoría registrada: ' . $request->tema, 'asesorias');

            // Devolver JSON para que el frontend maneje el flujo
            return response()->json([
                'success' => true,
                'sesion_id' => $sesion->id,
                'tipo_asesoria' => $request->tipo_asesoria,
                'primer_alumno_id' => $request->alumnos[0],
                'message' => 'Asesoría registrada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generarReporte(Request $request)
    {
        $request->validate([
            'sesion_id' => 'required|exists:sesiones_asesoria,id',
            'descargar' => 'nullable|boolean',
        ]);

        $sesion = sesiones_asesoria::with(['docente', 'alumnos'])->findOrFail($request->sesion_id);

        // Determinar carrera
        $carreraNombre = 'No especificada';
        if ($sesion->docente && $sesion->docente->carrera) {
            $carreraNombre = $sesion->docente->carrera->nombre;
        } else {
            $primerAlumno = $sesion->alumnos->first();
            if ($primerAlumno && $primerAlumno->alumno && $primerAlumno->alumno->carrera) {
                $carreraNombre = $primerAlumno->alumno->carrera->nombre;
            }
        }

        $materiaNombre = 'No especificada';

        $data = [
            'carrera_nombre' => $carreraNombre,
            'materia_nombre' => $materiaNombre,
            'tipo_asesoria'  => $sesion->tipo_asesoria ?? 'individual',
            'tema'           => $sesion->tema,
            'fecha'          => \Carbon\Carbon::parse($sesion->fecha_inicio)->format('Y-m-d'),
            'hora_inicio'    => \Carbon\Carbon::parse($sesion->fecha_inicio)->format('H:i'),
            'hora_fin'       => \Carbon\Carbon::parse($sesion->fecha_fin)->format('H:i'),
            'motivo'         => $sesion->motivo,
            'modalidad'      => $sesion->modalidad,
            'alumnos'        => [],
        ];

        foreach ($sesion->alumnos as $alumno) {
            $data['alumnos'][] = [
                'nombre' => $alumno->nombres . ' ' . $alumno->apellido_paterno . ' ' . $alumno->apellido_materno,
                'grupo'  => $alumno->alumno->grupo->nombre ?? 'N/A',
            ];
        }

        $pdf = Pdf::loadView('pdf.asesoria', ['data' => $data]);
        $pdfContent = $pdf->output();

        // Guardar en reportes_asesoria
        $nombreArchivo = 'reporte_' . $sesion->id . '_' . time() . '.pdf';
        $ruta = 'reportes/' . $nombreArchivo;
        Storage::disk('public')->put($ruta, $pdfContent);

        $reporte = reportes_asesoria::create([
            'sesion_id'      => $sesion->id,
            'nombre_archivo' => $nombreArchivo,
            'ruta'           => $ruta,
        ]);

        // Si se solicita descargar, devolver el PDF como descarga
        if ($request->descargar) {
            return response()->download(storage_path('app/public/' . $ruta), $nombreArchivo);
        }

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $ruta),
            'mensaje' => 'PDF generado correctamente'
        ]);
    }
}
