<?php

namespace App\Http\Controllers;

use App\Models\RegistroAuditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = RegistroAuditoria::with('usuario')
            ->orderBy('fecha', 'desc');

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $registros = $query->paginate(10)->withQueryString();

        // Para los selects de filtro
        $modulos = RegistroAuditoria::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = RegistroAuditoria::select('accion')->distinct()->orderBy('accion')->pluck('accion');

        return view('auditoria.index', compact('registros', 'modulos', 'acciones'));
    }

    public function destroy($id)
    {
        RegistroAuditoria::findOrFail($id)->delete();

        return redirect()->route('auditoria.index')
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function limpiar(Request $request)
    {
        $request->validate([
            'dias' => 'required|integer|in:0,30,60,90,180',
        ]);

        $total = RegistroAuditoria::where('fecha', '<', now()->subDays($request->dias))->count();
        RegistroAuditoria::where('fecha', '<', now()->subDays($request->dias))->delete();

        return redirect()->route('auditoria.index')
            ->with('success', 'Se eliminaron ' . $total . ' registros anteriores a ' . $request->dias . ' días.');
    }
}