<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class historial_academico extends Model
{
    use HasFactory;

    protected $table = 'historial_academico';

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'cuatrimestre',
        'reprobada',
        'extraordinario',
        'temas_no_dominados',
    ];

    public function alumno()
    {
        return $this->belongsTo(alumnos::class, 'alumno_id');
    }

    public function materia()
    {
        return $this->belongsTo(materias::class, 'materia_id');
    }
}