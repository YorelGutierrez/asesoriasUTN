<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([  
            CarrerasSeeder::class,        // primero carreras
            MateriasSeeder::class,        // después materias
            GruposSeeder::class,          // luego grupos (dependen de carreras)
            MateriaGrupoSeeder::class,    // finalmente relaciones (dependen de grupos y materias)
            UserSeeder::class,   
            DocenteSeeder::class, //Docentes
        ]);
    }
}
