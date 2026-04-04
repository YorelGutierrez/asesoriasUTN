<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class archivos_asesoria extends Model
{
    use HasFactory;

    protected $table = 'archivos_asesoria';

    protected $fillable = [
        'id_referencia',
        'tipo_referencia',
        'nombre_archivo',
        'ruta',
        'subido_por',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'subido_por');
    }
}