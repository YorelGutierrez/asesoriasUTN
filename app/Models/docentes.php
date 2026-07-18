<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docentes extends Model
{
    use HasFactory;

    protected $table = 'docentes';

    protected $fillable = [
        'user_id',
        'numero_empleado',
        'carrera_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera()
    {
        return $this->belongsTo(carreras::class, 'carrera_id');
    }

    public function materias()
    {
        return $this->belongsToMany(materias::class, 'docente_materias', 'docente_id', 'materia_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(solicitudes_asesoria::class, 'docente_id');
    }

    public function sesiones()
    {
        return $this->hasMany(sesiones_asesoria::class, 'docente_id');
    }
    
    public function grupos()
    {
        return $this->belongsToMany(grupos::class, 'docente_grupos', 'docente_id', 'grupo_id');
    }
}