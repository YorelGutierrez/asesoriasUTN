<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\logs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RespaldoController extends Controller
{
    /**
     * Dashboard principal (usa este en tus rutas)
     */
    public function dashboard()
    {
        //contador de usuarios
        $totalAdministradores = User::where('rol', 'admin')->count();
        $totalDocentes = User::where('rol', 'docente')->count();
        $totalTutores = User::where('rol', 'tutor')->count();
        $totalAlumnos = User::where('rol', 'alumno')->count();

        // RESPALDOS 
        $archivos = File::files(storage_path('app/respaldo'));

        $ultimo = null;

        if (!empty($archivos)) {
            $ultimoArchivo = collect($archivos)->sortByDesc(function ($file) {
                return $file->getCTime();
            })->first();

            // Obtener zona horaria de la aplicación
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
                    // Si falla el parseo, mostrar la fecha sin formato
                    $horaProgramada = $config['fecha'];
                }
            }
        }

        //logs
        $logs = logs::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('ultimo', 'horaProgramada', 'logs', 'totalAdministradores', 'totalDocentes', 'totalTutores', 'totalAlumnos'));
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
            // Registrar log
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

        // Registrar log
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

        // Registrar log
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

        // Ordenar por fecha descendente (más reciente primero)
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
            // Configurar base de datos
            $database = env('DB_DATABASE');
            $user = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');

            // Comando para restaurar (usando mysql)
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
