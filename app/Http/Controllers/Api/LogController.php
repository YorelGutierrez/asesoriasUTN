<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\logs;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    // Listar logs no eliminados (soft delete automático)
    public function index()
    {
        $logs = logs::with('user')->latest()->take(20)->get();

        $data = $logs->map(function ($log) {
            $user = $log->user;
            $fotoUrl = null;

            if ($user && $user->foto_perfil) {
                $fotoUrl = Storage::url($user->foto_perfil);
            }

            return [
                'id' => $log->id,
                'descripcion' => $log->descripcion,
                'created_at' => $log->created_at,
                'user' => $user ? [
                    'nombres' => $user->nombres,
                    'apellido_paterno' => $user->apellido_paterno,
                    'apellido_materno' => $user->apellido_materno,
                    'foto_perfil' => $fotoUrl,
                ] : null,
            ];
        });

        return response()->json($data);
    }

    // Soft delete individual
    public function destroy($id)
    {
        $log = logs::findOrFail($id);
        $log->delete();  // marca deleted_at

        return response()->json(['message' => 'Log eliminado (soft delete)']);
    }

    // Eliminar físicamente todos los logs (solo admin)
    public function destroyAll()
    {
        // Opcional: verificar rol admin
        if (auth()->user()->rol !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        logs::truncate();  // elimina todos los registros de la tabla

        return response()->json(['message' => 'Todos los logs eliminados permanentemente']);
    }
}

