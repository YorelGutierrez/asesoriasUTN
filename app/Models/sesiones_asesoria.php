<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sesiones_asesoria extends Model
{
    use HasFactory;

    protected $fillable = [
    'docente_id',
    'solicitud_id',
    'tema',
    'fecha_inicio',
    'fecha_fin',
    'modalidad',
    'lugar',
    'estado',
    'observaciones',
];

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(solicitudes_asesoria::class, 'solicitud_id');
    }

    public function alumnos()
    {
        return $this->belongsToMany(User::class, 'sesion_alumno', 'sesion_id', 'alumno_id')
                    ->withTimestamps();
    }
}