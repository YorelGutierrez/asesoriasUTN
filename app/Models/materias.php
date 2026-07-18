<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materias extends Model
{
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = [
        'nombre',
        'clave',
    ];
  
    public function docentes()
    {
        return $this->belongsToMany(docentes::class, 'docente_materias', 'materia_id', 'docente_id');
    }
  
    public function grupos()
    {
        return $this->belongsToMany(grupos::class, 'materia_grupos', 'materia_id', 'grupo_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(solicitudes_asesoria::class, 'materia_id');
    }

    public function sesiones()
    {
        return $this->hasMany(sesiones_asesoria::class, 'materia_id');
    }

    public function historialAcademico()
    {
        return $this->hasMany(historial_academico::class, 'materia_id');
    }
}