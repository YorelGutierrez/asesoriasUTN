<?php

use App\Http\Controllers\admin\RespaldoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\AsesoriaController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GraficasController;
use App\Http\Controllers\PerfilController;
use App\Models\alumnos;
use App\Models\docentes;
use App\Models\grupos;
use App\Models\logs;
use App\Models\reportes_asesoria;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
Route::get('lang/{locale}', [LocalizationController::class, 'setLang'])->name('lang.switch');
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/registro', [RegistroController::class, 'showRegistrationForm'])->name('register');

// Rutas POST publicas
Route::post('/login', [LoginController::class, 'login'])->name('login.procesar');
Route::post('/registro', [RegistroController::class, 'register'])->name('registro.procesar');

// Ruta AGENDA para TODOS los roles autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', [AsesoriaController::class, 'agendar'])->name('agenda');
    Route::post('/agenda', [AsesoriaController::class, 'storeAgenda'])->name('agenda.store');

    // ===== NOTIFICACIONES =====
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
    Route::post('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leerTodas');
    Route::post('/notificaciones/{id}/confirmar', [NotificacionController::class, 'confirmar'])->name('notificaciones.confirmar');
    Route::post('/notificaciones/{id}/rechazar', [NotificacionController::class, 'rechazar'])->name('notificaciones.rechazar');

    // ===== CALENDARIO =====
    Route::get('/calendario/sesiones', function () {
        $user = auth()->user();
        $mes = request('mes', now()->month);
        $anio = request('anio', now()->year);

        $query = \App\Models\sesiones_asesoria::where('estado', 'programada') // 👈 Solo programadas
            ->where('fecha_inicio', '>=', now()) // 👈 Solo futuras
            ->whereMonth('fecha_inicio', $mes)
            ->whereYear('fecha_inicio', $anio);

        if ($user->rol === 'docente') {
            $query->where('docente_id', $user->id);
        } elseif ($user->rol === 'alumno') {
            $query->whereHas('alumnos', function ($q) use ($user) {
                $q->where('sesion_alumno.alumno_id', $user->id);
            });
        }

        $dias = $query->pluck('fecha_inicio')
            ->map(fn($f) => (int) date('j', strtotime($f)))
            ->unique()
            ->values();

        return response()->json(['dias' => $dias]);
    })->name('calendario.sesiones');

    // ===== PERFIL =====
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::post('/perfil/cambiar-password', [PerfilController::class, 'cambiarPassword'])->name('perfil.cambiar.password');
    Route::post('/perfil/cambiar-foto', [PerfilController::class, 'cambiarFoto'])->name('perfil.cambiar.foto');
});

// ============================================================
// RUTAS ADMIN
// ============================================================
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/dashboard', [RespaldoController::class, 'dashboard'])->name('admin.dashboard');

    // ========== ROLES Y PERMISOS ==========
    Route::get('/roles_permisos', [UserController::class, 'rolesPermisos'])->name('roles_permisos');

    // ========== RUTAS DE ALUMNOS ==========
    Route::get('/registro/alumno', [AlumnoController::class, 'create'])->name('registro_alumnos');
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::get('/alumnos/{id}/editar', [AlumnoController::class, 'edit'])->name('alumnos.edit');
    Route::put('/alumnos/{id}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

    // ========== RUTAS DE DOCENTES ==========
    Route::get('/registro/docente', [DocenteController::class, 'create'])->name('registro_docente');
    Route::post('/docentes', [DocenteController::class, 'store'])->name('docentes.store');
    Route::get('/docentes/{id}/editar', [DocenteController::class, 'edit'])->name('docentes.edit');
    Route::put('/docentes/{id}', [DocenteController::class, 'update'])->name('docentes.update');
    Route::delete('/docentes/{id}', [DocenteController::class, 'destroy'])->name('docentes.destroy');

    // ========== RUTAS DE GRUPOS ==========
    Route::delete('/grupos/{id}', [GrupoController::class, 'destroy'])->name('grupos.destroy');

    // ========== RUTAS DE BITÁCORA ==========
    Route::delete('/bitacora/limpiar', function () {
        logs::query()->delete();
        return redirect()->back()->with('success', 'Bitácora limpiada');
    })->name('bitacora.limpiar');

    Route::delete('/bitacora/eliminar/{id}', function ($id) {
        $log = logs::findOrFail($id);
        $log->delete();
        return redirect()->back()->with('success', 'Registro eliminado');
    })->name('bitacora.eliminar');

    // ========== RUTA DE BLOQUEO/DESBLOQUEO ==========
    Route::post('/usuarios/bloquear/{id}', [UserController::class, 'toggleBlock'])->name('usuarios.toggleBlock');

    // ========== RUTA GESTIÓN ==========
    Route::get('/gestionar', [GestionController::class, 'index'])->name('gestion');

    // ========== RESPALDOS =============
    Route::post('/respaldo/generar', [RespaldoController::class, 'generate'])->name('respaldo.generar');
    Route::get('/respaldo/automatico', [RespaldoController::class, 'automaticoForm'])->name('respaldo.automatico.form');
    Route::post('/respaldo/automatico', [RespaldoController::class, 'automatico'])->name('respaldo.automatico.store');
    Route::get('/respaldo/descargar/{archivo}', [RespaldoController::class, 'download'])->name('respaldo.descargar');
    Route::post('/respaldo/restaurar', [RespaldoController::class, 'restaurar'])->name('respaldo.restaurar');
    Route::get('/respaldo/listar', [RespaldoController::class, 'listar'])->name('respaldo.listar');

    //========== RUTAS DE ASIGNACIONES ==========
    Route::get('/admin/asignaciones', [\App\Http\Controllers\Admin\AsignacionController::class, 'index'])->name('admin.asignaciones');
    Route::get('/admin/asignaciones/docentes', [\App\Http\Controllers\Admin\AsignacionController::class, 'docentesPorCarrera'])->name('admin.asignaciones.docentes');
    Route::get('/admin/asignaciones/grupos', [\App\Http\Controllers\Admin\AsignacionController::class, 'gruposPorCarrera'])->name('admin.asignaciones.grupos');
    Route::get('/admin/asignaciones/materias/{docenteId}', [\App\Http\Controllers\Admin\AsignacionController::class, 'materiasDocente'])->name('admin.asignaciones.materias');
    Route::get('/admin/asignaciones/editar/{id}', [\App\Http\Controllers\Admin\AsignacionController::class, 'editar'])->name('admin.asignaciones.editar');
    Route::post('/admin/asignaciones', [\App\Http\Controllers\Admin\AsignacionController::class, 'store'])->name('admin.asignaciones.store');
    Route::put('/admin/asignaciones/{id}', [\App\Http\Controllers\Admin\AsignacionController::class, 'update'])->name('admin.asignaciones.update');
    Route::delete('/admin/asignaciones/{id}', [\App\Http\Controllers\Admin\AsignacionController::class, 'destroy'])->name('admin.asignaciones.destroy');
});

