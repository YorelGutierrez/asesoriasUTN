<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class alumnos extends Model
{
    use HasFactory;

    protected $table = 'alumnos';

    protected $fillable = [
        'user_id',
        'matricula',
        'carrera_id',
        'grupo_id',
        'cuatrimestre',
        'status_academico',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera()
    {
        return $this->belongsTo(carreras::class, 'carrera_id');
    }

    public function grupo()
    {
        return $this->belongsTo(grupos::class, 'grupo_id');
    }

    public function historialAcademico()
    {
        return $this->hasMany(historial_academico::class, 'alumno_id');
    }

    public function sesiones()
    {
        return $this->belongsToMany(sesiones_asesoria::class, 'sesion_alumnos', 'alumno_id', 'sesion_id')
            ->withTimestamps();
    }

    public function solicitudes()
    {
        return $this->hasMany(solicitudes_asesoria::class, 'alumno_id');
    }
}