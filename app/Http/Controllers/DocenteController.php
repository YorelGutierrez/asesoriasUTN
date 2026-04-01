<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\docentes;
use App\Models\carreras;
use App\Models\materias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DocenteController extends Controller
{
    // Mostrar formulario de registro
    public function create()
    {
        $carreras = carreras::all();
        $materias = materias::all();
        return view('admin.registroDocentes', compact('carreras', 'materias'));
    }

    // Guardar nuevo docente
    public function store(Request $request)
    {
        try {
            // Validación con todas las reglas
            $validated = $request->validate([
                // Datos personales (tabla users)
                'nombres' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'apellido_paterno' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'apellido_materno' => 'nullable|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                    'regex:/^[a-zA-Z0-9._%+-]+@utnay\.edu\.mx$/'
                ],
                
                'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
                
                'fecha_nacimiento' => 'nullable|date|before:today|after:1920-01-01',
                'telefono' => 'nullable|string|size:10|regex:/^[0-9]+$/',
                'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                
                // Datos específicos de docente (tabla docentes)
                'numero_empleado' => 'required|string|unique:docentes,numero_empleado|regex:/^[0-9]+$/|min:5|max:20',
                'carrera' => 'required|exists:carreras,id',
                'materia' => 'nullable|exists:materias,id',
                
            ], [
                // Mensajes de error personalizados
                'nombres.required' => 'El campo nombres es obligatorio.',
                'nombres.regex' => 'El campo nombres solo debe contener letras.',
                'apellido_paterno.required' => 'El campo apellido paterno es obligatorio.',
                'apellido_paterno.regex' => 'El campo apellido paterno solo debe contener letras.',
                'apellido_materno.regex' => 'El campo apellido materno solo debe contener letras.',
                'email.required' => 'El campo correo electrónico es obligatorio.',
                'email.email' => 'Debe ingresar un correo electrónico válido.',
                'email.unique' => 'Este correo electrónico ya está registrado.',
                'email.regex' => 'El correo debe ser institucional con dominio @utnay.edu.mx',
                'password.required' => 'El campo contraseña es obligatorio.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'La confirmación de contraseña no coincide.',
                'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
                'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior al día de hoy.',
                'fecha_nacimiento.after' => 'La fecha de nacimiento debe ser posterior a 1920.',
                'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
                'telefono.regex' => 'El teléfono solo debe contener números.',
                'foto_perfil.image' => 'El archivo debe ser una imagen.',
                'foto_perfil.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
                'foto_perfil.max' => 'La imagen no debe pesar más de 2MB.',
                'numero_empleado.required' => 'El número de empleado es obligatorio.',
                'numero_empleado.unique' => 'Este número de empleado ya está registrado.',
                'numero_empleado.regex' => 'El número de empleado solo debe contener números.',
                'numero_empleado.min' => 'El número de empleado debe tener al menos 5 caracteres.',
                'numero_empleado.max' => 'El número de empleado no debe exceder los 20 caracteres.',
                'carrera.required' => 'Debe seleccionar una carrera.',
                'carrera.exists' => 'La carrera seleccionada no es válida.',
                'materia.exists' => 'La materia seleccionada no es válida.',
            ]);

            // ========== DEPURACIÓN: Si llegas aquí, la validación pasó ==========
            // Redirigir con un mensaje temporal para verificar
            return redirect()->route('registro_docente')
                ->with('debug', 'Validación pasó correctamente. Datos: ' . json_encode($validated));
            
            // ========== COMENTA O ELIMINA EL RESTO DEL CÓDIGO POR AHORA ==========
            
            /*
            // Iniciar transacción de base de datos
            DB::beginTransaction();

            // 1. Guardar foto de perfil (si se subió)
            $rutaFoto = null;
            if ($request->hasFile('foto_perfil')) {
                $rutaFoto = $request->file('foto_perfil')->store('fotos_perfil', 'public');
            }

            // 2. Crear el usuario con rol de docente
            $user = User::create([
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'foto_perfil' => $rutaFoto,
                'rol' => User::ROL_DOCENTE,
            ]);

            // Verificar que el usuario se creó
            if (!$user) {
                throw new \Exception('No se pudo crear el usuario');
            }

            // 3. Crear el registro de docente
            $docente = docentes::create([
                'user_id' => $user->id,
                'numero_empleado' => $request->numero_empleado,
                'carrera_id' => $request->carrera,
            ]);

            // Verificar que el docente se creó
            if (!$docente) {
                throw new \Exception('No se pudo crear el docente');
            }

            // 4. Asignar materia al docente (única)
            if ($request->has('materia') && $request->materia) {
                $docente->materias()->attach($request->materia);
            }

            // Confirmar transacción
            DB::commit();

            // Redireccionar con mensaje de éxito
            return redirect()->route('registro_docente')->with('success', 'Docente registrado exitosamente');
            */

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Esto maneja los errores de validación automáticamente
            throw $e;
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            
            // Redireccionar con mensaje de error
            return redirect()->route('registro_docente')
                ->with('error', 'Error al registrar docente: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    // Listar docentes
    public function index()
    {
        $docentes = docentes::with(['user', 'carrera', 'materias'])->get();
        return view('docentes.index', compact('docentes'));
    }
    
    // Ver detalles de un docente
    public function show($id)
    {
        $docente = docentes::with(['user', 'carrera', 'materias'])->findOrFail($id);
        return view('docentes.show', compact('docente'));
    }
    
    // Mostrar formulario de edición
    public function edit($id)
    {
        $docente = docentes::with('user')->findOrFail($id);
        $carreras = carreras::all();
        $materias = materias::all();
        return view('docentes.edit', compact('docente', 'carreras', 'materias'));
    }
    
    // Actualizar docente
    public function update(Request $request, $id)
    {
        try {
            $docente = docentes::findOrFail($id);
            $user = $docente->user;
            
            $request->validate([
                'nombres' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'apellido_paterno' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'apellido_materno' => 'nullable|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')->ignore($user->id),
                    'regex:/^[a-zA-Z0-9._%+-]+@utnay\.edu\.mx$/'
                ],
                'fecha_nacimiento' => 'nullable|date|before:today|after:1920-01-01',
                'telefono' => 'nullable|string|size:10|regex:/^[0-9]+$/',
                'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'numero_empleado' => 'required|string|regex:/^[0-9]+$/|min:5|max:20|unique:docentes,numero_empleado,' . $docente->id,
                'carrera' => 'required|exists:carreras,id',
                'materia' => 'nullable|exists:materias,id',
            ], [
                'nombres.regex' => 'El campo nombres solo debe contener letras.',
                'apellido_paterno.regex' => 'El campo apellido paterno solo debe contener letras.',
                'apellido_materno.regex' => 'El campo apellido materno solo debe contener letras.',
                'email.regex' => 'El correo debe ser institucional con dominio @utnay.edu.mx',
                'telefono.size' => 'El teléfono debe tener exactamente 10 dígitos.',
                'telefono.regex' => 'El teléfono solo debe contener números.',
                'numero_empleado.regex' => 'El número de empleado solo debe contener números.',
                'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior al día de hoy.',
            ]);
            
            DB::beginTransaction();
            
            // Actualizar datos del usuario
            $userData = [
                'nombres' => $request->nombres,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'email' => $request->email,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
            ];
            
            // Actualizar contraseña solo si se proporciona
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/'
                ], [
                    'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.'
                ]);
                $userData['password'] = Hash::make($request->password);
            }
            
            // Actualizar foto de perfil
            if ($request->hasFile('foto_perfil')) {
                if ($user->foto_perfil) {
                    Storage::disk('public')->delete($user->foto_perfil);
                }
                $rutaFoto = $request->file('foto_perfil')->store('fotos_perfil', 'public');
                $userData['foto_perfil'] = $rutaFoto;
            }
            
            $user->update($userData);
            
            // Actualizar datos del docente
            $docente->update([
                'numero_empleado' => $request->numero_empleado,
                'carrera_id' => $request->carrera,
            ]);
            
            // Sincronizar materia (única)
            $materiasIds = $request->has('materia') && $request->materia ? [$request->materia] : [];
            $docente->materias()->sync($materiasIds);
            
            DB::commit();
            
            return redirect()->route('docentes.index')->with('success', 'Docente actualizado exitosamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('docentes.index')->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    
    // Eliminar docente
    public function destroy($id)
    {
        try {
            $docente = docentes::findOrFail($id);
            $user = $docente->user;
            
            DB::beginTransaction();
            
            // Eliminar relaciones
            $docente->materias()->detach();
            
            // Eliminar docente
            $docente->delete();
            
            // Eliminar foto de perfil si existe
            if ($user->foto_perfil) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            
            // Eliminar usuario
            $user->delete();
            
            DB::commit();
            
            return redirect()->route('docentes.index')->with('success', 'Docente eliminado exitosamente');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('docentes.index')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}