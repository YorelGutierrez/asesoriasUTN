<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docente_materia extends Model
{
    use HasFactory;

    protected $table = 'docente_materias';

    protected $fillable = [
        'docente_id',
        'materia_id',
    ];

    public function docente()
    {
        return $this->belongsTo(docentes::class);
    }

    public function materia()
    {
        return $this->belongsTo(materias::class);
    }
}