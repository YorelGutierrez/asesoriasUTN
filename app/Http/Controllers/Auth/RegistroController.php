<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegistroController extends Controller
{
    // Mostrar formulario de registro
    public function showRegistrationForm()
    {
        return view('registro');
    }

    // Procesar el registro
    public function register(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'fecha_nacimiento' => 'nullable|date',
            'telefono' => 'nullable|string|max:20',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Crear el usuario
        $user = User::create([
            'nombres' => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
        ]);

        // Manejar la foto de perfil
        if ($request->hasFile('foto_perfil')) {
            $path = $request->file('foto_perfil')->store('fotos-perfil', 'public');
            $user->foto_perfil = $path;
            $user->save();
        }

        // Iniciar sesión
        Auth::login($user);
        // Redirigir al escritorio
        return redirect()->route('dashboard');

         // Intentar autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Redirigir según el rol del usuario autenticado
            $user = Auth::user();
            switch ($user->rol) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'docente':
                    return redirect()->route('docente.dashboard');
                case 'alumno':
                    return redirect()->route('alumno.dashboard');
            }
        }


    }
}
