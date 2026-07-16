<?php

namespace Database\Seeders;

use App\Models\alumnos;
use App\Models\grupos;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos del administrador
        $adminData = [
            'nombres' => 'Admin',
            'apellido_paterno' => 'Sistema',
            'apellido_materno' => 'Principal',
            'nickname' => 'admin',
            'email' => 'admin@utnay.edu.mx',
            'password' => Hash::make('12345678'),
            'edad' => '30',
            'fecha_nacimiento' => '1990-01-01',
            'telefono' => '123456789',
            'foto_perfil' => null,
            'rol' => 'admin', 
            'estado' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $existingUser = User::where('email', $adminData['email'])
                            ->orWhere('nickname', $adminData['nickname'])
                            ->first();

        if (!$existingUser) {
            User::create($adminData);
            $this->command->info('Usuario administrador creado exitosamente.');
        } else {
            $this->command->warn('El usuario administrador ya existe. No se insertó duplicado.');
        }

        // =============================================
        // ALUMNOS
        // Formato: [grupo, matricula, nombres, apellido_paterno, apellido_materno]
        // Email:    matricula_en_minusculas@utnay.edu.mx
        // Password: matricula_en_minusculas
        // =============================================
        $alumnosData = [
            // ── IDGS-91 ──────────────────────────────────────────────────────
            ['IDGS-91', 'TIC-310113', 'Ricardo',            'Andrade',    'Carlos'],
            ['IDGS-91', 'TIC-310060', 'Jose Ramon',         'Avalos',     'Zendejas'],
            ['IDGS-91', 'TIC-310095', 'Samantha Milliani',  'Beltran',    'Peña'],
            ['IDGS-91', 'TIC-300026', 'José Eduardo',       'Cervantes',  'Ramirez'],
            ['IDGS-91', 'TIC-310147', 'Dayron Antonio',     'Covarrubias','Garcia'],
            ['IDGS-91', 'TIC-310099', 'Angela Ailin',       'Fernandez',  'Lopez'],
            ['IDGS-91', 'TIC-310155', 'Edwin Julian',       'Garcia',     'Medina'],
            ['IDGS-91', 'TIC-300012', 'Fernanda',           'Gonzalez',   'Ruelas'],
            ['IDGS-91', 'TIC-310134', 'Luis Daniel',        'Lopez',      'Cabrera'],
            ['IDGS-91', 'TIC-310185', 'Kevin Arturo',       'Martinez',   'Elias'],
            ['IDGS-91', 'TIC-310184', 'Jonathan Alexis',    'Mora',       'Yañez'],
            ['IDGS-91', 'TIC-300083', 'Juan Fabian',        'Moreno',     'Lopez'],
            ['IDGS-91', 'TIC-310123', 'Adrian De Jesus',    'Perez',      'Arias'],
            ['IDGS-91', 'TIC-310042', 'Gabriel Alejandro',  'Ramirez',    'Serna'],
            ['IDGS-91', 'TIC-310010', 'Alan Andres',        'Regino',     'Ines'],
            ['IDGS-91', 'TIC-310182', 'David Arturo',       'Reyna',      'Villanueva'],
            ['IDGS-91', 'TIC-310150', 'Jorge Alexander',    'Robles',     'Ramirez'],
            ['IDGS-91', 'TIC-310001', 'Katherine Jais',     'Rubio',      'Romero'],
            ['IDGS-91', 'TIC-310071', 'Miguel Angel',       'Sandoval',   'Guardado'],
            ['IDGS-91', 'TIC-310046', 'Edgar Gabriel',      'Segura',     'Hernandez'],
            ['IDGS-91', 'TIC-310142', 'Jorge Alejandro',    'Vazquez',    'Cortez'],
            ['IDGS-91', 'TIC-310009', 'Enrique Gael',       'Zamora',     'Partida'],
 
            // ── IDGS-92 ──────────────────────────────────────────────────────
            ['IDGS-92', 'TIC-310173', 'Jose Manuel',        'Aguilar',    'Nuñez'],
            ['IDGS-92', 'TIC-310035', 'Xandier Daniel',     'Aguilar',    'Osuna'],
            ['IDGS-92', 'TIC-310012', 'Eimy Eileen',        'Aranda',     'Martinez'],
            ['IDGS-92', 'TIC-310153', 'Rafael Humberto',    'Avila',      'Rios'],
            ['IDGS-92', 'TIC-310054', 'Nephtis Adonahi',    'Cañedo',     'Segura'],
            ['IDGS-92', 'TIC-310029', 'Brandon Josue',      'De La Paz',  'Venegas'],
            ['IDGS-92', 'TIC-310049', 'Jesus Gabriel',      'Esparza',    'Burgara'],
            ['IDGS-92', 'TIC-310131', 'Diego Sebastian',    'Flores',     'Luna'],
            ['IDGS-92', 'TIC-310089', 'Joana Michelle',     'Gasga',      'Garcia'],
            ['IDGS-92', 'TIC-310040', 'Luis Ricardo',       'Gomez',      'Nava'],
            ['IDGS-92', 'TIC-310091', 'Karol Emmanuel',     'Gonzalez',   'Torres'],
            ['IDGS-92', 'TIC-310148', 'Carlos Eduardo',     'Lopez',      'Castillo'],
            ['IDGS-92', 'TIC-310011', 'Alan Emir',          'Medina',     'Delgado'],
            ['IDGS-92', 'TIC-310195', 'Alex Gilberto',      'Morales',    'Bañuelos'],
            ['IDGS-92', 'TIC-310167', 'Jesus Antonio',      'Ornelas',    'Gonzalez'],
            ['IDGS-92', 'TIC-300099', 'Karla Yadira',       'Ozuna',      'Aguilar'],
            ['IDGS-92', 'TIC-310068', 'Julio Javier',       'Perez',      'Ruiz'],
            ['IDGS-92', 'TIC-310059', 'Aldair Alejandro',   'Ramos',      'Diaz'],
            ['IDGS-92', 'TIC-310192', 'Jesus Emmanuel',     'Rodriguez',  'De La Cruz'],
            ['IDGS-92', 'TIC-310196', 'Maximiliano',        'Ruiz',       'Encarnacion'],
            ['IDGS-92', 'TIC-310137', 'Jose Armando',       'Topete',     'Fregoso'],
            ['IDGS-92', 'TIC-310156', 'Raul Mauricio',      'Velasco',    'Sanchez'],
            ['IDGS-92', 'TIC-310088', 'Jazmin Lizeth',      'Zepeda',     'Aguilar'],
 
            // ── IDGS-93 ──────────────────────────────────────────────────────
            ['IDGS-93', 'TIC-310072', 'Alain Javier',       'Araujo',     'Robledo'],
            ['IDGS-93', 'TIC-310166', 'Roman Alexis',       'Bañuelos',   'Vizcarra'],
            ['IDGS-93', 'TIC-310002', 'Diana Laura',        'Bernal',     'Arias'],
            ['IDGS-93', 'TIC-310048', 'Alondra Guadalupe',  'Cisneros',   'Macias'],
            ['IDGS-93', 'TIC-310085', 'Cesar Andres',       'Diaz',       'Hernandez'],
            ['IDGS-93', 'TIC-310097', 'Emiliano',           'Estrada',    'Parra'],
            ['IDGS-93', 'TIC-310143', 'Kervin Geovanni',    'Flores',     'Ochoa'],
            ['IDGS-93', 'TIC-310083', 'Juansis Eduardo',    'Hernandez',  'Mayorga'],
            ['IDGS-93', 'TIC-310047', 'Christopher Wilfrido','Lopez',     'Raygoza'],
            ['IDGS-93', 'TIC-310104', 'Gilberto Alonso',    'Mendoza',    'Salas'],
            ['IDGS-93', 'TIC-310190', 'Pedro Vladimir',     'Montes',     'Montes'],
            ['IDGS-93', 'TIC-310025', 'Anel Elizabeth',     'Moreno',     'Avalos'],
            ['IDGS-93', 'TIC-310160', 'Kevin Abraham',      'Palomar',    'Macias'],
            ['IDGS-93', 'TIC-310114', 'Christopher Martin', 'Plascencia', 'Dominguez'],
            ['IDGS-93', 'TIC-310116', 'Yoel Guadalupe',     'Ramos',      'Rivera'],
            ['IDGS-93', 'TIC-310067', 'Jose Manuel',        'Rivas',      'Sierra'],
            ['IDGS-93', 'TIC-260053', 'Gerardo Alberto',    'Rodriguez',  'Millan'],
            ['IDGS-93', 'TIC-310168', 'Sherlyn Vanessa',    'Rosales',    'Garcia'],
            ['IDGS-93', 'TIC-310094', 'Gilberto',           'Ruiz',       'Mendoza'],
            ['IDGS-93', 'TIC-310102', 'Jose Carlos',        'Topete',     'Sanchez'],
            ['IDGS-93', 'TIC-310037', 'Axel',               'Velazquez',  'Meza'],
 
            // ── IDGS-94 ──────────────────────────────────────────────────────
            ['IDGS-94', 'TIC-310120', 'Alexis Ariel',       'Alvarado',   'Rodriguez'],
            ['IDGS-94', 'TIC-310103', 'Fernanda Dalet',     'Arce',       'Rosales'],
            ['IDGS-94', 'TIC-310020', 'Erick Geovanny',     'Barajas',    'Rosales'],
            ['IDGS-94', 'TIC-310188', 'Brandon Eduardo',    'Bernal',     'Hernandez'],
            ['IDGS-94', 'TIC-300089', 'Christopher Johan',  'Cocco',      'Malagon'],
            ['IDGS-94', 'TIC-310022', 'Alain Antonio',      'Corona',     'Perez'],
            ['IDGS-94', 'TIC-310027', 'Victor Manuel',      'Diaz',       'Herrera'],
            ['IDGS-94', 'TIC-310128', 'Bertha Odalys',      'Garcia',     'Correa'],
            ['IDGS-94', 'TIC-312001', 'Jahir',              'Garcia',     'Macias'],
            ['IDGS-94', 'TIC-310003', 'Alexandra Rubi',     'Gonzalez',   'Lares'],
            ['IDGS-94', 'TIC-310163', 'Roque Joseph',       'Guerrero',   'Ponce'],
            ['IDGS-94', 'TIC-310151', 'Nelly Jarei',        'Gutierrez',  'Ruelas'],
            ['IDGS-94', 'TIC-310036', 'Yorel Isai',         'Gutierrez',  'Zepeda'],
            ['IDGS-94', 'TIC-310019', 'Cristopher',         'Larios',     'Garcia'],
            ['IDGS-94', 'TIC-300133', 'Crystopher',         'Marrujo',    'Arellano'],
            ['IDGS-94', 'TIC-300170', 'Antonio Damian',     'Navarro',    'Lopez'],
            ['IDGS-94', 'TIC-310121', 'Jorge Gabriel',      'Peña',       'Arvizu'],
            ['IDGS-94', 'TIC-310055', 'Danna Giselle',      'Ramirez',    'Abrego'],
            ['IDGS-94', 'TIC-310016', 'Julissa Anahy',      'Raygosa',    'Curiel'],
            ['IDGS-94', 'TIC-310073', 'Vanessa De Jesus',   'Rivera',     'Orozco'],
            ['IDGS-94', 'TIC-310007', 'Andy Alexander',     'Samaniego',  'De Leon'],
            ['IDGS-94', 'TIC-310087', 'Jeshua Miguel',      'Segundo',    'Lara'],
            ['IDGS-94', 'TIC-310187', 'Emmanuel',           'Torres',     'Rodriguez'],
            ['IDGS-94', 'TIC-310178', 'Alfonso Alejandro',  'Wu',         'Barocio'],
        ];
 
        $creados  = 0;
        $omitidos = 0;
 
        foreach ($alumnosData as [$grupoNombre, $matricula, $nombres, $ap, $am]) {
            $grupo = grupos::where('nombre', $grupoNombre)->first();
 
            if (!$grupo) {
                $this->command->warn("⚠ Grupo '$grupoNombre' no encontrado — alumno $matricula omitido.");
                $omitidos++;
                continue;
            }
 
            $email = strtolower($matricula) . '@utnay.edu.mx';
 
            if (User::where('email', $email)->exists()) {
                $omitidos++;
                continue;
            }
 
            DB::transaction(function () use ($nombres, $ap, $am, $email, $matricula, $grupo) {
                $user = User::create([
                    'nombres'           => $nombres,
                    'apellido_paterno'  => $ap,
                    'apellido_materno'  => $am,
                    'nickname'          => null,
                    'email'             => $email,
                    'password'          => Hash::make(strtolower($matricula)),
                    'edad'              => null,
                    'fecha_nacimiento'  => null,
                    'telefono'          => null,
                    'foto_perfil'       => null,
                    'rol'               => 'alumno',
                    'estado'            => true,
                    'email_verified_at' => now(),
                ]);
 
                alumnos::create([
                    'user_id'          => $user->id,
                    'matricula'        => $matricula,
                    'grupo_id'         => $grupo->id,
                    'carrera_id'       => 8,
                    'cuatrimestre'     => 9,
                    'status_academico' => null,
                ]);
            });
 
            $creados++;
        }
 
        $this->command->info("✓ Alumnos creados: $creados | Omitidos: $omitidos");
    }
}
