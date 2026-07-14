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
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\UserController;
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

// Ruta AGENDA para TODOS los roles autenticados (ALUMNOS, DOCENTES, ADMIN)
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', [AsesoriaController::class, 'agendar'])->name('agenda');
});

// Rutas protegidas para el admin
Route::middleware(['auth', 'rol:admin'])->group(function () {

    Route::get('/admin/dashboard', [RespaldoController::class, 'dashboard'])->name('admin.dashboard');

    // ========== ROLES Y PERMISOS ==========
    Route::get('/roles_permisos', function () {
        $usuarios = App\Models\User::with(['docente', 'alumno'])->get();
        return view('admin.rolesPermisos', compact('usuarios'));
    })->name('roles_permisos');

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
        logs::truncate();
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
    Route::get('/gestionar', function () {
        $alumnos = alumnos::with(['user', 'carrera', 'grupo'])->paginate(10);
        $docentes = docentes::with(['user', 'carrera'])->paginate(10);
        $grupos = grupos::with(['carrera'])->withCount('alumnos')->paginate(10);
        return view('admin.gestion', compact('alumnos', 'docentes', 'grupos'));
    })->name('gestion');

    // Rutas de respaldos
    Route::post('/respaldo/generar', [RespaldoController::class, 'generate'])->name('respaldo.generar');
    Route::get('/respaldo/automatico', [RespaldoController::class, 'automaticoForm'])->name('respaldo.automatico.form');
    Route::post('/respaldo/automatico', [RespaldoController::class, 'automatico'])->name('respaldo.automatico.store');
    Route::get('/respaldo/descargar/{archivo}', [RespaldoController::class, 'download'])->name('respaldo.descargar');
});

// Rutas protegidas para docentes
Route::middleware(['auth', 'rol:docente'])->group(function () {
    Route::get('/docente/dashboard', function () {
        return view('auth.docentes.escritorioDocente');
    })->name('docente.dashboard');

    // RUTAS DE ASESORÍAS
    Route::get('/docente/asesoria', [AsesoriaController::class, 'create'])->name('registro');
    Route::post('/docente/asesoria', [AsesoriaController::class, 'store'])->name('asesoria.store');
});

// Rutas protegidas para alumnos
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno/dashboard', function () {
        return view('auth.alumnos.escritorioAlumno');
    })->name('alumno.dashboard');

    Route::get('/solicitud', function () {
        return view('auth.alumnos.solicitud_asesorias');
    })->name('solicitud');
});

// Rutas compartidas (docentes y admin)
Route::middleware(['auth', 'rol:admin,docente'])->group(function () {
    Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos');
    Route::post('/grupos/{id}/seleccionar', [GrupoController::class, 'seleccionar'])->name('grupos.seleccionar');
    Route::post('/grupos/limpiar', [GrupoController::class, 'limpiarSeleccion'])->name('grupos.limpiar');

    Route::get('/alumnos', [AlumnoController::class, 'listar'])->name('alumnos');
    Route::get('/alumnos/expediente/{id}', [AlumnoController::class, 'expediente'])->name('expedienteAlumnos');

    Route::get('/historial', function () {
        return view('auth.historial');
    })->name('historial');

    // Rutas para PDF
    Route::post('/docente/asesoria/reporte/generar', [AsesoriaController::class, 'generarReporte'])->name('asesoria.reporte.generar');
    // Ver reporte (PDF) desde el expediente
    Route::get('/docente/reporte/{id}', function ($id) {
        $reporte = \App\Models\reportes_asesoria::findOrFail($id);
        return response()->file(storage_path('app/public/' . $reporte->ruta));
    })->name('reporte.ver');
});

// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Resetear navegación (para el botón Inicio)
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
