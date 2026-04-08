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

        // Usuario docente
User::create([
    'nombres' => 'Juan',
    'apellido_paterno' => 'Tovar',
    'apellido_materno' => 'Sánchez',
    'nickname' => 'juan.tovar',
    'email' => 'tovar@utnay.edu.mx',
    'password' => bcrypt('12345678'),
    'edad' => 35,
    'fecha_nacimiento' => '1989-05-20',
    'telefono' => '3111234567',
    'foto_perfil' => '/img/default-avatar.png',  // ✅ Ahora sí
    'rol' => 'docente',
    'estado' => true,
    'email_verified_at' => now(),
    'created_at' => now(),
    'updated_at' => now(),
]);



        $existingUser = User::where('email', $adminData['email'])
                            ->orWhere('nickname', $adminData['nickname'])
                            ->first();

        if (!$existingUser) {
            User::create($adminData);
            $this->command->info('Usuario administrador creado exitosamente.');
        } else {
            $this->command->warn('El usuario administrador ya existe. No se insertó duplicado.');
        }
    }
}
