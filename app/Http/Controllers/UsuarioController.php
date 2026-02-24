<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Usuario;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // ── INDEX ──
    public function index()
    {
        $usuarios = Usuario::with('rol')->orderBy('created_at', 'desc')->get();
        $roles    = Rol::orderBy('nombre')->get();

        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    // ── STORE ──
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6|confirmed',
            'rol_id'   => 'required|exists:roles,id',
            'estado'   => 'required|in:0,1',
        ], [
            'nombre.required'    => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol_id.required'    => 'Debes asignar un rol.',
        ]);

        $usuario = Usuario::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol_id'   => $request->rol_id,
            'estado'   => (bool) $request->estado,
        ]);

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Usuarios',
            'Crear',
            'Creó el usuario: "' . $usuario->nombre . '" - Correo: ' . $usuario->email . ' - Rol: ' . $usuario->rol->nombre
        );

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // ── UPDATE ──
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email,' . $usuario->id,
            'rol_id'   => 'required|exists:roles,id',
            'estado'   => 'required|in:0,1',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ((bool) $request->estado === false) {
            $esAdmin     = Rol::where('nombre', 'admin')->first();
            $adminsActivos = Usuario::where('rol_id', $esAdmin->id)
                ->where('estado', true)
                ->where('id', '!=', $usuario->id)
                ->count();

            if ($adminsActivos === 0) {
                return redirect()->route('usuarios.index')
                    ->with('error', 'No puedes desactivar el único administrador activo del sistema.');
            }
        }

        $nombreAnterior = $usuario->nombre;
        $emailAnterior  = $usuario->email;
        $rolAnterior    = $usuario->rol->nombre;
        $estadoAnterior = $usuario->estado ? 'Activo' : 'Inactivo';

        $datos = [
            'nombre' => $request->nombre,
            'email'  => $request->email,
            'rol_id' => $request->rol_id,
            'estado' => (bool) $request->estado,
        ];

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        // ── AUDITORÍA ──
        $cambioPassword = $request->filled('password') ? ' | Contraseña: actualizada' : '';
        Auditoria::registrar(
            'Usuarios',
            'Editar',
            'Actualizó usuario: "' . $nombreAnterior . '" → "' . $usuario->nombre . '" - ' .
            'Correo: ' . $emailAnterior . ' → ' . $usuario->email . ' - ' .
            'Rol: ' . $rolAnterior . ' → ' . $usuario->rol->nombre . ' - ' .
            'Estado: ' . $estadoAnterior . ' → ' . ($usuario->estado ? 'Activo' : 'Inactivo') .
            $cambioPassword
        );

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // ── TOGGLE ESTADO ──
    public function toggleEstado(Usuario $usuario)
    {
        if ($usuario->id === session('usuario_id')) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $estadoAnterior = $usuario->estado ? 'Activo' : 'Inactivo';

        $usuario->update(['estado' => !$usuario->estado]);

        $nuevoEstado = $usuario->estado ? 'Activo' : 'Inactivo';

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Usuarios',
            'Editar',
            'Cambió estado del usuario: "' . $usuario->nombre . '" - ' . $estadoAnterior . ' → ' . $nuevoEstado
        );

        $msg = $usuario->estado ? 'activado' : 'desactivado';
        return redirect()->route('usuarios.index')
            ->with('success', "Usuario {$msg} correctamente.");
    }

    // ── DESTROY ──
    public function destroy(Usuario $usuario)
    {
        if ($usuario->id === session('usuario_id')) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $nombre = $usuario->nombre;
        $email  = $usuario->email;
        $rol    = $usuario->rol->nombre;

        $usuario->delete();

        // ── AUDITORÍA ──
        Auditoria::registrar(
            'Usuarios',
            'Eliminar',
            'Eliminó el usuario: "' . $nombre . '" - Correo: ' . $email . ' - Rol: ' . $rol
        );

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}