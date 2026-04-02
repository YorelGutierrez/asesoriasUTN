<?php

use App\Models\logs;

function registrar_log($accion, $descripcion = null, $modulo = null)
{
    logs::create([
        'user_id' => auth()->id(),
        'accion' => $accion,
        'descripcion' => $descripcion,
        'modulo' => $modulo,
        'ip' => request()->ip()
    ]);
}