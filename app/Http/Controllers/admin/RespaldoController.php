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
}