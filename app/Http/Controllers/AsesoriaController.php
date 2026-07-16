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
use App\Models\notificaciones;
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

    /**
     * Guarda una asesoría agendada desde la vista pública (todos los roles).
     * - Alumno: selecciona un docente, la sesión queda pendiente de confirmación.
     * - Docente: selecciona un alumno, la sesión queda programada directamente.
     * - Admin: selecciona un alumno (o docente), similar a docente.
     */
    public function storeAgenda(Request $request)
    {
        // Validación base
        $request->validate([
            'tema'              => 'required|string|max:255',
            'fecha'             => 'required|date|after_or_equal:today',
            'hora_inicio'       => 'required|date_format:H:i',
            'modalidad'         => 'required|in:presencial,virtual',
            'destinatario_id'   => 'required|integer',
            'tipo_destinatario' => 'required|in:docente,alumno',
            'pregunta_objetivo' => 'nullable|string|max:500',
            'pregunta_conocimiento' => 'nullable|boolean',
            'pregunta_material' => 'nullable|boolean',
            'pregunta_ejercicios' => 'nullable|boolean',
        ]);

        $user = auth()->user();

        try {
            DB::beginTransaction();

            // Determinar docente_id y alumno_id según rol y tipo_destinatario
            $docenteId = null;
            $alumnoId = null;
            $estado = 'programada';

            if ($user->rol === 'alumno') {
                $docenteId = $request->destinatario_id;
                $alumnoId = $user->id;
            } elseif ($user->rol === 'docente') {
                $docenteId = $user->id;
                $alumnoId = $request->destinatario_id;
            } elseif ($user->rol === 'admin') {
                $docenteId = $user->id;
                $alumnoId = $request->destinatario_id;
            } else {
                throw new \Exception('Rol no válido para agendar.');
            }

            // Validar que el destinatario exista y tenga el rol correcto
            if ($request->tipo_destinatario === 'docente') {
                $docente = User::find($docenteId);
                if (!$docente || $docente->rol !== 'docente') {
                    throw new \Exception('El docente seleccionado no es válido.');
                }
            } elseif ($request->tipo_destinatario === 'alumno') {
                $alumno = User::find($alumnoId);
                if (!$alumno || $alumno->rol !== 'alumno') {
                    throw new \Exception('El alumno seleccionado no es válido.');
                }
            }

            // Construir fecha_hora
            $fechaInicio = $request->fecha . ' ' . $request->hora_inicio . ':00';
            $fechaFin = $request->fecha . ' ' . $request->hora_inicio . ':00';

            // Crear la sesión
            $sesion = sesiones_asesoria::create([
                'docente_id'    => $docenteId,
                'tema'          => $request->tema,
                'tipo_asesoria' => 'individual', // 👈 Agregado
                'fecha_inicio'  => $fechaInicio,
                'fecha_fin'     => $fechaFin,
                'modalidad'     => $request->modalidad,
                'estado'        => $estado,
                'motivo'        => $request->pregunta_objetivo ?? 'Sin objetivo específico.',
                'observaciones' => null,
            ]);

            // Asociar el alumno (si existe)
            if ($alumnoId) {
                sesion_alumno::create([
                    'sesion_id' => $sesion->id,
                    'alumno_id' => $alumnoId,
                ]);
            }

            // ---- NOTIFICACIONES ----
            $fechaFormato = date('d/m/Y', strtotime($request->fecha));
            $horaFormato = $request->hora_inicio;

            if ($user->rol === 'alumno') {
                // Alumno → notificar al docente
                $nombreAlumno = $user->nombres . ' ' . $user->apellido_paterno;
                notificaciones::crear(
                    $docenteId,
                    'solicitud_asesoria',
                    "El alumno {$nombreAlumno} ha solicitado una asesoría sobre \"{$request->tema}\" para el {$fechaFormato} a las {$horaFormato} — {$request->modalidad}." .
                        ($request->pregunta_objetivo ? " Objetivo: {$request->pregunta_objetivo}" : ''),
                    [
                        'sesion_id' => $sesion->id,
                        'alumno_user_id' => $user->id,
                        'alumno_nombre' => $nombreAlumno,
                        'fecha' => $fechaFormato,
                        'hora' => $horaFormato,
                        'modalidad' => $request->modalidad,
                        'objetivo' => $request->pregunta_objetivo ?? '',
                        'tema' => $request->tema,
                    ]
                );

                // Notificar al alumno (confirmación de envío)
                notificaciones::crear(
                    $user->id,
                    'recordatorio',
                    "Tu solicitud de asesoría sobre \"{$request->tema}\" fue enviada al docente. Espera su confirmación.",
                    ['sesion_id' => $sesion->id]
                );
            } elseif ($user->rol === 'docente' || $user->rol === 'admin') {
                // Docente/Admin → notificar al alumno
                $nombreDocente = $user->nombres . ' ' . $user->apellido_paterno;
                notificaciones::crear(
                    $alumnoId,
                    'solicitud_asesoria',
                    "El docente {$nombreDocente} ha agendado una asesoría contigo sobre \"{$request->tema}\" para el {$fechaFormato} a las {$horaFormato} — {$request->modalidad}." .
                        ($request->pregunta_objetivo ? " Objetivo: {$request->pregunta_objetivo}" : ''),
                    [
                        'sesion_id' => $sesion->id,
                        'docente_id' => $user->id,
                        'docente_nombre' => $nombreDocente,
                        'fecha' => $fechaFormato,
                        'hora' => $horaFormato,
                        'modalidad' => $request->modalidad,
                        'objetivo' => $request->pregunta_objetivo ?? '',
                        'tema' => $request->tema,
                    ]
                );

                // Notificar al docente (confirmación de agendado)
                notificaciones::crear(
                    $user->id,
                    'recordatorio',
                    "Has agendado una asesoría con el alumno para el {$fechaFormato} a las {$horaFormato}.",
                    ['sesion_id' => $sesion->id]
                );
            }

            DB::commit();

            registrar_log('CREAR', 'Asesoría agendada: ' . $request->tema, 'asesorias');

            // Redirigir según rol
            $dashboard = match ($user->rol) {
                'admin' => 'admin.dashboard',
                'docente' => 'docente.dashboard',
                default => 'alumno.dashboard'
            };

            return redirect()->route($dashboard)
                ->with('success', 'Asesoría agendada correctamente. ' . ($user->rol === 'alumno' ? 'El docente recibirá tu solicitud.' : ''));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al agendar: ' . $e->getMessage());
        }
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

    /**
     * Muestra el historial general de asesorías con filtros según el rol.
     */
    public function historial(Request $request)
    {
        $user = auth()->user();

        // --- Construir consulta base ---
        $query = sesiones_asesoria::with(['docente', 'alumnos', 'acuerdos', 'reporte'])
            ->where('estado', 'realizada'); // Solo realizadas

        // Filtrar por rol
        if ($user->rol === 'docente') {
            $query->where('docente_id', $user->id);
        } elseif ($user->rol === 'alumno') {
            $query->whereHas('alumnos', function ($q) use ($user) {
                $q->where('sesion_alumno.alumno_id', $user->id);
            });
        }
        // Admin: sin filtro

        // --- Filtros GET ---

        // 🔥 CUATRIMESTRE: filtrar sesiones que tengan alumnos con ese cuatrimestre
        if ($request->filled('cuatrimestre')) {
            $cuatrimestre = $request->cuatrimestre;
            $query->whereHas('alumnos', function ($q) use ($cuatrimestre) {
                $q->whereHas('alumno', function ($sub) use ($cuatrimestre) {
                    $sub->where('cuatrimestre', $cuatrimestre);
                });
            });
        }

        // Materia (buscamos en el campo 'tema')
        if ($request->filled('materia')) {
            $query->where('tema', 'like', '%' . $request->materia . '%');
        }

        // Buscar alumno (por nombre o matrícula)
        if ($request->filled('buscar_alumno')) {
            $search = $request->buscar_alumno;
            $query->whereHas('alumnos', function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%");
            });
        }

        // Fecha (fecha_inicio)
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_inicio', $request->fecha);
        }

        // --- Obtener datos para selects ---
        // Materias: extraemos temas únicos de las sesiones del usuario (sin filtrar por estado)
        $materias = sesiones_asesoria::where('estado', 'realizada')
            ->distinct('tema')->pluck('tema')->filter()->values();

        // Cuatrimestres: rango fijo 1-12 (o los que existan en la tabla alumnos)
        $cuatrimestres = range(1, 12);

        // --- Obtener sesiones con paginación (10 por página) ---
        $sesiones = $query->orderBy('fecha_inicio', 'desc')->paginate(10);

        return view('auth.historial', compact('sesiones', 'materias', 'cuatrimestres'));
    }
}
