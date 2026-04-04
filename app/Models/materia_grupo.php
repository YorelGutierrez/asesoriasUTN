<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\grupos;
use App\Models\materias;

class materia_grupo extends Model
{
    use HasFactory;
     protected $table = 'materia_grupos';

    protected $fillable = [
        'materia_id',
        'grupo_id',
    ];

    public function materia()
    {
        return $this->belongsTo(materias::class);
    }

    public function grupo()
    {
        return $this->belongsTo(grupos::class);
    }
}
