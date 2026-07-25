<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\docentes;
use App\Models\carreras;
use App\Models\materias;
use App\Helpers\functions;
use App\Models\grupos;
use App\Models\sesiones_asesoria;
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
            'email' => 'required|email|ends_with:@utnay.edu.mx|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'not_in:12345678',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'
            ],
            'fecha_nacimiento' => 'required|date|before:today',
            'telefono' => 'required|string|regex:/^[0-9]{10}$/',
            'numero_empleado' => 'required|string|unique:docentes,numero_empleado',
            'carrera_id' => 'required|exists:carreras,id',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nombres.required' => 'El campo nombres es obligatorio.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.ends_with' => 'El correo debe ser del dominio @utnay.edu.mx',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.not_in' => 'La contraseña no puede ser "12345678". Elige una contraseña más segura.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'numero_empleado.required' => 'El número de empleado es obligatorio.',
            'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        try {
            DB::beginTransaction();

            $fotoPath = null;
            if ($request->hasFile('foto_perfil')) {
                $fotoPath = $request->file('foto_perfil')->store('fotos_perfil', 'public');
            }

            $apellidoMaterno = $request->apellido_materno;
            if (empty($apellidoMaterno)) {
                $apellidoMaterno = '';
            }

            $user = User::create([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $apellidoMaterno,
                'nickname' => null,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'foto_perfil' => $fotoPath,
                'rol' => User::ROL_DOCENTE,
            ]);

            $docente = docentes::create([
                'user_id' => $user->id,
                'numero_empleado' => $request->numero_empleado,
                'carrera_id' => $request->carrera_id,
            ]);

            DB::commit();

            registrar_log('CREAR', 'Docente: ' . $user->nombres . ' ' . $user->apellido_paterno . ' | Núm. empleado: ' . $request->numero_empleado, 'docentes');

            return redirect()->route('gestion', ['tab' => 'docentes'])->with('success', 'Docente registrado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestion', ['tab' => 'docentes'])->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $docente = docentes::with('user')->findOrFail($id);
        $carreras = carreras::all();
        $materias = materias::all();

        registrar_log('ACCESO', 'Accedió a edición del docente: ' . $docente->user->nombres . ' ' . $docente->user->apellido_paterno . ' | Núm. empleado: ' . $docente->numero_empleado, 'docentes');

        return view('admin.actualizarDocente', compact('docente', 'carreras', 'materias'));
    }

    public function update(Request $request, $id)
    {
        try {
            $docente = docentes::with('user')->findOrFail($id);
            $user = $docente->user;

            $request->validate([
                'nombres' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'apellido_materno' => 'nullable|string|max:255',
                'email' => 'required|email|ends_with:@utnay.edu.mx|unique:users,email,' . $user->id,
                'fecha_nacimiento' => 'nullable|date|before:today',
                'telefono' => 'nullable|string|regex:/^[0-9]{10}$/',
                'numero_empleado' => 'required|string|unique:docentes,numero_empleado,' . $docente->id,
                'carrera_id' => 'required|exists:carreras,id',
                'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            DB::beginTransaction();

            $apellidoMaterno = $request->input('apellido_materno');
            if (empty($apellidoMaterno)) {
                $apellidoMaterno = '';
            }

            $telefono = $request->input('telefono');
            if (empty($telefono)) {
                $telefono = '';
            }

            $fechaNacimiento = $request->input('fecha_nacimiento');
            if (empty($fechaNacimiento)) {
                $fechaNacimiento = null;
            }

            DB::update("UPDATE users SET 
                nombres = ?,
                apellido_paterno = ?,
                apellido_materno = ?,
                email = ?,
                fecha_nacimiento = ?,
                telefono = ?,
                nickname = NULL,
                updated_at = NOW()
                WHERE id = ?", [
                $request->input('nombres'),
                $request->input('apellido_paterno'),
                $apellidoMaterno,
                $request->input('email'),
                $fechaNacimiento,
                $telefono,
                $user->id
            ]);

            DB::table('docentes')
                ->where('id', $id)
                ->update([
                    'numero_empleado' => $request->input('numero_empleado'),
                    'carrera_id' => $request->input('carrera_id'),
                    'updated_at' => now()
                ]);

            if ($request->hasFile('foto_perfil')) {
                if ($user->foto_perfil) {
                    Storage::disk('public')->delete($user->foto_perfil);
                }
                $fotoPath = $request->file('foto_perfil')->store('fotos_perfil', 'public');
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['foto_perfil' => $fotoPath]);
            }

            if ($request->filled('password')) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => Hash::make($request->password)]);
            }

            DB::commit();

            $userVerificado = DB::table('users')->where('id', $user->id)->first();

            registrar_log('EDITAR', 'Docente: ' . $userVerificado->nombres . ' ' . $userVerificado->apellido_paterno . ' | Núm. empleado: ' . $request->numero_empleado, 'docentes');

            return redirect()->route('gestion', ['tab' => 'docentes'])
                ->with('success', 'Docente actualizado correctamente. Nuevo nombre: ' . $userVerificado->nombres);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar docente:', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $docente = docentes::with('user')->findOrFail($id);
            $user = $docente->user;

            registrar_log('ELIMINAR', 'Docente: ' . $user->nombres . ' ' . $user->apellido_paterno . ' | Núm. empleado: ' . $docente->numero_empleado, 'docentes');

            $docente->materias()->detach();

            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $docente->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('gestion', ['tab' => 'docentes'])->with('success', 'Docente eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard del docente con datos reales y gráficas.
     */
    public function dashboardDocente()
    {
        $user = auth()->user();
        $docente = $user->docente;

        // ============================================
        // PRÓXIMA ASESORÍA
        // ============================================
        $proximaAsesoria = sesiones_asesoria::where('docente_id', $user->id)
            ->whereIn('estado', ['programada', 'pendiente'])
            ->where('fecha_inicio', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        // ============================================
        // CONTAR ASESORÍAS AGENDADAS Y COMPLETADAS
        // ============================================
        $agendadas = sesiones_asesoria::where('docente_id', $user->id)
            ->whereIn('estado', ['programada', 'pendiente'])
            ->count();

        $completadas = sesiones_asesoria::where('docente_id', $user->id)
            ->where('estado', 'realizada')
            ->count();

        // ============================================
        // TOTAL ALUMNOS ATENDIDOS
        // ============================================
        $totalAlumnos = sesiones_asesoria::where('docente_id', $user->id)
            ->where('estado', 'realizada')
            ->with('alumnos')
            ->get()
            ->pluck('alumnos')
            ->flatten()
            ->unique('id')
            ->count();

        // ============================================
        // GRUPOS ACTIVOS
        // ============================================
        $gruposActivos = grupos::whereHas('docentes', function ($q) use ($user) {
            $q->where('docente_id', $user->id);
        })->count();

        // ============================================
        // GRUPOS RECIENTES
        // ============================================
        $recientesIds = session('grupos_recientes', []);
        $gruposRecientes = [];

        if (!empty($recientesIds)) {
            $gruposRecientes = grupos::with(['carrera', 'alumnos'])
                ->whereIn('id', $recientesIds)
                ->get()
                ->sortBy(function ($grupo) use ($recientesIds) {
                    return array_search($grupo->id, $recientesIds);
                });
        }

        // ============================================================
        // 📊 DATOS PARA GRÁFICAS DEL DOCENTE
        // ============================================================

        // 1. Alumnos más frecuentes (Top 10)
        $alumnos = DB::table('sesion_alumno')
            ->join('alumnos', 'sesion_alumno.alumno_id', '=', 'alumnos.user_id')
            ->join('users', 'alumnos.user_id', '=', 'users.id')
            ->join('sesiones_asesoria', 'sesion_alumno.sesion_id', '=', 'sesiones_asesoria.id')
            ->select(
                DB::raw('CONCAT(users.nombres, " ", users.apellido_paterno) as nombre_completo'),
                DB::raw('COUNT(*) as total')
            )
            ->where('sesiones_asesoria.docente_id', $user->id)
            ->groupBy('users.nombres', 'users.apellido_paterno', 'users.id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $alumnosLabels = $alumnos->pluck('nombre_completo')->toArray();
        $alumnosValues = $alumnos->pluck('total')->toArray();

        // 2. Estado de mis sesiones
        $solicitudes = sesiones_asesoria::select('estado', DB::raw('COUNT(*) as total'))
            ->where('docente_id', $user->id)
            ->groupBy('estado')
            ->get();

        $solicitudesLabels = $solicitudes->pluck('estado')->map(function($e) {
            $map = [
                'programada' => 'Programadas',
                'realizada' => 'Realizadas',
                'cancelada' => 'Canceladas',
                'pendiente' => 'Pendientes'
            ];
            return $map[$e] ?? $e;
        })->toArray();
        $solicitudesValues = $solicitudes->pluck('total')->toArray();

        return view('auth.docentes.escritorioDocente', compact(
            'proximaAsesoria',
            'agendadas',
            'completadas',
            'totalAlumnos',
            'gruposActivos',
            'gruposRecientes',
            'alumnosLabels',
            'alumnosValues',
            'solicitudesLabels',
            'solicitudesValues'
        ));
    }
}