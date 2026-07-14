<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reportes_asesoria extends Model
{
    use HasFactory;

    protected $table = 'reportes_asesoria';

    protected $fillable = [
        'sesion_id',
        'nombre_archivo',
        'ruta',
    ];

    public function sesion()
    {
        return $this->belongsTo(sesiones_asesoria::class, 'sesion_id');
    }
}