<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\grupos;

class carreras extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'clave',
        'logo',
    ];

    // Relación con grupos (una carrera tiene muchos grupos)
    public function grupos()
    {
        return $this->hasMany(grupos::class);
    }
}
