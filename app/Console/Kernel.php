<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $configPath = storage_path('app/respaldo_config.json');

            if (!file_exists($configPath)) return;

            $config = json_decode(file_get_contents($configPath), true);

            if (!isset($config['fecha'])) return;

            $fechaProgramada = strtotime($config['fecha']);

            // margen de 60 segundos
            if (abs(time() - $fechaProgramada) <= 60) {

                app(\App\Http\Controllers\admin\RespaldoController::class)->generate();

                // 🔥 OPCIONAL: borrar programación después de ejecutar
                unlink($configPath);
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
