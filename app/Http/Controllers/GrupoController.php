<?php

namespace App\Http\Controllers;

use App\Models\grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    /**
     * Muestra los grupos según el rol:
     * - Docente: solo los grupos asignados en docente_grupos (docente_id = users.id)
     * - Admin:   todos los grupos
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->rol === 'admin') {
            $gruposLista = grupos::with(['carrera', 'alumnos'])->get();
        } else {
            // docente_grupos.docente_id apunta a users.id
            $gruposLista = grupos::with(['carrera', 'alumnos'])
                ->whereHas('docentes', function ($q) use ($user) {
                    $q->where('docente_id', $user->id);
                })
                ->get();
        }

        $grupoActivoId = session('grupo_activo_id');

        return view('auth.grupos', compact('gruposLista', 'grupoActivoId'));
    }

    /**
     * Guarda el grupo seleccionado en sesión y redirige al dashboard.
     */
    public function seleccionar($id)
    {
        $user = auth()->user();

        // Docente: verificar que el grupo le pertenece
        if ($user->rol === 'docente') {
            $tieneAcceso = grupos::whereHas('docentes', function ($q) use ($user) {
                $q->where('docente_id', $user->id);
            })->where('id', $id)->exists();

            if (!$tieneAcceso) {
                return redirect()->route('grupos')
                    ->with('error', 'No tienes acceso a ese grupo.');
            }
        }

        $grupo = grupos::with('carrera')->findOrFail($id);

        // Guardar grupo activo
        session([
            'grupo_activo_id'      => $grupo->id,
            'grupo_activo_nombre'  => $grupo->nombre,
            'grupo_activo_carrera' => $grupo->carrera->nombre ?? '',
        ]);

        // ===== 🔥 NUEVO: Actualizar historial de grupos recientes =====
        $recientes = session('grupos_recientes', []);

        // Remover el grupo si ya estaba en el historial (para moverlo al inicio)
        $recientes = array_filter($recientes, function ($item) use ($id) {
            return $item !== $id;
        });

        // Agregar el grupo al inicio
        array_unshift($recientes, $id);

        // Limitar a 3 grupos
        $recientes = array_slice($recientes, 0, 3);

        // Guardar en sesión
        session(['grupos_recientes' => $recientes]);

        $dashboard = $user->rol === 'admin' ? 'admin.dashboard' : 'docente.dashboard';

        return redirect()->route($dashboard)
            ->with('success', 'Grupo ' . $grupo->nombre . ' seleccionado.');
    }
    /**
     * Limpia el grupo activo de sesión.
     */
    public function limpiarSeleccion()
    {
        session()->forget(['grupo_activo_id', 'grupo_activo_nombre', 'grupo_activo_carrera']);
        return redirect()->route('grupos');
    }

    /**
     * Elimina un grupo (solo admin).
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $grupo = grupos::findOrFail($id);

            if (session('grupo_activo_id') == $id) {
                session()->forget(['grupo_activo_id', 'grupo_activo_nombre', 'grupo_activo_carrera']);
            }

            $grupo->delete();
            DB::commit();

            return redirect()->route('gestion', ['tab' => 'grupos'])
                ->with('success', 'Grupo eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestion', ['tab' => 'grupos'])
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
