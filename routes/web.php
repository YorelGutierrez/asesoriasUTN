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

// Ruta protegida (Solo acceso a usuarios)
Route::get('/Escritorio', function(){
    return view('Escritorio');
})->name('dashboard')->middleware('auth');

// Cerrar sesión
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grupo de rutas para admin (solo usuarios con rol 'admin')
Route::middleware(['auth', 'rol:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');
    
});