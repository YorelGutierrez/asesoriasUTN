<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\alumnos;
use App\Models\carreras;
use App\Models\grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    // Mostrar formulario de registro
    public function create()
    {
        $carreras = carreras::all();
        $grupos = grupos::all();
        return view('admin.registroAlumnos', compact('carreras', 'grupos'));
    }

    // Guardar nuevo alumno
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
            'matricula' => 'required|string|unique:alumnos,matricula',
            'carrera_id' => 'required|exists:carreras,id',
            'grupo_id' => 'nullable|exists:grupos,id',
            'cuatrimestre' => 'nullable|integer|min:1|max:12',
        ], [
            'nombres.required' => 'El campo nombres es obligatorio.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'cuatrimestre.min' => 'El cuatrimestre debe ser al menos 1.',
            'cuatrimestre.max' => 'El cuatrimestre no puede ser mayor a 12.',
        ]);

        try {
            DB::beginTransaction();

            // Crear el usuario
            $user = User::create([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'rol' => User::ROL_ALUMNO,
            ]);

            // Crear el alumno
            $alumno = alumnos::create([
                'user_id' => $user->id,
                'matricula' => $request->matricula,
                'carrera_id' => $request->carrera_id,
                'grupo_id' => $request->grupo_id,
                'cuatrimestre' => $request->cuatrimestre,
            ]);

            DB::commit();

            return redirect()->route('registro_alumnos')->with('success', 'Alumno registrado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('registro_alumnos')->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
}