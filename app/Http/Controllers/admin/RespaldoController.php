<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class RespaldoController extends Controller
{
    /**
     * Dashboard principal (usa este en tus rutas)
     */
    public function dashboard()
    {
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

        return view('admin.dashboard', compact('ultimo', 'horaProgramada'));
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
            return back()->with('success', 'Respaldo generado correctamente');
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

        return response()->json(['success' => true]);
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

        return response()->download($ruta);
    }
}
