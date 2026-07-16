<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\alumnos;
use App\Models\carreras;
use App\Models\grupos;
use App\Models\sesiones_asesoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AlumnoController extends Controller
{
    //Dashboard del alumno.
    public function dashboardAlumno()
    {
        $user = auth()->user();
        $alumno = $user->alumno;

        // Próxima asesoría (más cercana en el futuro)
        $proximaAsesoria = sesiones_asesoria::whereHas('alumnos', function ($q) use ($user) {
            $q->where('sesion_alumno.alumno_id', $user->id);
        })
            ->whereIn('estado', ['programada', 'pendiente'])
            ->where('fecha_inicio', '>=', now())
            ->orderBy('fecha_inicio', 'asc')
            ->first();

        // Asesorías agendadas
        $agendadas = sesiones_asesoria::whereHas('alumnos', function ($q) use ($user) {
            $q->where('sesion_alumno.alumno_id', $user->id);
        })
            ->whereIn('estado', ['programada', 'pendiente'])
            ->count();

        // Asesorías completadas
        $completadas = sesiones_asesoria::whereHas('alumnos', function ($q) use ($user) {
            $q->where('sesion_alumno.alumno_id', $user->id);
        })
            ->where('estado', 'realizada')
            ->count();

        // Últimas 5 asesorías realizadas
        $ultimasSesiones = sesiones_asesoria::with(['docente', 'acuerdos', 'reporte'])
            ->whereHas('alumnos', function ($q) use ($user) {
                $q->where('sesion_alumno.alumno_id', $user->id);
            })
            ->where('estado', 'realizada')
            ->orderBy('fecha_inicio', 'desc')
            ->take(5)
            ->get();

        // ===== 🔥 NUEVO: 3 docentes aleatorios del grupo con sus materias =====
        $docentesAleatorios = [];

        if ($alumno && $alumno->grupo) {
            // Obtener materias del grupo (IDs)
            $materiasGrupoIds = $alumno->grupo->materias->pluck('id')->toArray();

            // Obtener docentes del grupo
            $docentesDelGrupo = $alumno->grupo->docentes;

            // Construir array con docente y materias que imparte en este grupo
            $docentesConMaterias = [];
            foreach ($docentesDelGrupo as $docente) {
                $materiasDocenteIds = $docente->materias->pluck('id')->toArray();
                $materiasComunes = array_intersect($materiasGrupoIds, $materiasDocenteIds);

                if (!empty($materiasComunes)) {
                    // Obtener nombres de las materias comunes
                    $materiasNombres = \App\Models\materias::whereIn('id', $materiasComunes)->pluck('nombre')->toArray();
                    $docentesConMaterias[] = [
                        'docente' => $docente,
                        'materias' => $materiasNombres,
                    ];
                }
            }

            // Mezclar y tomar 3 aleatorios
            shuffle($docentesConMaterias);
            $docentesAleatorios = array_slice($docentesConMaterias, 0, 3);
        }

        return view('auth.alumnos.escritorioAlumno', compact(
            'proximaAsesoria',
            'agendadas',
            'completadas',
            'ultimasSesiones',
            'alumno',
            'docentesAleatorios' // 👈 Nueva variable
        ));
    }
    /**
     * Lista los alumnos del grupo activo en sesión.
     * - Docente: solo alumnos del grupo que seleccionó.
     * - Admin:   puede ver alumnos de cualquier grupo; si no hay grupo activo, muestra todos.
     */
    public function listar()
    {
        $grupoActivoId = session('grupo_activo_id');

        if ($grupoActivoId) {
            $grupo = grupos::with('carrera')->findOrFail($grupoActivoId);
            $alumnos = alumnos::with(['user', 'carrera', 'grupo'])
                ->where('grupo_id', $grupoActivoId)
                ->join('users', 'alumnos.user_id', '=', 'users.id')
                ->orderBy('users.apellido_paterno')
                ->orderBy('users.apellido_materno')
                ->orderBy('users.nombres')
                ->select('alumnos.*')
                ->get();
        } else {
            $grupo   = null;
            $alumnos = alumnos::with(['user', 'carrera', 'grupo'])
                ->join('users', 'alumnos.user_id', '=', 'users.id')
                ->orderBy('users.apellido_paterno')
                ->orderBy('users.apellido_materno')
                ->orderBy('users.nombres')
                ->select('alumnos.*')
                ->get();
        }

        return view('auth.alumnos', compact('alumnos', 'grupo'));
    }

    /**
     * Muestra el expediente completo de un alumno.
     * Guarda el alumno en sesión para que otras vistas lo recuerden.
     */
    public function expediente($id)
    {
        $alumno = alumnos::with(['user', 'carrera', 'grupo'])->findOrFail($id);

        // Guardar en sesión para uso en otras secciones
        session(['alumno_activo_id' => $alumno->id]);

        // Materias reprobadas del historial académico
        $materiasReprobadas = $alumno->historialAcademico()
            ->with('materia')
            ->where('reprobada', true)
            ->get();

        // Temas no dominados (registros con temas_no_dominados no nulo)
        $temasNoDominados = $alumno->historialAcademico()
            ->with('materia')
            ->whereNotNull('temas_no_dominados')
            ->where('temas_no_dominados', '!=', '')
            ->get();

        // Asesorías del alumno: sesiones donde aparece en sesion_alumno
        // Asesorías del alumno: solo las que están realizadas
        $sesiones = sesiones_asesoria::with(['docente', 'acuerdos', 'reporte'])
            ->whereHas('alumnos', function ($q) use ($alumno) {
                $q->where('sesion_alumno.alumno_id', $alumno->user_id);
            })
            ->where('estado', 'realizada') // 🔥 FILTRO: solo realizadas
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        // Últimas 3 asesorías para el resumen rápido
        $ultimasSesiones = $sesiones->take(3);

        return view('auth.expediente_alumnos', compact(
            'alumno',
            'materiasReprobadas',
            'temasNoDominados',
            'sesiones',
            'ultimasSesiones'
        ));
    }

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
            'matricula' => 'required|string|unique:alumnos,matricula',
            'carrera_id' => 'required|exists:carreras,id',
            'grupo_id' => 'nullable|exists:grupos,id',
            'cuatrimestre' => 'nullable|integer|min:1|max:12',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nombres.required' => 'El campo nombres es obligatorio.',
            'nombres.max' => 'El campo nombres no puede tener más de 255 caracteres.',
            'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
            'apellido_paterno.max' => 'El campo apellido paterno no puede tener más de 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.ends_with' => 'El correo debe ser del dominio @utnay.edu.mx',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.regex' => 'El teléfono debe tener exactamente 10 dígitos.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Esta matrícula ya está registrada.',
            'carrera_id.required' => 'Debes seleccionar una carrera.',
            'carrera_id.exists' => 'La carrera seleccionada no existe.',
            'grupo_id.exists' => 'El grupo seleccionado no existe.',
            'cuatrimestre.integer' => 'El cuatrimestre debe ser un número entero.',
            'cuatrimestre.min' => 'El cuatrimestre debe ser al menos 1.',
            'cuatrimestre.max' => 'El cuatrimestre no puede ser mayor a 12.',
            'foto_perfil.image' => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'foto_perfil.max' => 'La imagen no debe pesar más de 2MB.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.not_in' => 'La contraseña no puede ser "12345678". Elige una contraseña más segura.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
        ]);

        try {
            DB::beginTransaction();

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

            // REGISTRAR LOG
            registrar_log('CREAR', 'Alumno: ' . $user->nombres . ' ' . $user->apellido_paterno . ' | Matrícula: ' . $request->matricula, 'alumnos');

            return redirect()->route('gestion', ['tab' => 'alumnos'])->with('success', 'Alumno registrado correctamente');
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

            // REGISTRAR LOG
            registrar_log('EDITAR', 'Alumno: ' . $user->nombres . ' ' . $user->apellido_paterno . ' | Matrícula: ' . $request->matricula, 'alumnos');

            return redirect()->route('gestion', ['tab' => 'alumnos'])->with('success', 'Alumno actualizado correctamente');
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

            // REGISTRAR LOG ANTES DE ELIMINAR
            registrar_log('ELIMINAR', 'Alumno: ' . $user->nombres . ' ' . $user->apellido_paterno . ' | Matrícula: ' . $alumno->matricula, 'alumnos');

            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }

            $alumno->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('gestion', ['tab' => 'alumnos'])->with('success', 'Alumno eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
