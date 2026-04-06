<?php

use App\Models\logs;
use App\Models\User;

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

function getUserStats()
{
    return [
        'admins' => User::where('rol', 'admin')->count(),
        'docentes' => User::where('rol', 'docente')->count(),
        'tutores' => User::where('rol', 'tutor')->count(),
        'alumnos' => User::where('rol', 'alumno')->count(),
    ];
}