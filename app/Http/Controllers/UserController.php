<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['docente', 'alumno'])->get();
        return view('admin.rolesPermisos', compact('usuarios'));
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return back()->with('error', 'No puedes bloquear tu propia cuenta.');
        }
        $user->estado = !$user->estado;
        $user->save();

        $action = $user->estado ? 'desbloqueada' : 'bloqueada';
        registrar_log('BLOQUEO', "Cuenta {$action} - Usuario: {$user->email}", 'seguridad');
        return back()->with('success', "Cuenta {$action} correctamente.");
    }
}