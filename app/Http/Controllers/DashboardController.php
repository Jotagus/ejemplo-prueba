<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\DetalleMaterial;
use App\Models\Material;
use App\Models\Movimiento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Contadores para las tarjetas ──
        $totalMateriales     = Material::count();
        $movimientosMes      = Movimiento::whereMonth('fecha', now()->month)
                                         ->whereYear('fecha', now()->year)
                                         ->count();
        $totalBajoStock      = DetalleMaterial::whereColumn('cantidad_actual', '<=', 'cantidad_minima')->count();

        // ── Materiales con bajo stock / crítico para la tabla ──
        $materialesBajoStock = Material::with(['detalleMaterial', 'unidadMedida'])
            ->whereHas('detalleMaterial', function ($q) {
                $q->whereColumn('cantidad_actual', '<=', 'cantidad_minima');
            })
            ->orderByRaw('(SELECT cantidad_actual FROM detalle_material WHERE material_id = materiales.id) ASC')
            ->take(5)
            ->get();

        // ── Movimientos recientes ──
        $movimientosRecientes = Movimiento::with('material')
            ->latest('fecha')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalMateriales',
            'movimientosMes',
            'totalBajoStock',
            'materialesBajoStock',
            'movimientosRecientes'
        ));
    }
}