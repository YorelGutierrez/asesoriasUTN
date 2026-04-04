<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\docentes;
use App\Models\carreras;
use App\Models\materias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocenteController extends Controller
{
    // Mostrar formulario de registro
        public function create()
        {
            $carreras = carreras::all();
            $materias = materias::all();
            return view('admin.registroDocentes', compact('carreras', 'materias'));
        }

public function store(Request $request)
{
    $request->validate([
        'nombres' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
        'fecha_nacimiento' => 'required|date|before:today',
        'telefono' => 'required|string|regex:/^[0-9]{10}$/',
        'numero_empleado' => 'required|string|unique:docentes,numero_empleado',
        'carrera_id' => 'required|exists:carreras,id',
    ], [
        'nombres.required' => 'El campo nombres es obligatorio.',
        'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'numero_empleado.required' => 'El número de empleado es obligatorio.',
        'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
        'carrera_id.required' => 'Debes seleccionar una carrera.',
        'carrera_id.exists' => 'La carrera seleccionada no existe.',
        'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
        'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
        'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
        'telefono.required' => 'El teléfono es obligatorio.',
        'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
    ]);

    try {
        DB::beginTransaction();

        $user = User::create([
            'nombres' => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
            'rol' => User::ROL_DOCENTE,
        ]);

        $docente = docentes::create([
            'user_id' => $user->id,
            'numero_empleado' => $request->numero_empleado,
            'carrera_id' => $request->carrera_id,
        ]);

        DB::commit();

        return redirect()->route('registro_docente')->with('success', 'Docente registrado correctamente');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('registro_docente')->with('error', 'Error: ' . $e->getMessage())->withInput();
    }
}

    // Eliminar
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $docente = docentes::with('user')->findOrFail($id);
            $docente->materias()->detach();
            $user = $docente->user;

            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $docente->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('docentes.index')
                ->with('success', 'Docente eliminado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}