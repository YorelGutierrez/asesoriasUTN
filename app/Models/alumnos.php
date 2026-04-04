<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class alumnos extends Model
{
    use HasFactory;

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
        return $this->belongsTo(User::class);
    }

    public function carrera()
    {
        return $this->belongsTo(carreras::class, 'carrera_id');
    }

    public function grupo()
    {
        return $this->belongsTo(grupos::class, 'grupo_id');
    }
}