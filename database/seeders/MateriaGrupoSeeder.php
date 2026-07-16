<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\materias;
use App\Models\grupos;

class MateriaGrupoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todas las materias
        $materias = materias::all();

        // Obtener los grupos IDGS-81, 82, 83, 84 (usando sus nombres o IDs)
        $grupos = grupos::whereIn('nombre', ['IDGS-91', 'IDGS-92', 'IDGS-93', 'IDGS-94'])->get();

        if ($materias->isEmpty()) {
            $this->command->error('No hay materias. Ejecuta primero MateriasSeeder.');
            return;
        }

        if ($grupos->isEmpty()) {
            $this->command->error('No se encontraron los grupos IDGS-91,92,93,94. Ejecuta primero GruposSeeder.');
            return;
        }

        // Para cada grupo, asociar todas las materias
        foreach ($grupos as $grupo) {
            foreach ($materias as $materia) {
                // Usamos firstOrCreate para evitar duplicados si el seeder se ejecuta varias veces
                $grupo->materias()->syncWithoutDetaching([$materia->id]);
            }
        }

        $this->command->info('Asociación de materias con grupos completada.');
    }
}
