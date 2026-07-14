<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class acuerdos_asesoria extends Model
{
    use HasFactory;

    protected $table = 'acuerdos_asesoria';

    protected $fillable = [
        'sesion_id',
        'alumno_id',
        'acuerdo',
    ];

    // Relación con la sesión de asesoría
    public function sesion()
    {
        return $this->belongsTo(sesiones_asesoria::class, 'sesion_id');
    }

    // Relación con el alumno (si se guarda por alumno)
    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }
}
