<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\carreras;

class CarrerasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = [
            ['nombre' => 'Ingeniería en Desarrollo y Gestión de Software', 'clave' => 'IDGS', 'logo' => 'https://www.utnay.edu.mx/assets/IDGS.png'],
            ['nombre' => 'Ingeniería en Redes', 'clave' => 'IR', 'logo' => 'https://www.utnay.edu.mx/assets/IR.png'],
            ['nombre' => 'Ingeniería en Energías Renovables', 'clave' => 'IER', 'logo' => 'https://www.utnay.edu.mx/assets/IER.png'],
            ['nombre' => 'Ingeniería en Electrónica', 'clave' => 'IE', 'logo' => 'https://www.utnay.edu.mx/assets/IE.png'],
            ['nombre' => 'Ingeniería en Mecatrónica', 'clave' => 'IM', 'logo' => 'https://www.utnay.edu.mx/assets/IM.png'],
            ['nombre' => 'Ingeniería en Biotecnología', 'clave' => 'IB', 'logo' => 'https://www.utnay.edu.mx/assets/IB.png'],
            ['nombre' => 'Licenciatura en Administración', 'clave' => 'LA', 'logo' => 'https://www.utnay.edu.mx/assets/LA.png'],
            ['nombre' => 'Licenciatura en Contaduría', 'clave' => 'LC', 'logo' => 'https://www.utnay.edu.mx/assets/LC.png'],
            ['nombre' => 'Licenciatura en Mercadotecnia', 'clave' => 'LM', 'logo' => 'https://www.utnay.edu.mx/assets/LM.png'],
            ['nombre' => 'Licenciatura en Gastronomía', 'clave' => 'LG', 'logo' => 'https://www.utnay.edu.mx/assets/LG.png'],
            ['nombre' => 'Licenciatura en Turismo', 'clave' => 'LT', 'logo' => 'https://www.utnay.edu.mx/assets/LT.png'],
            ['nombre' => 'Técnico Superior Universitario en Tecnologías de la Información', 'clave' => 'TSU-TI', 'logo' => 'https://www.utnay.edu.mx/assets/TSU-TI.png'],
            ['nombre' => 'Técnico Superior Universitario en Energías Renovables', 'clave' => 'TSU-ER', 'logo' => 'https://www.utnay.edu.mx/assets/TSU-ER.png'],
            ['nombre' => 'Técnico Superior Universitario en Procesos Alimentarios', 'clave' => 'TSU-PA', 'logo' => 'https://www.utnay.edu.mx/assets/TSU-PA.png'],
        ];

        foreach ($carreras as $c) {
            carreras::firstOrCreate(['clave' => $c['clave']], $c);
        }
    }
}
