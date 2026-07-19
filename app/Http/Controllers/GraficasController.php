<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
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
    // DASHBOARD ADMIN
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

        // 3. Alumnos con más sesiones
        $alumnos = DB::table('sesion_alumno')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->select(
                DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as nombre_completo'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('users.nombres', 'users.apellido_paterno')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $alumnosLabels = $alumnos->pluck('nombre_completo');
        $alumnosValues = $alumnos->pluck('total');

        // 4. Docentes con más sesiones
        $docentes = sesiones_asesoria::select('docente_id', DB::raw('COUNT(*) as total'))
            ->with('docente')
            ->groupBy('docente_id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $docentesLabels = $docentes->map(function($item) {
            if ($item->docente) {
                return $item->docente->nombres . ' ' . $item->docente->apellido_paterno;
            }
            return 'Sin asignar';
        });
        $docentesValues = $docentes->pluck('total');

        // 5. Solicitudes por día
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

        // 6. Acuerdos registrados
        $resultados = acuerdos_asesoria::select('acuerdo', DB::raw('COUNT(*) as total'))
            ->whereNotNull('acuerdo')
            ->groupBy('acuerdo')
            ->get();

        $resultadosLabels = $resultados->pluck('acuerdo');
        $resultadosValues = $resultados->pluck('total');

        // 7. Sesiones por tipo
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

        // 8. Materias con más reprobadas
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
    // DASHBOARD DOCENTE
    // ============================================================
    private function docenteDashboard()
    {
        $user = Auth::user();
        
        $docenteId = $user->id;

        $proximaAsesoria = sesiones_asesoria::where('docente_id', $docenteId)
            ->where('estado', 'pendiente')
            ->where('fecha_inicio', '>', now())
            ->orderBy('fecha_inicio', 'ASC')
            ->first();

        $totalAlumnos = DB::table('sesion_alumno')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->distinct('sesion_alumno.alumno_id')
            ->count('sesion_alumno.alumno_id');

        $gruposActivos = DB::table('sesion_alumno')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->distinct('alumnos.grupo_id')
            ->count('alumnos.grupo_id');

        $gruposIds = DB::table('grupos')
            ->join('alumnos', 'grupos.id', '=', 'alumnos.grupo_id')
            ->join('sesion_alumno', 'alumnos.user_id', '=', 'sesion_alumno.alumno_id')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->distinct()
            ->pluck('grupos.id')
            ->take(6)
            ->toArray();

        $gruposRecientes = grupos::with(['carrera', 'alumnos'])
            ->whereIn('id', $gruposIds)
            ->get();

        // 1. Mis alumnos más frecuentes
        $alumnos = DB::table('sesion_alumno')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->select(
                DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as nombre_completo'),
                DB::raw('COUNT(*) as total')
            )
            ->where('sesiones_asesoria.docente_id', $docenteId)
            ->groupBy('users.nombres', 'users.apellido_paterno')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $alumnosLabels = $alumnos->pluck('nombre_completo');
        $alumnosValues = $alumnos->pluck('total');

        // 2. Estado de mis sesiones
        $solicitudes = sesiones_asesoria::select('estado', DB::raw('COUNT(*) as total'))
            ->where('docente_id', $docenteId)
            ->groupBy('estado')
            ->get();

        $solicitudesLabels = $solicitudes->pluck('estado')->map(function($e) {
            $map = [
                'programada' => 'Programadas',
                'realizada' => 'Realizadas',
                'cancelada' => 'Canceladas',
                'pendiente' => 'Pendientes'
            ];
            return $map[$e] ?? $e;
        });
        $solicitudesValues = $solicitudes->pluck('total');

        return view('auth.docentes.escritorioDocente', compact(
            'proximaAsesoria',
            'totalAlumnos',
            'gruposActivos',
            'gruposRecientes',
            'alumnosLabels',
            'alumnosValues',
            'solicitudesLabels',
            'solicitudesValues'
        ));
    }

    // ============================================================
    // DASHBOARD ALUMNO - SIN GRÁFICAS
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

        return view('auth.alumnos.escritorioAlumno', compact(
            'proximaAsesoria',
            'agendadas',
            'completadas',
            'docentesAleatorios',
            'alumno',
            'ultimasSesiones'
        ));
    }

    // ============================================================
    // GENERAR PDF ADMIN
    // ============================================================
    public function imprimirPDFAdmin(Request $request)
    {
        $ids = $request->ids;
        
        // Alumnos con más solicitudes
        $alumnosLabels = [];
        $alumnosValues = [];
        
        if (in_array('grafica-1', $ids)) {
            $alumnos = DB::table('sesion_alumno')
                ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
                ->join('users', 'alumnos.user_id', '=', 'users.id')
                ->select(
                    DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as nombre_completo'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('users.nombres', 'users.apellido_paterno')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get();
            
            $alumnosLabels = $alumnos->pluck('nombre_completo')->toArray();
            $alumnosValues = $alumnos->pluck('total')->toArray();
        }
        
        // Docentes con más sesiones
        $docentesLabels = [];
        $docentesValues = [];
        
        if (in_array('grafica-2', $ids)) {
            $docentes = sesiones_asesoria::select('docente_id', DB::raw('COUNT(*) as total'))
                ->with('docente')
                ->groupBy('docente_id')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get();
            
            $docentesLabels = $docentes->map(function($item) {
                return $item->docente ? $item->docente->nombres . ' ' . $item->docente->apellido_paterno : 'Sin asignar';
            })->toArray();
            $docentesValues = $docentes->pluck('total')->toArray();
        }
        
        $data = [
            'ids' => $ids,
            'alumnosLabels' => $alumnosLabels,
            'alumnosValues' => $alumnosValues,
            'docentesLabels' => $docentesLabels,
            'docentesValues' => $docentesValues,
        ];
        
        $html = $this->generarHTMLAdminPDF($data);
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('graficas_admin_' . time() . '.pdf');
    }

    private function generarHTMLAdminPDF($data)
    {
        $ids = $data['ids'];
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Gráficas - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Arial", "Helvetica", sans-serif; 
            margin: 0;
            padding: 20px;
            background: #fff;
            font-size: 11px;
        }
        .header {
            text-align: center;
            padding: 15px 0 10px 0;
            border-bottom: 2px solid #2c9f49;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #2c9f49;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            color: #888;
            font-size: 11px;
            margin-top: 3px;
        }
        .graficas-wrapper {
            max-width: 100%;
            margin: 0 auto;
        }
        .graficas-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 5px;
        }
        .graficas-grid.single {
            grid-template-columns: 1fr;
            max-width: 60%;
            margin: 0 auto;
        }
        .grafica-container {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 14px;
            background: #ffffff;
        }
        .grafica-container h2 {
            color: #2c9f49;
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #e8e8e8;
            padding-bottom: 6px;
            margin-bottom: 8px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        th {
            background-color: #f5f5f5;
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        td {
            padding: 4px 6px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
        }
        .barra-container {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .barra-fondo {
            flex: 1;
            background: #ececec;
            height: 14px;
            border-radius: 3px;
            overflow: hidden;
        }
        .barra-relleno {
            height: 100%;
            border-radius: 3px;
        }
        .barra-porcentaje {
            font-size: 9px;
            font-weight: bold;
            min-width: 32px;
            text-align: right;
            color: #555;
        }
        .total {
            font-weight: bold;
            margin-top: 5px;
            padding: 3px 8px;
            background: #f8f9fa;
            border-radius: 3px;
            text-align: right;
            font-size: 10px;
            color: #333;
        }
        .numero {
            font-weight: bold;
            color: #2c9f49;
        }
        .sin-datos {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            color: #aaa;
            font-size: 9px;
        }
        .text-muted {
            color: #999;
            font-style: italic;
            font-size: 9px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>';

        $html .= '<div class="header">
            <h1>Reporte de Gráficas - Administrador</h1>
            <p>Generado el ' . now()->format('d/m/Y H:i') . '</p>
        </div>';

        $graficasCount = count($ids);
        $gridClass = $graficasCount == 1 ? 'graficas-grid single' : 'graficas-grid';

        $html .= '<div class="graficas-wrapper">
            <div class="' . $gridClass . '">';

        // GRAFICA 1: ALUMNOS CON MAS SOLICITUDES
        if (in_array('grafica-1', $ids)) {
            $html .= '<div class="grafica-container">
                <h2>Alumnos con mas solicitudes</h2>';
            
            if (!empty($data['alumnosLabels']) && !empty($data['alumnosValues'])) {
                $total = array_sum($data['alumnosValues']);
                $html .= '<table>
                    <thead><tr><th style="width:38%;">Alumno</th><th style="width:12%;">Cant.</th><th style="width:50%;">Distribucion</th></tr></thead>
                    <tbody>';
                $maxItems = 6;
                $count = 0;
                foreach ($data['alumnosLabels'] as $index => $label) {
                    if ($count >= $maxItems) break;
                    $porcentaje = $total > 0 ? round(($data['alumnosValues'][$index] / $total) * 100, 1) : 0;
                    $color = $porcentaje > 30 ? '#2c9f49' : ($porcentaje > 15 ? '#e67e22' : '#c0392b');
                    $html .= '<tr><td><strong>' . $label . '</strong></td><td><span class="numero">' . $data['alumnosValues'][$index] . '</span></td>
                        <td><div class="barra-container"><div class="barra-fondo"><div class="barra-relleno" style="width:' . $porcentaje . '%;background:' . $color . ';"></div></div>
                        <span class="barra-porcentaje">' . $porcentaje . '%</span></div></td></tr>';
                    $count++;
                }
                if (count($data['alumnosLabels']) > $maxItems) {
                    $html .= '<tr><td colspan="3" class="text-muted text-center">Mostrando ' . $maxItems . ' de ' . count($data['alumnosLabels']) . ' registros</td></tr>';
                }
                $html .= '</tbody></table><div class="total">Total de solicitudes: <span class="numero">' . $total . '</span></div>';
            } else {
                $html .= '<div class="sin-datos">No hay datos disponibles</div>';
            }
            $html .= '</div>';
        }

        // GRAFICA 2: DOCENTES CON MAS ASESORIAS
        if (in_array('grafica-2', $ids)) {
            $html .= '<div class="grafica-container">
                <h2>Docentes con mas asesorias</h2>';
            
            if (!empty($data['docentesLabels']) && !empty($data['docentesValues'])) {
                $total = array_sum($data['docentesValues']);
                $html .= '<table>
                    <thead><tr><th style="width:38%;">Docente</th><th style="width:12%;">Cant.</th><th style="width:50%;">Distribucion</th></tr></thead>
                    <tbody>';
                $maxItems = 6;
                $count = 0;
                foreach ($data['docentesLabels'] as $index => $label) {
                    if ($count >= $maxItems) break;
                    $porcentaje = $total > 0 ? round(($data['docentesValues'][$index] / $total) * 100, 1) : 0;
                    $color = $porcentaje > 30 ? '#2c9f49' : ($porcentaje > 15 ? '#e67e22' : '#c0392b');
                    $html .= '<tr><td><strong>' . $label . '</strong></td><td><span class="numero">' . $data['docentesValues'][$index] . '</span></td>
                        <td><div class="barra-container"><div class="barra-fondo"><div class="barra-relleno" style="width:' . $porcentaje . '%;background:' . $color . ';"></div></div>
                        <span class="barra-porcentaje">' . $porcentaje . '%</span></div></td></tr>';
                    $count++;
                }
                if (count($data['docentesLabels']) > $maxItems) {
                    $html .= '<tr><td colspan="3" class="text-muted text-center">Mostrando ' . $maxItems . ' de ' . count($data['docentesLabels']) . ' registros</td></tr>';
                }
                $html .= '</tbody></table><div class="total">Total de asesorias: <span class="numero">' . $total . '</span></div>';
            } else {
                $html .= '<div class="sin-datos">No hay datos disponibles</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div></div>';

        $html .= '<div class="footer">
            Sistema de Asesorias UTN | ' . date('Y') . '
        </div>';

        $html .= '</body></html>';
        
        return $html;
    }

    // ============================================================
    // GENERAR PDF DOCENTE
    // ============================================================
    public function imprimirPDFDocente(Request $request)
    {
        $ids = $request->ids;
        $user = Auth::user();
        $docenteId = $user->id;
        
        // 1. Mis alumnos más frecuentes
        $alumnosLabels = [];
        $alumnosValues = [];
        
        if (in_array('grafica-docente-1', $ids)) {
            $alumnos = DB::table('sesion_alumno')
                ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
                ->join('users', 'alumnos.user_id', '=', 'users.id')
                ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
                ->select(
                    DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as nombre_completo'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('sesiones_asesoria.docente_id', $docenteId)
                ->groupBy('users.nombres', 'users.apellido_paterno')
                ->orderBy('total', 'DESC')
                ->limit(10)
                ->get();

            $alumnosLabels = $alumnos->pluck('nombre_completo')->toArray();
            $alumnosValues = $alumnos->pluck('total')->toArray();
        }
        
        // 2. Estado de mis sesiones
        $solicitudesLabels = [];
        $solicitudesValues = [];
        
        if (in_array('grafica-docente-2', $ids)) {
            $estados = sesiones_asesoria::select('estado', DB::raw('COUNT(*) as total'))
                ->where('docente_id', $docenteId)
                ->groupBy('estado')
                ->get();

            $solicitudesLabels = $estados->pluck('estado')->map(function($e) {
                $map = [
                    'programada' => 'Programadas',
                    'realizada' => 'Realizadas',
                    'cancelada' => 'Canceladas',
                    'pendiente' => 'Pendientes'
                ];
                return $map[$e] ?? $e;
            })->toArray();
            $solicitudesValues = $estados->pluck('total')->toArray();
        }
        
        $data = [
            'ids' => $ids,
            'alumnosLabels' => $alumnosLabels,
            'alumnosValues' => $alumnosValues,
            'solicitudesLabels' => $solicitudesLabels,
            'solicitudesValues' => $solicitudesValues,
        ];
        
        $html = $this->generarHTMLDocentePDF($data);
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('graficas_docente_' . time() . '.pdf');
    }

    private function generarHTMLDocentePDF($data)
    {
        $ids = $data['ids'];
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Gráficas - Docente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Arial", "Helvetica", sans-serif; 
            margin: 0;
            padding: 20px;
            background: #fff;
            font-size: 11px;
        }
        .header {
            text-align: center;
            padding: 15px 0 10px 0;
            border-bottom: 2px solid #2c9f49;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #2c9f49;
            font-size: 20px;
            font-weight: bold;
        }
        .header p {
            color: #888;
            font-size: 11px;
            margin-top: 3px;
        }
        .graficas-wrapper {
            max-width: 100%;
            margin: 0 auto;
        }
        .graficas-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 5px;
        }
        .graficas-grid.single {
            grid-template-columns: 1fr;
            max-width: 60%;
            margin: 0 auto;
        }
        .grafica-container {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 14px;
            background: #ffffff;
        }
        .grafica-container h2 {
            color: #2c9f49;
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #e8e8e8;
            padding-bottom: 6px;
            margin-bottom: 8px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        th {
            background-color: #f5f5f5;
            padding: 4px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            color: #333;
        }
        td {
            padding: 4px 6px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10px;
        }
        .barra-container {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .barra-fondo {
            flex: 1;
            background: #ececec;
            height: 14px;
            border-radius: 3px;
            overflow: hidden;
        }
        .barra-relleno {
            height: 100%;
            border-radius: 3px;
        }
        .barra-porcentaje {
            font-size: 9px;
            font-weight: bold;
            min-width: 32px;
            text-align: right;
            color: #555;
        }
        .total {
            font-weight: bold;
            margin-top: 5px;
            padding: 3px 8px;
            background: #f8f9fa;
            border-radius: 3px;
            text-align: right;
            font-size: 10px;
            color: #333;
        }
        .numero {
            font-weight: bold;
            color: #2c9f49;
        }
        .sin-datos {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            color: #aaa;
            font-size: 9px;
        }
        .text-muted {
            color: #999;
            font-style: italic;
            font-size: 9px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>';

        $html .= '<div class="header">
            <h1>Reporte de Gráficas - Docente</h1>
            <p>Generado el ' . now()->format('d/m/Y H:i') . '</p>
        </div>';

        $graficasCount = count($ids);
        $gridClass = $graficasCount == 1 ? 'graficas-grid single' : 'graficas-grid';

        $html .= '<div class="graficas-wrapper">
            <div class="' . $gridClass . '">';

        // GRAFICA 1: MIS ALUMNOS MAS FRECUENTES
        if (in_array('grafica-docente-1', $ids)) {
            $html .= '<div class="grafica-container">
                <h2>Mis alumnos mas frecuentes</h2>';
            
            if (!empty($data['alumnosLabels']) && !empty($data['alumnosValues'])) {
                $total = array_sum($data['alumnosValues']);
                $html .= '<table>
                    <thead><tr><th style="width:38%;">Alumno</th><th style="width:12%;">Cant.</th><th style="width:50%;">Distribucion</th></tr></thead>
                    <tbody>';
                $maxItems = 6;
                $count = 0;
                foreach ($data['alumnosLabels'] as $index => $label) {
                    if ($count >= $maxItems) break;
                    $porcentaje = $total > 0 ? round(($data['alumnosValues'][$index] / $total) * 100, 1) : 0;
                    $color = $porcentaje > 30 ? '#2c9f49' : ($porcentaje > 15 ? '#e67e22' : '#c0392b');
                    $html .= '<tr><td><strong>' . $label . '</strong></td><td><span class="numero">' . $data['alumnosValues'][$index] . '</span></td>
                        <td><div class="barra-container"><div class="barra-fondo"><div class="barra-relleno" style="width:' . $porcentaje . '%;background:' . $color . ';"></div></div>
                        <span class="barra-porcentaje">' . $porcentaje . '%</span></div></td></tr>';
                    $count++;
                }
                if (count($data['alumnosLabels']) > $maxItems) {
                    $html .= '<tr><td colspan="3" class="text-muted text-center">Mostrando ' . $maxItems . ' de ' . count($data['alumnosLabels']) . ' registros</td></tr>';
                }
                $html .= '</tbody></table><div class="total">Total de asesorias: <span class="numero">' . $total . '</span></div>';
            } else {
                $html .= '<div class="sin-datos">No hay datos disponibles</div>';
            }
            $html .= '</div>';
        }

        // GRAFICA 2: ESTADO DE MIS SOLICITUDES
        if (in_array('grafica-docente-2', $ids)) {
            $html .= '<div class="grafica-container">
                <h2>Estado de mis solicitudes</h2>';
            if (!empty($data['solicitudesLabels']) && !empty($data['solicitudesValues'])) {
                $total = array_sum($data['solicitudesValues']);
                $html .= '<table>
                    <thead><tr><th style="width:38%;">Estado</th><th style="width:12%;">Cant.</th><th style="width:50%;">Distribucion</th></tr></thead>
                    <tbody>';
                $maxItems = 6;
                $count = 0;
                foreach ($data['solicitudesLabels'] as $index => $label) {
                    if ($count >= $maxItems) break;
                    $porcentaje = $total > 0 ? round(($data['solicitudesValues'][$index] / $total) * 100, 1) : 0;
                    $color = $porcentaje > 30 ? '#2c9f49' : ($porcentaje > 15 ? '#e67e22' : '#c0392b');
                    $html .= '<tr><td><strong>' . $label . '</strong></td><td><span class="numero">' . $data['solicitudesValues'][$index] . '</span></td>
                        <td><div class="barra-container"><div class="barra-fondo"><div class="barra-relleno" style="width:' . $porcentaje . '%;background:' . $color . ';"></div></div>
                        <span class="barra-porcentaje">' . $porcentaje . '%</span></div></td></tr>';
                    $count++;
                }
                if (count($data['solicitudesLabels']) > $maxItems) {
                    $html .= '<tr><td colspan="3" class="text-muted text-center">Mostrando ' . $maxItems . ' de ' . count($data['solicitudesLabels']) . ' registros</td></tr>';
                }
                $html .= '</tbody></table><div class="total">Total: <span class="numero">' . $total . '</span></div>';
            } else {
                $html .= '<div class="sin-datos">No hay datos disponibles</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div></div>';

        $html .= '<div class="footer">
            Sistema de Asesorias UTN | ' . date('Y') . '
        </div>';

        $html .= '</body></html>';
        
        return $html;
    }
}