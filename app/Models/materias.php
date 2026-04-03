<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class materias extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'clave',
    ];

    // Relación con docentes (muchos a muchos)
    public function docentes()
    {
        return $this->belongsToMany(docentes::class, 'docente_materias', 'materia_id', 'docente_id');
    }
}