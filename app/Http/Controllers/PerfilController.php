<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\alumnos;
use App\Models\docentes;

class PerfilController extends Controller
{
    /**
     * Muestra el perfil del usuario con opción para cambiar contraseña
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener datos adicionales según el rol
        $datosAdicionales = [];
        
        if ($user->rol === 'alumno') {
            $alumno = alumnos::where('user_id', $user->id)->first();
            if ($alumno) {
                $datosAdicionales = [
                    'matricula' => $alumno->matricula ?? 'N/A',
                    'carrera' => $alumno->carrera->nombre ?? 'N/A',
                    'grupo' => $alumno->grupo->nombre ?? 'N/A',
                    'cuatrimestre' => $alumno->cuatrimestre ?? 'N/A',
                ];
            }
        } elseif ($user->rol === 'docente') {
            $docente = docentes::where('user_id', $user->id)->first();
            if ($docente) {
                $datosAdicionales = [
                    'numero_empleado' => $docente->numero_empleado ?? 'N/A',
                    'carrera' => $docente->carrera->nombre ?? 'N/A',
                    'departamento' => $docente->departamento ?? 'N/A',
                ];
            }
        } elseif ($user->rol === 'admin') {
            $datosAdicionales = [
                'rol' => 'Administrador',
            ];
        }
        
        return view('auth.perfil', compact('user', 'datosAdicionales'));
    }

    /**
     * Cambia la contraseña del usuario
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->password_actual, $user->password)) {
            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta.',
            ])->withInput();
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        // Registrar en bitácora
        if (function_exists('registrar_log')) {
            registrar_log('PERFIL', 'Cambió su contraseña', 'usuarios');
        }

        return redirect()->route('perfil')->with('success', '¡Contraseña actualizada correctamente!');
    }

    /**
     * Cambia la foto de perfil del usuario
     */
   /**
 * Cambia la foto de perfil del usuario
 */
    public function cambiarFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Eliminar foto anterior si existe
        if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        // Guardar la nueva foto
        $path = $request->file('foto')->store('perfiles', 'public');
        
        // Actualizar el campo foto_perfil en la base de datos
        $user->foto_perfil = $path;
        $user->save();

        // Registrar en bitácora
        if (function_exists('registrar_log')) {
            registrar_log('PERFIL', 'Cambió su foto de perfil', 'usuarios');
        }

        // Limpiar la URL del historial
        session()->forget('navigation_history');

        return redirect()->route('perfil')->with('success', '¡Foto de perfil actualizada correctamente!');
    }
}