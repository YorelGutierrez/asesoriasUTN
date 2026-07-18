<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\alumnos;
use App\Models\docentes;
use App\Models\materias;
use App\Models\solicitudes_asesoria;
use App\Models\sesiones_asesoria;
use App\Models\acuerdos_asesoria;
use App\Models\historial_academico;
use App\Models\grupos;

class GraficasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->rol) {
            case 'admin':
                return $this->adminDashboard();
            case 'docente':
                return $this->docenteDashboard();
            case 'alumno':
                return $this->alumnoDashboard();
            default:
                return redirect()->route('login')->with('error', 'Rol no válido');
        }
    }

    // ============================================================
    // 📊 DASHBOARD ADMIN - CORREGIDO
    // ============================================================
    private function adminDashboard()
    {
        $totalAdministradores = User::where('rol', 'admin')->count();
        $totalDocentes = User::where('rol', 'docente')->count();
        $totalTutores = User::where('rol', 'tutor')->count();
        $totalAlumnos = User::where('rol', 'alumno')->count();

        $ultimo = null;
        $horaProgramada = null;

        // 1. Solicitudes por estado
        $solicitudesEstado = solicitudes_asesoria::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        $estadosLabels = $solicitudesEstado->pluck('estado')->map(function($e) {
            $map = [
                'pendiente' => 'Pendientes',
                'atendida' => 'Atendidas',
                'cancelada' => 'Canceladas'
            ];
            return $map[$e] ?? $e;
        });
        $estadosValues = $solicitudesEstado->pluck('total');

        // 2. Solicitudes por mes
        $mesesData = solicitudes_asesoria::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('año', 'ASC')
            ->orderBy('mes', 'ASC')
            ->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $mesesLabels = [];
        $mesesValues = [];
        
        foreach ($mesesData as $item) {
            $mesesLabels[] = $meses[$item->mes - 1] . ' ' . $item->año;
            $mesesValues[] = $item->total;
        }

        // 3. Materias con más solicitudes
        $materias = solicitudes_asesoria::select('materia_id', DB::raw('COUNT(*) as total'))
            ->with('materia')
            ->groupBy('materia_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $materiasLabels = $materias->map(function($item) {
            return $item->materia ? $item->materia->nombre : 'Sin materia';
        });
        $materiasValues = $materias->pluck('total');

        // 4. Alumnos con más solicitudes - CORREGIDO
        $alumnos = solicitudes_asesoria::select('alumno_id', DB::raw('COUNT(*) as total'))
            ->with('alumno')  // ← CORREGIDO: solo alumno
            ->groupBy('alumno_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $alumnosLabels = $alumnos->map(function($item) {
            if ($item->alumno) {  // ← CORREGIDO
                return $item->alumno->nombres . ' ' . $item->alumno->apellido_paterno;
            }
            return 'Sin asignar';
        });
        $alumnosValues = $alumnos->pluck('total');

        // 5. Docentes con más sesiones - CORREGIDO
        $docentes = sesiones_asesoria::select('docente_id', DB::raw('COUNT(*) as total'))
            ->with('docente')  // ← CORREGIDO: solo docente
            ->groupBy('docente_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $docentesLabels = $docentes->map(function($item) {
            if ($item->docente) {  // ← CORREGIDO
                return $item->docente->nombres . ' ' . $item->docente->apellido_paterno;
            }
            return 'Sin asignar';
        });
        $docentesValues = $docentes->pluck('total');

        // 6. Solicitudes por día
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $diasData = solicitudes_asesoria::select(
                DB::raw('DAYOFWEEK(created_at) as dia'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DAYOFWEEK(created_at)'))
            ->get();

        $diasLabels = [];
        $diasValues = [];
        foreach ($diasData as $item) {
            $diasLabels[] = $dias[$item->dia - 1] ?? 'Desconocido';
            $diasValues[] = $item->total;
        }

        // 7. Acuerdos registrados
        $resultados = acuerdos_asesoria::select('acuerdo', DB::raw('COUNT(*) as total'))
            ->whereNotNull('acuerdo')
            ->groupBy('acuerdo')
            ->get();

        $resultadosLabels = $resultados->pluck('acuerdo');
        $resultadosValues = $resultados->pluck('total');

        // 8. Sesiones por tipo
        $tiposSesion = sesiones_asesoria::select('tipo_asesoria', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_asesoria')
            ->get();

        $tiposLabels = $tiposSesion->pluck('tipo_asesoria')->map(function($e) {
            $map = [
                'virtual' => 'Virtual',
                'presencial' => 'Presencial',
                'mixta' => 'Mixta'
            ];
            return $map[$e] ?? $e;
        });
        $tiposValues = $tiposSesion->pluck('total');

        // 9. Materias con más reprobadas
        $materiasReprobadas = historial_academico::select('materia_id', DB::raw('COUNT(*) as total'))
            ->where('reprobada', true)
            ->with('materia')
            ->groupBy('materia_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $materiasReprobadasLabels = $materiasReprobadas->map(function($item) {
            return $item->materia ? $item->materia->nombre : 'Sin materia';
        });
        $materiasReprobadasValues = $materiasReprobadas->pluck('total');

        return view('admin.dashboard', compact(
            'totalAdministradores',
            'totalDocentes',
            'totalTutores',
            'totalAlumnos',
            'ultimo',
            'horaProgramada',
            'estadosLabels',
            'estadosValues',
            'mesesLabels',
            'mesesValues',
            'materiasLabels',
            'materiasValues',
            'alumnosLabels',
            'alumnosValues',
            'docentesLabels',
            'docentesValues',
            'diasLabels',
            'diasValues',
            'resultadosLabels',
            'resultadosValues',
            'tiposLabels',
            'tiposValues',
            'materiasReprobadasLabels',
            'materiasReprobadasValues'
        ));
    }

    // ============================================================
    // 📊 DASHBOARD DOCENTE
    // ============================================================
    private function docenteDashboard()
    {
        $user = Auth::user();
        
        $docente = docentes::where('user_id', $user->id)->first();
        $docenteId = $docente ? $docente->id : null;

        // Próxima asesoría del docente
        $proximaAsesoria = sesiones_asesoria::where('docente_id', $docenteId)
            ->where('estado', 'pendiente')
            ->where('fecha_inicio', '>', now())
            ->orderBy('fecha_inicio', 'ASC')
            ->first();

        // Total de alumnos únicos que ha atendido el docente
        $totalAlumnos = DB::table('sesion_alumno')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->distinct('sesion_alumno.alumno_id')
            ->count('sesion_alumno.alumno_id');

        // Grupos activos del docente
        $gruposActivos = DB::table('sesion_alumno')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->distinct('alumnos.grupo_id')
            ->count('alumnos.grupo_id');

        // Grupos recientes
        $gruposRecientes = DB::table('grupos')
            ->join('alumnos', 'grupos.id', '=', 'alumnos.grupo_id')
            ->join('sesion_alumno', 'alumnos.user_id', '=', 'sesion_alumno.alumno_id')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->select('grupos.*')
            ->distinct()
            ->limit(6)
            ->get();

        // 1. Mis sesiones por mes
        $mesesData = sesiones_asesoria::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('docente_id', $docenteId)
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('año', 'ASC')
            ->orderBy('mes', 'ASC')
            ->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $mesesLabels = [];
        $mesesValues = [];
        
        foreach ($mesesData as $item) {
            $mesesLabels[] = $meses[$item->mes - 1] . ' ' . $item->año;
            $mesesValues[] = $item->total;
        }

        // 2. Mis sesiones por materia
        $materias = DB::table('sesiones_asesoria')
            ->join('solicitudes_asesoria', 'sesiones_asesoria.solicitud_id', '=', 'solicitudes_asesoria.id')
            ->join('materias', 'solicitudes_asesoria.materia_id', '=', 'materias.id')
            ->select(
                'materias.nombre as materia_nombre',
                DB::raw('COUNT(*) as total')
            )
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->groupBy('materias.nombre')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $materiasLabels = $materias->pluck('materia_nombre');
        $materiasValues = $materias->pluck('total');

        // 3. Mis alumnos más frecuentes
        $alumnos = DB::table('sesion_alumno')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->select(
                'users.nombres',
                'users.apellido_paterno',
                DB::raw('COUNT(*) as total')
            )
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->groupBy('users.nombres', 'users.apellido_paterno')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $alumnosLabels = $alumnos->map(function($item) {
            return $item->nombres . ' ' . $item->apellido_paterno;
        });
        $alumnosValues = $alumnos->pluck('total');

        // 4. Solicitudes asociadas a las sesiones del docente
        $solicitudes = solicitudes_asesoria::select('estado', DB::raw('COUNT(*) as total'))
            ->whereHas('sesiones', function($query) use ($docenteId) {
                $query->where('docente_id', $docenteId);
            })
            ->groupBy('estado')
            ->get();

        $solicitudesLabels = $solicitudes->pluck('estado')->map(function($e) {
            $map = [
                'pendiente' => 'Pendientes',
                'atendida' => 'Atendidas',
                'cancelada' => 'Canceladas'
            ];
            return $map[$e] ?? $e;
        });
        $solicitudesValues = $solicitudes->pluck('total');

        // 5. Totales del docente
        $totalSesiones = sesiones_asesoria::where('docente_id', $docenteId)->count();
        
        $totalSolicitudes = solicitudes_asesoria::whereHas('sesiones', function($query) use ($docenteId) {
            $query->where('docente_id', $docenteId);
        })->count();
        
        $totalPendientes = solicitudes_asesoria::whereHas('sesiones', function($query) use ($docenteId) {
            $query->where('docente_id', $docenteId);
        })->where('estado', 'pendiente')->count();
        
        $totalAtendidas = solicitudes_asesoria::whereHas('sesiones', function($query) use ($docenteId) {
            $query->where('docente_id', $docenteId);
        })->where('estado', 'atendida')->count();

        return view('auth.docentes.escritorioDocente', compact(
            'proximaAsesoria',
            'totalAlumnos',
            'gruposActivos',
            'gruposRecientes',
            'mesesLabels',
            'mesesValues',
            'materiasLabels',
            'materiasValues',
            'alumnosLabels',
            'alumnosValues',
            'solicitudesLabels',
            'solicitudesValues',
            'totalSesiones',
            'totalSolicitudes',
            'totalPendientes',
            'totalAtendidas'
        ));
    }

    // ============================================================
    // 📊 DASHBOARD ALUMNO
    // ============================================================
    private function alumnoDashboard()
    {
        $user = Auth::user();
        
        $alumno = alumnos::where('user_id', $user->id)->first();
        $alumnoId = $alumno ? $alumno->id : null;

        $proximaAsesoria = sesiones_asesoria::whereHas('alumnos', function($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->where('estado', 'pendiente')
            ->where('fecha_inicio', '>', now())
            ->orderBy('fecha_inicio', 'ASC')
            ->first();

        $agendadas = sesiones_asesoria::whereHas('alumnos', function($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->where('estado', 'pendiente')
            ->count();

        $completadas = sesiones_asesoria::whereHas('alumnos', function($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->where('estado', 'completada')
            ->count();

        $docentesAleatorios = [];
        if ($alumno && $alumno->grupo_id) {
            $docentesDelGrupo = docentes::with('user')->get();

            foreach ($docentesDelGrupo as $docente) {
                $docentesAleatorios[] = [
                    'docente' => $docente->user,
                    'materias' => []
                ];
            }
            shuffle($docentesAleatorios);
            $docentesAleatorios = array_slice($docentesAleatorios, 0, 3);
        }

        $ultimasSesiones = sesiones_asesoria::whereHas('alumnos', function($query) use ($alumnoId) {
                $query->where('alumno_id', $alumnoId);
            })
            ->with(['docente.user', 'alumnos', 'acuerdos', 'reporte'])
            ->orderBy('fecha_inicio', 'DESC')
            ->limit(10)
            ->get();

        // 1. Sesiones por mes
        $mesesData = DB::table('sesiones_asesoria')
            ->join('sesion_alumno', 'sesiones_asesoria.id', '=', 'sesion_alumno.sesion_id')
            ->select(
                DB::raw('YEAR(sesiones_asesoria.created_at) as año'),
                DB::raw('MONTH(sesiones_asesoria.created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('sesion_alumno.alumno_id', $alumnoId)
            ->where('sesiones_asesoria.created_at', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(sesiones_asesoria.created_at)'), DB::raw('MONTH(sesiones_asesoria.created_at)'))
            ->orderBy('año', 'ASC')
            ->orderBy('mes', 'ASC')
            ->get();

        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $mesesLabels = [];
        $mesesValues = [];
        
        foreach ($mesesData as $item) {
            $mesesLabels[] = $meses[$item->mes - 1] . ' ' . $item->año;
            $mesesValues[] = $item->total;
        }

        // 2. Sesiones por materia
        $materias = DB::table('sesiones_asesoria')
            ->join('sesion_alumno', 'sesiones_asesoria.id', '=', 'sesion_alumno.sesion_id')
            ->join('solicitudes_asesoria', 'sesiones_asesoria.solicitud_id', '=', 'solicitudes_asesoria.id')
            ->join('materias', 'solicitudes_asesoria.materia_id', '=', 'materias.id')
            ->select(
                'materias.nombre as materia_nombre',
                DB::raw('COUNT(*) as total')
            )
            ->where('sesion_alumno.alumno_id', $alumnoId)
            ->groupBy('materias.nombre')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $materiasLabels = $materias->pluck('materia_nombre');
        $materiasValues = $materias->pluck('total');

        // 3. Docentes que me asesoran
        $docentes = DB::table('sesiones_asesoria')
            ->join('sesion_alumno', 'sesiones_asesoria.id', '=', 'sesion_alumno.sesion_id')
            ->join('docentes', 'sesiones_asesoria.docente_id', '=', 'docentes.id')
            ->join('users', 'docentes.user_id', '=', 'users.id')
            ->select(
                DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as docente_nombre'),
                DB::raw('COUNT(*) as total')
            )
            ->where('sesion_alumno.alumno_id', $alumnoId)
            ->groupBy('users.nombres', 'users.apellido_paterno')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $docentesLabels = $docentes->pluck('docente_nombre');
        $docentesValues = $docentes->pluck('total');

        $solicitudes = solicitudes_asesoria::select('estado', DB::raw('COUNT(*) as total'))
            ->where('alumno_id', $alumnoId)
            ->groupBy('estado')
            ->get();

        $solicitudesLabels = $solicitudes->pluck('estado')->map(function($e) {
            $map = [
                'pendiente' => 'Pendientes',
                'atendida' => 'Atendidas',
                'cancelada' => 'Canceladas'
            ];
            return $map[$e] ?? $e;
        });
        $solicitudesValues = $solicitudes->pluck('total');

        // 5. Totales del alumno
        $totalSesiones = DB::table('sesion_alumno')
            ->where('alumno_id', $alumnoId)
            ->count();
        
        $totalSolicitudes = solicitudes_asesoria::where('alumno_id', $alumnoId)->count();
        $totalPendientes = solicitudes_asesoria::where('alumno_id', $alumnoId)->where('estado', 'pendiente')->count();
        $totalAtendidas = solicitudes_asesoria::where('alumno_id', $alumnoId)->where('estado', 'atendida')->count();

        $historial = historial_academico::where('alumno_id', $alumnoId)->get();
        $materiasReprobadas = $historial->where('reprobada', true)->count();
        $materiasAprobadas = $historial->where('reprobada', false)->count();
        
        $misMateriasReprobadas = historial_academico::where('alumno_id', $alumnoId)
            ->where('reprobada', true)
            ->with('materia')
            ->get();

        $misMateriasReprobadasLabels = $misMateriasReprobadas->map(function($item) {
            return $item->materia ? $item->materia->nombre : 'Sin materia';
        });
        $misMateriasReprobadasValues = $misMateriasReprobadas->pluck('cuatrimestre');

        return view('auth.alumnos.escritorioAlumno', compact(
            'proximaAsesoria',
            'agendadas',
            'completadas',
            'docentesAleatorios',
            'alumno',
            'ultimasSesiones',
            'mesesLabels',
            'mesesValues',
            'materiasLabels',
            'materiasValues',
            'docentesLabels',
            'docentesValues',
            'solicitudesLabels',
            'solicitudesValues',
            'totalSesiones',
            'totalSolicitudes',
            'totalPendientes',
            'totalAtendidas',
            'materiasReprobadas',
            'materiasAprobadas',
            'misMateriasReprobadasLabels',
            'misMateriasReprobadasValues'
        ));
    }
}