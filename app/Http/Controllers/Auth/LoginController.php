<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('login');
    }

    // Procesar el login
    public function login(Request $request)
    {
        // Validar credenciales
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $token = JWTAuth::fromUser($user);

            // Redirigir según rol, adjuntando el token como cookie (accesible por JS)
            $dashboardRoute = match ($user->rol) {
                'admin' => route('admin.dashboard'),
                'docente' => route('docente.dashboard'),
                'alumno' => route('alumno.dashboard'),
            };

            //log
            registrar_log('login', 'Inició sesión', 'auth');
            // Cookie con el token (ultimas 2 son, secure, httpOnly)
            return redirect($dashboardRoute)->cookie('jwt_token', $token, 60 * 24 * 7, null, null, false, false);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ])->onlyInput('email');
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        // Invalidar token JWT si existe
        if ($token = $request->cookie('jwt_token')) {
            JWTAuth::setToken($token)->invalidate();
        }

        //log
        registrar_log('logout', 'Cerró sesión', 'auth');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->cookie('jwt_token', '', -1); // eliminar cookie
    }
}
