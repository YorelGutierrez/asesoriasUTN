<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\grupos;

class materias extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'clave',
    ];

    // Relación muchos a muchos con grupos
    public function grupos()
    {
        return $this->belongsToMany(grupos::class, 'materia_grupos');
    }

}
