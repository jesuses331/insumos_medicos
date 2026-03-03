<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutenticacionController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function mostrarFormulario()
    {
        if (Auth::check()) {
            return redirect()->route('admin.tablero');
        }
        return view('auth.login');
    }

    /**
     * Procesar el intento de inicio de sesión.
     */
    public function acceder(Request $solicitud)
    {
        $credenciales = $solicitud->validate([
            'correo' => ['required', 'email'],
            'contrasena' => ['required'],
        ]);

        // Adaptar nombres de campos al estándar de Laravel para Auth
        if (Auth::attempt(['email' => $credenciales['correo'], 'password' => $credenciales['contrasena']], $solicitud->filled('recordar'))) {
            $solicitud->session()->regenerate();
            return redirect()->intended(route('admin.tablero'));
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    /**
     * Cerrar la sesión del usuario.
     */
    public function salir(Request $solicitud)
    {
        Auth::logout();
        $solicitud->session()->invalidate();
        $solicitud->session()->regenerateToken();

        return redirect('/');
    }
}
