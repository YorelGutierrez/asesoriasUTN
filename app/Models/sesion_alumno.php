<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sesion_alumno extends Model
{
    use HasFactory;

    protected $table = 'sesion_alumno';

    protected $fillable = [
        'sesion_id',
        'alumno_id',
    ];

    public function sesion()
    {
        return $this->belongsTo(sesiones_asesoria::class, 'sesion_id');
    }

    public function alumno()
    {
        return $this->belongsTo(User::class, 'alumno_id');
    }
}