// ============================================================
// RUTAS DOCENTE
// ============================================================
Route::middleware(['auth', 'rol:docente'])->group(function () {
    Route::get('/docente/dashboard', [DocenteController::class, 'dashboardDocente'])->name('docente.dashboard');

    Route::get('/docente/asesoria', [AsesoriaController::class, 'create'])->name('registro');
    Route::post('/docente/asesoria', [AsesoriaController::class, 'store'])->name('asesoria.store');
});

// ============================================================
// RUTAS ALUMNO
// ============================================================
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno/dashboard', [AlumnoController::class, 'dashboardAlumno'])->name('alumno.dashboard');
});

// ============================================================
// RUTAS COMPARTIDAS (Admin y Docente)
// ============================================================
Route::middleware(['auth', 'rol:admin,docente'])->group(function () {
    Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos');
    Route::post('/grupos/{id}/seleccionar', [GrupoController::class, 'seleccionar'])->name('grupos.seleccionar');
    Route::post('/grupos/limpiar', [GrupoController::class, 'limpiarSeleccion'])->name('grupos.limpiar');

    Route::get('/alumnos', [AlumnoController::class, 'listar'])->name('alumnos');
    Route::get('/alumnos/expediente/{id}', [AlumnoController::class, 'expediente'])->name('expedienteAlumnos');

    Route::get('/historial', [AsesoriaController::class, 'historial'])->name('historial');

    Route::post('/docente/asesoria/reporte/generar', [AsesoriaController::class, 'generarReporte'])->name('asesoria.reporte.generar');
    Route::get('/docente/reporte/{id}', function ($id) {
        $reporte = \App\Models\reportes_asesoria::findOrFail($id);
        return response()->file(storage_path('app/public/' . $reporte->ruta));
    })->name('reporte.ver');
});

// ============================================================
// RUTAS PARA PDF DE GRÁFICAS
// ============================================================
Route::post('/imprimir-graficas-admin-pdf', [GraficasController::class, 'imprimirPDFAdmin'])->name('imprimir.graficas.admin.pdf');
Route::post('/imprimir-graficas-docente-pdf', [GraficasController::class, 'imprimirPDFDocente'])->name('imprimir.graficas.docente.pdf');

// ============================================================
// RUTAS API PARA BITÁCORA
// ============================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/api/logs', function () {
        $user = auth()->user();

        if ($user->rol === 'admin') {
            $logs = App\Models\logs::with('user')->latest()->take(10)->get();
        } else {
            $logs = App\Models\logs::with('user')->where('user_id', $user->id)->latest()->take(10)->get();
        }

        return response()->json($logs);
    });

    Route::delete('/api/logs/{id}', function ($id) {
        $log = App\Models\logs::findOrFail($id);
        $log->delete();
        return response()->json(['success' => true]);
    });
});

// ============================================================
// RUTA DE CIERRE DE SESIÓN
// ============================================================
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================
// RESETEO DE NAVEGACIÓN
// ============================================================
Route::get('/reset-navigation', function () {
    session()->forget('navigation_history');
    return redirect()->route(
        match (auth()->user()->rol) {
            'admin' => 'admin.dashboard',
            'docente' => 'docente.dashboard',
            default => 'alumno.dashboard'
        }
    );
})->name('reset.navigation');
