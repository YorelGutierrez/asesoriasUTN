<?php

namespace Database\Seeders;

use App\Models\materias;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MateriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            ['nombre' => 'Inglés VIII', 'clave' => 'ING8'],
            ['nombre' => 'Desarrollo de equipos de alto rendimiento', 'clave' => 'DEAR'],
            ['nombre' => 'Desarrollo para dispositivos Moviles', 'clave' => 'DDM'],
            ['nombre' => 'Desarrollo Web', 'clave' => 'WEB'],
            ['nombre' => 'Administración de proyectos', 'clave' => 'ADMP'],
            ['nombre' => 'Extracción de contenido en base de datos', 'clave' => 'EXBD'],
        ];

        foreach ($materias as $m) {
            materias::firstOrCreate(['clave' => $m['clave']], $m);
        }
    }
}
