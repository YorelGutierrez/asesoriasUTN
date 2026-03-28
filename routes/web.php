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

// Rutas POST
Route::post('/login', [LoginController::class, 'login'])->name('login.procesar');
Route::post('/registro', [RegistroController::class, 'register'])->name('registro.procesar');

// Ruta protegida (Solo acceso a usuarios autenticados)
Route::middleware(['auth'])->group(function () {
    Route::get('/Escritorio', function () {
        return view('Escritorio');
    })->name('dashboard');

    Route::get('/grupos', function () {
        return view('grupos');
    })->name('grupos');

    Route::get('/alumnos', function () {
        return view('alumnos');
    })->name('alumnos');

    Route::get('/agenda', function () {
        return view('agendar');
    })->name('agenda');

    Route::get('/alumnos/expediente', function () {
        return view('expediente_alumnos');
    })->name('expedienteAlumnos');

    Route::get('/historial', function () {
        return view('historial');
    })->name('historial');
});


// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grupo de rutas para admin (solo usuarios con rol 'admin')
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');

    Route::get('/roles_permisos', function () {
        return view('admin/rolesPermisos');
    })->name('roles_permisos');

    Route::get('/registro/alumno', function () {
        return view('admin/registroAlumnos');
    })->name('registro_alumnos');

    Route::get('/registro/docente', function () {
        return view('admin/registroDocentes');
    })->name('registro_docente');
});
