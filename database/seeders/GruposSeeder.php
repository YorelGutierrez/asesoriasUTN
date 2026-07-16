<?php

namespace Database\Seeders;

use App\Models\carreras;
use App\Models\grupos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GruposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carrera = carreras::where('clave', 'ITIID')->first();

        if (!$carrera) {
            $this->command->error('No se encontró la carrera IDGS. Ejecuta primero CarrerasSeeder.');
            return;
        }

        $grupos = [
            ['nombre' => 'IDGS-91', 'cuatrimestre' => 9, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-92', 'cuatrimestre' => 9, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-93', 'cuatrimestre' => 9, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-94', 'cuatrimestre' => 9, 'carrera_id' => $carrera->id],
        ];

        foreach ($grupos as $g) {
            grupos::firstOrCreate(['nombre' => $g['nombre']], $g);
        }
    }
}
