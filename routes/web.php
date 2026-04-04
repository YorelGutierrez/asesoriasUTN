<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocalizationController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AlumnoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rutas públicas
Route::get('lang/{locale}', [LocalizationController::class, 'setLang'])->name('lang.switch'); //cambiar idioma
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/registro', [RegistroController::class, 'showRegistrationForm'])->name('register');

// Rutas POST publicas
Route::post('/login', [LoginController::class, 'login'])->name('login.procesar');
Route::post('/registro', [RegistroController::class, 'register'])->name('registro.procesar');

//modificacion del 03/04/2026
// Rutas protegidas para el admin
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('/admin/dashboard');
    })->name('admin.dashboard');

    Route::get('/roles_permisos', function () {
        return view('/admin/rolesPermisos');
    })->name('roles_permisos');

    // ========== RUTAS DE ALUMNOS ==========
    // Ruta para mostrar el formulario (usa el controlador)
    Route::get('/registro/alumno', [AlumnoController::class, 'create'])->name('registro_alumnos');
    // Ruta para guardar los datos
    Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');

    // ========== RUTAS DE DOCENTES ==========
    Route::get('/registro/docente', [DocenteController::class, 'create'])->name('registro_docente');
    Route::post('/docentes', [DocenteController::class, 'store'])->name('docentes.store');
    Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes.index');
    Route::get('/docentes/{id}', [DocenteController::class, 'show'])->name('docentes.show');
    Route::get('/docentes/{id}/edit', [DocenteController::class, 'edit'])->name('docentes.edit');
    Route::put('/docentes/{id}', [DocenteController::class, 'update'])->name('docentes.update');
    Route::delete('/docentes/{id}', [DocenteController::class, 'destroy'])->name('docentes.destroy');

    Route::get('/gestionar', function () {
        return view('/admin/gestion');
    })->name('gestion');
});
// Rutas protegidas para docentes
Route::middleware(['auth', 'rol:docente'])->group(function () {
    Route::get('/docente/dashboard', function () {
        return view('/auth/docentes/escritorioDocente');
    })->name('docente.dashboard');

    Route::get('/registro', function () {
        return view('/auth/docentes/registro_asesorias');
    })->name('registro');
});

//rutas protegidas para alumnos
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno/dashboard', function () {
        return view('/auth/alumnos/escritorioAlumno');
    })->name('alumno.dashboard');

    Route::get('/solicitud', function () {
        return view('/auth/alumnos/solicitud_asesorias');
    })->name('solicitud');
});


//rutas protedigas para tutor
    //aun no poner nada aqui porfavor

//rutas compartidas (docentes y admin)
Route::middleware(['auth', 'rol:admin,docente'])->group(function () {
    Route::get('/grupos', function () {
        return view('/auth/grupos');
    })->name('grupos');

    Route::get('/alumnos', function () {
        return view('/auth/alumnos');
    })->name('alumnos');

    Route::get('/agenda', function () {
        return view('/auth/agendar');
    })->name('agenda');

    Route::get('/alumnos/expediente', function () {
        return view('/auth/expediente_alumnos');
    })->name('expedienteAlumnos');

    Route::get('/historial', function () {
        return view('/auth/historial');
    })->name('historial');
});

// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');