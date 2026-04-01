<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docentes extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_empleado',
        'carrera_id',  // ← DEBE ESTAR AQUÍ
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carrera()
    {
        return $this->belongsTo(carreras::class, 'carrera_id');
    }

    public function materias()
    {
        return $this->belongsToMany(materias::class, 'docente_materias', 'docente_id', 'materia_id');
    }
}