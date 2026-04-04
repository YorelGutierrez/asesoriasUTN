<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\carreras;
use App\Models\alumnos;
use App\Models\materias;

class grupos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'carrera_id',
        'cuatrimestre',
    ];

    // Relación con carrera
    public function carrera()
    {
        return $this->belongsTo(carreras::class);
    }

    // Relación con alumnos (si tienes la tabla alumnos)
    public function alumnos()
    {
        return $this->hasMany(alumnos::class);
    }

    // Relación muchos a muchos con materias
    public function materias()
    {
        return $this->belongsToMany(materias::class, 'materia_grupos', 'grupo_id', 'materia_id');
    }
}
