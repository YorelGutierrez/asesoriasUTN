<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========== USUARIO ADMINISTRADOR ==========
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

        // Crear admin si no existe (evita duplicados)
        User::firstOrCreate(
            ['nickname' => 'admin'], // Buscar por nickname
            $adminData // Datos para crear si no existe
        );

        // ========== USUARIO DOCENTE ==========
        $docenteData = [
            'nombres' => 'Juan',
            'apellido_paterno' => 'Tovar',
            'apellido_materno' => 'Sánchez',
            'nickname' => 'juan.tovar',
            'email' => 'tovar@utnay.edu.mx',
            'password' => bcrypt('12345678'),
            'edad' => 35,
            'fecha_nacimiento' => '1989-05-20',
            'telefono' => '3111234567',
            'foto_perfil' => '/img/default-avatar.png',
            'rol' => 'docente',
            'estado' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Crear docente si no existe (evita duplicados)
        User::firstOrCreate(
            ['nickname' => 'juan.tovar'], // Buscar por nickname
            $docenteData // Datos para crear si no existe
        );

        $this->command->info('Usuarios creados/verificados exitosamente.');
    }
}