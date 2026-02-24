<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // ── Muestra el formulario de login ──
    public function showLogin()
    {
        // Si ya hay sesión activa, redirige al dashboard
        if (session()->has('usuario_id')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    // ── Procesa el login ──
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'Ingresa un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        // Busca el usuario por email
        $usuario = Usuario::with('rol')
            ->where('email', $request->email)
            ->first();

        // Verifica que exista y que la contraseña sea correcta
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Correo o contraseña incorrectos.');
        }

        // Verifica que el usuario esté activo
        if (!$usuario->estado) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        // Guarda datos en sesión
        session([
            'usuario_id'     => $usuario->id,
            'usuario_nombre' => $usuario->nombre,
            'usuario_email'  => $usuario->email,
            'usuario_rol'    => $usuario->rol->nombre,
            'usuario_ini'    => $usuario->iniciales,
        ]);

        // Actualiza último acceso
        $usuario->update(['ultimo_acceso' => now()]);

        return redirect()->route('dashboard')
            ->with('success', '¡Bienvenido, ' . $usuario->nombre . '!');
    }

    // ── Cierra sesión ──
    public function logout(Request $request)
    {
        session()->flush();
        return redirect()->route('login')
            ->with('success', 'Sesión cerrada correctamente.');
    }
}