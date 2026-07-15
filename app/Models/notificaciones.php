<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notificaciones extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'mensaje',
        'leido',
        'datos',
        'accion',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'datos' => 'array',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Helper para crear notificaciones
    public static function crear($usuarioId, $tipo, $mensaje, $datos = [])
    {
        return self::create([
            'usuario_id' => $usuarioId,
            'tipo'       => $tipo,
            'mensaje'    => $mensaje,
            'leido'      => false,
            'datos'      => $datos,
            'accion'     => 'pendiente',
        ]);
    }
}
