<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRol
{
    /**
     * Roles disponibles:
     *   admin       → acceso total
     *   almacenero  → CRUD en categorías, materiales, inventario, movimientos
     *   contable    → solo ver + exportar reportes
     *   invitado    → solo ver materiales e inventario
     *
     * Uso en rutas:
     *   ->middleware('auth.rol:admin')
     *   ->middleware('auth.rol:admin,almacenero')
     *   ->middleware('auth.rol:admin,almacenero,contable,invitado')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $rolUsuario = session('usuario_rol');

        if (!in_array($rolUsuario, $roles)) {
            // Si es una petición AJAX o espera JSON → 403 en JSON
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para realizar esta acción.'], 403);
            }

            // Si es una petición normal → redirige con mensaje
            return redirect()->back()
                ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}