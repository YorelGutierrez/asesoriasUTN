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
