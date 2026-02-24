<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        $materiales = Material::with([
            'subcategoria.categoria',
            'unidadMedida',
            'detalleMaterial',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventario.index', compact('materiales'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'cantidad_actual' => 'required|numeric|min:0',
            'cantidad_minima' => 'required|numeric|min:0',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $detalle = $material->detalleMaterial;

        if ($detalle) {
            $cantidadAnterior = $detalle->cantidad_actual;
            $minimaAnterior   = $detalle->cantidad_minima;
            $precioAnterior   = $detalle->precio_unitario;

            $detalle->update([
                'cantidad_actual' => $request->cantidad_actual,
                'cantidad_minima' => $request->cantidad_minima,
                'precio_unitario' => $request->precio_unitario,
                'costo_total'     => $request->cantidad_actual * $request->precio_unitario,
            ]);

            // ── AUDITORÍA ──
            Auditoria::registrar(
                'Inventario',
                'Editar',
                'Actualizó inventario de "' . $material->nombre . '" - ' .
                'Cantidad: ' . $cantidadAnterior . ' → ' . $request->cantidad_actual . ' | ' .
                'Mínimo: '   . $minimaAnterior   . ' → ' . $request->cantidad_minima . ' | ' .
                'Precio: Bs. ' . $precioAnterior . ' → Bs. ' . $request->precio_unitario
            );
        }

        return redirect()
            ->route('inventario.index')
            ->with('success', 'Inventario de "' . $material->nombre . '" actualizado correctamente.');
    }
}