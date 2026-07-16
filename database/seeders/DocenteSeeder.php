<?php

namespace Database\Seeders;

use App\Models\docentes;
use App\Models\materias;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =============================================
        // DATOS DE DOCENTES
        // [nombres, apellido_paterno, apellido_materno, email, clave_empleado, [materias]]
        // =============================================
        $docentesData = [
            [
                'nombres'          => 'Stephany',
                'apellido_paterno' => 'Lopez',
                'apellido_materno' => '',
                'email'            => 'stephany@utnay.edu.mx',
                'numero_empleado'  => 'STEP-001',
                'materias'         => ['WEB'],   // Desarrollo Web
            ],
            [
                'nombres'          => 'Juan Manuel',
                'apellido_paterno' => 'Tovar',
                'apellido_materno' => '',
                'email'            => 'juan@utnay.edu.mx',
                'numero_empleado'  => 'JUAN-002',
                'materias'         => ['EXBD'],  // Extracción de contenido en BD
            ],
            [
                'nombres'          => 'Marylin',
                'apellido_paterno' => 'Velazquez',
                'apellido_materno' => '',
                'email'            => 'mary@utnay.edu.mx',
                'numero_empleado'  => 'MARY-003',
                'materias'         => ['ING8'],  // Inglés VIII
            ],
            [
                'nombres'          => 'Daniela',
                'apellido_paterno' => 'Viramontes',
                'apellido_materno' => '',
                'email'            => 'daniela@utnay.edu.mx',
                'numero_empleado'  => 'DANI-004',
                'materias'         => ['DEAR'],  // Desarrollo de equipos de alto rendimiento
            ],
            [
                'nombres'          => 'Leonardo',
                'apellido_paterno' => 'Guerra',
                'apellido_materno' => '',
                'email'            => 'leonardo@utnay.edu.mx',
                'numero_empleado'  => 'LEON-005',
                'materias'         => ['ADMP'],  // Administración de proyectos
            ],
            [
                'nombres'          => 'Oscar',
                'apellido_paterno' => 'Arenas',
                'apellido_materno' => '',
                'email'            => 'oscar@utnay.edu.mx',
                'numero_empleado'  => 'OSCA-006',
                'materias'         => ['DDM'],   // Desarrollo para dispositivos móviles
            ],
        ];

        $creados  = 0;
        $omitidos = 0;

        foreach ($docentesData as $data) {
            // Evitar duplicados por email o número de empleado
            if (User::where('email', $data['email'])->exists()) {
                $this->command->warn("⚠ {$data['email']} ya existe, se omitió.");
                $omitidos++;
                continue;
            }

            DB::transaction(function () use ($data, &$creados) {
                // 1. Crear user
                $user = User::create([
                    'nombres'           => $data['nombres'],
                    'apellido_paterno'  => $data['apellido_paterno'],
                    'apellido_materno'  => $data['apellido_materno'],
                    'nickname'          => null,
                    'email'             => $data['email'],
                    'password'          => Hash::make('12345678'),
                    'edad'              => null,
                    'fecha_nacimiento'  => null,
                    'telefono'          => null,
                    'foto_perfil'       => null,
                    'rol'               => 'docente',
                    'estado'            => true,
                    'email_verified_at' => now(),
                ]);

                // 2. Crear registro en docentes (docente_id en docente_materias → users.id)
                docentes::create([
                    'user_id'          => $user->id,
                    'numero_empleado'  => $data['numero_empleado'],
                    'departamento'     => null,
                    'carrera_id'       => 8,
                ]);

                // 3. Relacionar materias en docente_materias usando users.id como docente_id
                foreach ($data['materias'] as $clave) {
                    $materia = materias::where('clave', $clave)->first();
                    if ($materia) {
                        DB::table('docente_materias')->insertOrIgnore([
                            'docente_id' => $user->id,   // → users.id
                            'materia_id' => $materia->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $this->command->warn("  ⚠ Materia '$clave' no encontrada.");
                    }
                }

                $creados++;
            });

            $this->command->info("✓ Docente creado: {$data['nombres']} {$data['apellido_paterno']} [{$data['numero_empleado']}]");
        }

        $this->command->info("─────────────────────────────────────");
        $this->command->info("✓ Docentes creados: $creados | Omitidos: $omitidos");
    }
}
