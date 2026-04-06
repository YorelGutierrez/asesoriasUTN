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
            ['nombre' => 'TSU en inteligencia artificial', 'clave' => 'IA', 'logo' => '/img/carreras/IA-DIxRWf-C.png'],
            ['nombre' => 'Ingeniería en alimentos', 'clave' => 'IAL', 'logo' => '/img/carreras/IAL-D7vwp92R.png'],
            ['nombre' => 'Ingeniería en civil', 'clave' => 'IC', 'logo' => '/img/carreras/IC-DVbWQOvg.png'],
            ['nombre' => 'Ingeniería en logística internacional', 'clave' => 'ILI', 'logo' => '/img/carreras/ILI-Be-QpSkC.png'],
            ['nombre' => 'Ingeniería en mantenimineto industrial', 'clave' => 'IMI', 'logo' => '/img/carreras/IMI-B-buC3Pg.png'],
            ['nombre' => 'Ingeniería en microelectrónica y semiconductores', 'clave' => 'IMS', 'logo' => '/img/carreras/IMS-DWpSJ3cI.png'],
            ['nombre' => 'Ingenieria en mecatronica', 'clave' => 'IMT', 'logo' => '/img/carreras/IMT-Ch5y60fw.png'],
            ['nombre' => 'ingenieria en tecnologías de la información e innovación digital', 'clave' => 'ITIID', 'logo' => '/img/carreras/ITIID-DDH-gJkG.png'],
            ['nombre' => 'Licenciatura en administración', 'clave' => 'LAD', 'logo' => '/img/carreras/LAD-Cek-Xcxa.png'],
            ['nombre' => 'Licenciatura en gestión y desarrollo turístico', 'clave' => 'LGDT', 'logo' => '/img/carreras/LGDT-D7fFmQRl.png'],
            ['nombre' => 'Licenciatura en gastronomía', 'clave' => 'LGT', 'logo' => '/img/carreras/LGT-CdJNfZHA.png'],
            ['nombre' => 'Licenciatura en negocios y mercadotecnia', 'clave' => 'LINM', 'logo' => '/img/carreras/LINM-LAz2BrcJ.png'],
            ['nombre' => 'Licenciatura en psicología', 'clave' => 'LPS', 'logo' => '/img/carreras/LPS-CBEmlYDO.png'],
            ['nombre' => 'Licenciatura en seguridad pública', 'clave' => 'LSP', 'logo' => '/img/carreras/LSP-D0qqiMn7.png'],
        ];

        foreach ($carreras as $c) {
            carreras::firstOrCreate(['clave' => $c['clave']], $c);
        }
    }
}
