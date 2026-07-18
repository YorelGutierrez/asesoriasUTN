<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class solicitudes_asesoria extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_asesoria';  // ← IMPORTANTE

    protected $fillable = [
        'alumno_id',
        'docente_id',
        'materia_id',
        'fecha_solicitud',
        'estado',
        'descripcion',
        'motivo'
    ];

    public function alumno()
    {
        return $this->belongsTo(alumnos::class, 'alumno_id');
    }

    public function docente()
    {
        return $this->belongsTo(docentes::class, 'docente_id');
    }

    public function materia()
    {
        return $this->belongsTo(materias::class, 'materia_id');
    }

    public function sesiones()
    {
        return $this->hasMany(sesiones_asesoria::class, 'solicitud_id');
    }
}