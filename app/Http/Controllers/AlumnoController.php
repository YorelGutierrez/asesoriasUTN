<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\alumnos;
use App\Models\carreras;
use App\Models\grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlumnoController extends Controller
{
    public function create()
    {
        $carreras = carreras::all();
        $grupos = grupos::all();
        return view('admin.registroAlumnos', compact('carreras', 'grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'email' => 'required|email|ends_with:@utnay.edu.mx|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'required|string|regex:/^[0-9]{10}$/',
            'matricula' => 'required|string|unique:alumnos,matricula',
            'carrera_id' => 'required|exists:carreras,id',
            'grupo_id' => 'nullable|exists:grupos,id',
            'cuatrimestre' => 'nullable|integer|min:1|max:12',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.ends_with' => 'El correo debe ser del dominio @utnay.edu.mx',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // Guardar foto
            $fotoPath = null;
            if ($request->hasFile('foto_perfil')) {
                $fotoPath = $request->file('foto_perfil')->store('fotos_perfil', 'public');
            }

            $user = User::create([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'foto_perfil' => $fotoPath,
                'rol' => User::ROL_ALUMNO,
            ]);

            $alumno = alumnos::create([
                'user_id' => $user->id,
                'matricula' => $request->matricula,
                'carrera_id' => $request->carrera_id,
                'grupo_id' => $request->grupo_id,
                'cuatrimestre' => $request->cuatrimestre,
            ]);

            DB::commit();

            return redirect()->route('alumnos.index')->with('success', 'Alumno registrado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $alumnos = alumnos::with(['user', 'carrera', 'grupo'])->paginate(10);
        return view('admin.alumnos.index', compact('alumnos'));
    }

    public function show($id)
    {
        $alumno = alumnos::with(['user', 'carrera', 'grupo'])->findOrFail($id);
        return view('admin.alumnos.show', compact('alumno'));
    }

    public function edit($id)
{
    $alumno = alumnos::with('user')->findOrFail($id);
    $carreras = carreras::all();
    $grupos = grupos::all();
    return view('admin.actualizarAlumno', compact('alumno', 'carreras', 'grupos'));
}

    public function update(Request $request, $id)
{
    $alumno = alumnos::findOrFail($id);
    $user = $alumno->user;

    $request->validate([
        'nombres' => 'required|string|max:255',
        'apellido_paterno' => 'required|string|max:255',
        'apellido_materno' => 'nullable|string|max:255',
        'email' => 'required|email|ends_with:@utnay.edu.mx|unique:users,email,' . $user->id,
        'fecha_nacimiento' => 'nullable|date|before:today',
        'telefono' => 'nullable|string|regex:/^[0-9]{10}$/',
        'matricula' => 'required|string|unique:alumnos,matricula,' . $alumno->id,
        'carrera_id' => 'required|exists:carreras,id',
        'grupo_id' => 'nullable|exists:grupos,id',
        'cuatrimestre' => 'nullable|integer|min:1|max:12',
        'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    try {
        DB::beginTransaction();

        // Actualizar foto
        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $fotoPath = $request->file('foto_perfil')->store('fotos_perfil', 'public');
            $user->foto_perfil = $fotoPath;
        }

        $user->update([
            'nombres' => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email' => $request->email,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'telefono' => $request->telefono,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        } else {
            $user->save();
        }

        $alumno->update([
            'matricula' => $request->matricula,
            'carrera_id' => $request->carrera_id,
            'grupo_id' => $request->grupo_id,
            'cuatrimestre' => $request->cuatrimestre,
        ]);

        DB::commit();

        return redirect()->route('gestion')->with('success', 'Alumno actualizado correctamente');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $alumno = alumnos::with('user')->findOrFail($id);
            $user = $alumno->user;

            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $alumno->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('gestion', ['tab' => 'alumnos'])
            ->with('success', 'Alumno eliminado correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}