<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\logs;
use App\Models\User;
use App\Models\alumnos;
use App\Models\docentes;
use App\Models\materias;
use App\Models\solicitudes_asesoria;
use App\Models\sesiones_asesoria;
use App\Models\acuerdos_asesoria;
use App\Models\historial_academico;
use App\Models\grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RespaldoController extends Controller
{
    /**
     * Dashboard principal (usa este en tus rutas)
     */
    public function dashboard()
    {
        // ============================================================
        // 1. CONTADOR DE USUARIOS
        // ============================================================
        $totalAdministradores = User::where('rol', 'admin')->count();
        $totalDocentes = User::where('rol', 'docente')->count();
        $totalTutores = User::where('rol', 'tutor')->count();
        $totalAlumnos = User::where('rol', 'alumno')->count();

        // ============================================================
        // 2. RESPALDOS
        // ============================================================
        $archivos = File::files(storage_path('app/respaldo'));

        $ultimo = null;

        if (!empty($archivos)) {
            $ultimoArchivo = collect($archivos)->sortByDesc(function ($file) {
                return $file->getCTime();
            })->first();

            $timezone = config('app.timezone', date_default_timezone_get());

            $ultimo = [
                'nombre' => $ultimoArchivo->getFilename(),
                'fecha' => Carbon::createFromTimestamp($ultimoArchivo->getCTime())
                    ->timezone($timezone)
                    ->format('d/m/Y - h:i A')
            ];
        }

        $configPath = storage_path('app/respaldo_config.json');
        $horaProgramada = null;
        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true);
            if (isset($config['fecha'])) {
                $timezone = config('app.timezone', date_default_timezone_get());
                try {
                    $horaProgramada = Carbon::parse($config['fecha'])
                        ->timezone($timezone)
                        ->format('d/m/Y - h:i A');
                } catch (\Exception $e) {
                    $horaProgramada = $config['fecha'];
                }
            }
        }

        // ============================================================
        // 3. LOGS
        // ============================================================
        $logs = logs::with('user')->latest()->take(10)->get();

        // ============================================================
        // 4. DATOS PARA GRÁFICAS
        // ============================================================

        // 4.1 Solicitudes por estado
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

        // 4.2 Solicitudes por mes
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

        // 4.3 Alumnos con más sesiones
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

        // 4.4 Docentes con más sesiones
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

        // 4.5 Solicitudes por día
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

        // 4.6 Acuerdos registrados
        $resultados = acuerdos_asesoria::select('acuerdo', DB::raw('COUNT(*) as total'))
            ->whereNotNull('acuerdo')
            ->groupBy('acuerdo')
            ->get();

        $resultadosLabels = $resultados->pluck('acuerdo');
        $resultadosValues = $resultados->pluck('total');

        // 4.7 Sesiones por tipo
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

        // 4.8 Materias con más reprobadas
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

        // ============================================================
        // 5. ENVIAR A LA VISTA (CON TODOS LOS DATOS)
        // ============================================================
        return view('admin.dashboard', compact(
            'ultimo',
            'horaProgramada',
            'logs',
            'totalAdministradores',
            'totalDocentes',
            'totalTutores',
            'totalAlumnos',
            // Variables de gráficas
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

    /**
     * Generar respaldo manual
     */
    public function generate()
    {
        $carpeta = storage_path('app/respaldo');

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombre = 'respaldo_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $ruta = $carpeta . '/' . $nombre;

        $database = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        $command = "mysqldump --user={$user} --password={$password} {$database} > {$ruta}";

        system($command, $resultado);

        if ($resultado === 0) {
            registrar_log('CREAR', 'Respaldo generado: ' . $nombre, 'respaldos');
            return back()->with('respaldo_success', 'Respaldo generado correctamente');
        } else {
            return back()->with('error', 'Error al generar el respaldo');
        }
    }

    /**
     * Guardar configuración de respaldo automático
     */
    public function automatico(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        File::put(storage_path('app/respaldo_config.json'), json_encode([
            'fecha' => $request->fecha
        ]));

        registrar_log('PROGRAMAR', 'Respaldo programado para: ' . $request->fecha, 'respaldos');

        return response()->json([
            'success' => true,
            'message' => 'Respaldo programado correctamente'
        ]);
    }

    /**
     * Descargar respaldo
     */
    public function download($archivo)
    {
        $ruta = storage_path('app/respaldo/' . $archivo);

        if (!file_exists($ruta)) {
            abort(404);
        }

        registrar_log('DESCARGAR', 'Respaldo descargado: ' . $archivo, 'respaldos');

        return response()->download($ruta);
    }

    /**
     * Lista los respaldos disponibles para restaurar.
     */
    public function listar()
    {
        $archivos = File::files(storage_path('app/respaldo'));
        $respaldos = [];

        foreach ($archivos as $archivo) {
            $respaldos[] = [
                'nombre' => $archivo->getFilename(),
                'fecha' => Carbon::createFromTimestamp($archivo->getCTime())->format('d/m/Y H:i:s'),
                'tamano' => $this->formatSizeUnits($archivo->getSize()),
                'timestamp' => $archivo->getCTime(),
            ];
        }

        usort($respaldos, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return response()->json($respaldos);
    }

    /**
     * Restaura un respaldo seleccionado.
     */
    public function restaurar(Request $request)
    {
        $archivo = $request->archivo;
        $ruta = storage_path('app/respaldo/' . $archivo);

        if (!file_exists($ruta)) {
            return response()->json([
                'success' => false,
                'message' => 'El archivo de respaldo no existe.'
            ], 404);
        }

        try {
            $database = env('DB_DATABASE');
            $user = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');

            $command = "mysql --host={$host} --port={$port} --user={$user} --password={$password} {$database} < {$ruta}";

            system($command, $resultado);

            if ($resultado === 0) {
                registrar_log('RESTAURAR', 'Respaldo restaurado: ' . $archivo, 'respaldos');
                return response()->json([
                    'success' => true,
                    'message' => 'Base de datos restaurada correctamente desde: ' . $archivo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al restaurar la base de datos. Código: ' . $resultado
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Formatea el tamaño de un archivo en unidades legibles.
     */
    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
}