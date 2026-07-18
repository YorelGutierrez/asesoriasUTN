<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm() { return view('login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

       $user = User::where('email', $request->email)->first();

        // Cuenta bloqueada → mensaje con session('error')
        if ($user && !$user->estado) {
            return back()->with('error', 'Tu cuenta está bloqueada. Contacta al administrador.');
        }

        $cacheKey = 'login_attempts_' . $request->email;
        $attempts = Cache::get($cacheKey, 0);
        $attemptsLeft = 3 - $attempts; // ← AGREGADO: intentos restantes

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            Cache::forget($cacheKey);
            $request->session()->regenerate();
            $token = JWTAuth::fromUser(Auth::user());

            $user = Auth::user();
            $nombre = $user->nombres . ' ' . $user->apellido_paterno;
            
            // Mensaje personalizado según el rol
            $mensaje = match ($user->rol) {
                'admin' => "¡Bienvenido Administrador {$nombre}!",
                'docente' => "¡Bienvenido Docente {$nombre}!",
                'alumno' => "¡Bienvenido Alumno {$nombre}!",
                default => "¡Bienvenido {$nombre}!",
            };

            $dashboard = match (Auth::user()->rol) {
                'admin' => route('admin.dashboard'),
                'docente' => route('docente.dashboard'),
                'alumno' => route('alumno.dashboard'),
                default => '/',
            };

            registrar_log('login', 'Inició sesión', 'auth');

            session()->flash('success', $mensaje);
            
            return redirect($dashboard)->cookie('jwt_token', $token, 60*24*7, null, null, false, false)->with('success', $mensaje);
        }

        $attempts++;
        Cache::put($cacheKey, $attempts, now()->addMinutes(15));

        // Bloqueo por intentos fallidos → mensaje con session('error')
        if ($attempts >= 3 && $user) {
            $user->estado = false;
            $user->save();
            Cache::forget($cacheKey);
            return back()->with('error', 'Has superado los 3 intentos permitidos. Tu cuenta ha sido bloqueada.');
        }

        // ← AGREGADO: Mensaje con intentos restantes para credenciales incorrectas
        if ($user && $attemptsLeft > 0) {
            return back()->with('warning', "Correo o contraseña incorrectos. Te quedan {$attemptsLeft} intento(s).");
        }

        // ← AGREGADO: Usuario no registrado
        if (!$user) {
            return back()->with('warning', 'El correo electrónico no está registrado.');
        }

        // Otros errores de credenciales → se mantienen con withErrors
        return back()->withErrors(['email' => 'Las credenciales no coinciden.']);
    }

    public function logout(Request $request)
    {
        try {
            if ($token = $request->cookie('jwt_token')) {
                JWTAuth::setToken($token)->invalidate();
            }
        } catch (TokenExpiredException $e) {
            // Token ya expirado, no es necesario invalidarlo
        } catch (JWTException $e) {
            // Otro error con el token, ignoramos
        }

        registrar_log('logout', 'Cerró sesión', 'auth');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->cookie('jwt_token', '', -1);
    }
}