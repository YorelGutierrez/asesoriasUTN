<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asesoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'docente_id',
        'tipo',
        'alumno_id',
        'grupo_id',
        'materia',
        'tema',
        'modalidad',
        'fecha',
        'hora',
        'preguntas',
        'estado'
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }
}