<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LocalizationController;

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


// Rutas protegidas para el admin
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('/admin/dashboard');
    })->name('admin.dashboard');

    Route::get('/roles_permisos', function () {
        return view('/admin/rolesPermisos');
    })->name('roles_permisos');

    Route::get('/registro/alumno', function () {
        return view('/admin/registroAlumnos');
    })->name('registro_alumnos');

    Route::get('/registro/docente', function () {
        return view('/admin/registroDocentes');
    })->name('registro_docente');

    Route::get('/gestionar', function () {
        return view('/admin/gestion');
    })->name('gestion');
});

// Rutas protegidas para docentes
Route::middleware(['auth', 'rol:docente'])->group(function () {
    Route::get('/docente/dashboard', function () {
        return view('/auth/docentes/escritorioDocente');
    })->name('docente.dashboard');
});

//rutas protegidas para alumnos
Route::middleware(['auth', 'rol:alumno'])->group(function () {
    Route::get('/alumno/dashboard', function () {
        return view('/auth/alumnos/escritorioAlumno');
    })->name('alumno.dashboard');
});
Route::get('/solicitud', function () {
        return view('/auth/alumnos/solicitud_asesorias');
    })->name('solicitud');

//rutas protedigas para tutor
    //aun no poner nada aqui porfavor

//rutas compartidas (docentes -> admin)
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
