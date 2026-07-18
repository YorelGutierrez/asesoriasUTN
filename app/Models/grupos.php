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

    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'carrera_id',
        'cuatrimestre',
    ];

    public function carrera()
    {
        return $this->belongsTo(carreras::class, 'carrera_id');
    }

    public function alumnos()
    {
        return $this->hasMany(alumnos::class, 'grupo_id');
    }

    public function materias()
    {
        return $this->belongsToMany(materias::class, 'materia_grupos', 'grupo_id', 'materia_id');
    }

    public function docentes()
    {
        return $this->belongsToMany(\App\Models\User::class, 'docente_grupos', 'grupo_id', 'docente_id')
                    ->withTimestamps();
    }
}