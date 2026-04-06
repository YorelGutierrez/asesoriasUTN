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
            ['nombre' => 'IDGS-81', 'cuatrimestre' => 8, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-82', 'cuatrimestre' => 6, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-83', 'cuatrimestre' => 4, 'carrera_id' => $carrera->id],
            ['nombre' => 'IDGS-84', 'cuatrimestre' => 2, 'carrera_id' => $carrera->id],
        ];

        foreach ($grupos as $g) {
            grupos::firstOrCreate(['nombre' => $g['nombre']], $g);
        }
    }
}
