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
            ['nombre' => 'Inglés VII', 'clave' => 'ING7'],
            ['nombre' => 'Matemáticas para ingeniería II', 'clave' => 'MATII'],
            ['nombre' => 'Administración de Base de Datos', 'clave' => 'BD'],
            ['nombre' => 'Desarrollo Web', 'clave' => 'WEB'],
            ['nombre' => 'Seguridad de Aplicaciones', 'clave' => 'SEG'],
            ['nombre' => 'Planificación y Organización de Trabajo', 'clave' => 'PYO'],
            ['nombre' => 'Estructura de Datos', 'clave' => 'ED'],
        ];

        foreach ($materias as $m) {
            materias::firstOrCreate(['clave' => $m['clave']], $m);
        }
    }
}
