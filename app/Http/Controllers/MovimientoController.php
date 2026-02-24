<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Material;
use App\Models\DetalleMaterial;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimientoController extends Controller
{
    public function index(Request $request)
    {
        $por_pagina = $request->get('per_page', 10);

        $query = Movimiento::with(['material.subcategoria.categoria', 'material.unidadMedida'])
            ->orderBy('fecha', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }
        if ($request->filled('tipo_movimiento')) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->paginate($por_pagina)->withQueryString();
        $materiales  = Material::with('subcategoria')->orderBy('nombre')->get();

        return view('movimientos.index', compact('movimientos', 'materiales'));
    }

    public function store(Request $request)
    {
        $rules = [
            'material_id'     => 'required|exists:materiales,id',
            'tipo_movimiento' => 'required|in:entrada,salida',
            'fecha'           => 'required|date',
            'cantidad'        => 'required|numeric|min:0.01',
            'costo_unitario'  => 'required|numeric|min:0',
            'observaciones'   => 'nullable|string',
        ];

        if ($request->tipo_movimiento === 'entrada') {
            $rules['numero_factura'] = 'nullable|string|max:50';
            $rules['numero_ingreso'] = 'nullable|string|max:50';
        } else {
            $rules['numero_salida']      = 'required|string|max:50';
            $rules['unidad_solicitante'] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $material = Material::findOrFail($request->material_id);
            $detalle  = $material->detalleMaterial;

            if (!$detalle) {
                throw new \Exception('El material no tiene detalle de inventario configurado');
            }

            $ultimoMovimiento   = Movimiento::where('material_id', $request->material_id)
                ->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();

            $saldoAnterior      = $ultimoMovimiento ? $ultimoMovimiento->saldo_cantidad   : $detalle->cantidad_actual;
            $costoTotalAnterior = $ultimoMovimiento ? $ultimoMovimiento->saldo_costo_total : $detalle->costo_total;

            if ($request->tipo_movimiento === 'entrada') {
                $nuevoSaldoCantidad   = $saldoAnterior + $request->cantidad;
                $nuevoSaldoCostoTotal = $costoTotalAnterior + ($request->cantidad * $request->costo_unitario);
            } else {
                if ($saldoAnterior < $request->cantidad) {
                    throw new \Exception('Stock insuficiente. Disponible: ' . $saldoAnterior);
                }
                $nuevoSaldoCantidad   = $saldoAnterior - $request->cantidad;
                $nuevoSaldoCostoTotal = $costoTotalAnterior - ($request->cantidad * $request->costo_unitario);
            }

            $movimiento = Movimiento::create([
                'material_id'        => $request->material_id,
                'tipo_movimiento'    => $request->tipo_movimiento,
                'fecha'              => $request->fecha,
                'numero_factura'     => $request->numero_factura,
                'numero_ingreso'     => $request->numero_ingreso,
                'numero_salida'      => $request->numero_salida,
                'unidad_solicitante' => $request->unidad_solicitante,
                'cantidad'           => $request->cantidad,
                'costo_unitario'     => $request->costo_unitario,
                'total'              => $request->cantidad * $request->costo_unitario,
                'saldo_cantidad'     => $nuevoSaldoCantidad,
                'saldo_costo_total'  => $nuevoSaldoCostoTotal,
                'observaciones'      => $request->observaciones,
            ]);

            $detalle->update([
                'cantidad_actual' => $nuevoSaldoCantidad,
                'costo_total'     => $nuevoSaldoCostoTotal,
                'precio_unitario' => $nuevoSaldoCantidad > 0
                    ? $nuevoSaldoCostoTotal / $nuevoSaldoCantidad
                    : $detalle->precio_unitario,
            ]);

            $tipo = $request->tipo_movimiento === 'entrada' ? 'Entrada' : 'Salida';
            Auditoria::registrar(
                'Movimientos',
                'Crear',
                'Registró ' . $tipo . ' del material: "' . $material->nombre . '" - Cantidad: ' . $request->cantidad . ' - Total: Bs. ' . number_format($request->cantidad * $request->costo_unitario, 2)
            );

            DB::commit();
            return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar movimiento: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Movimiento $movimiento)
    {
        $rules = [
            'material_id'     => 'required|exists:materiales,id',
            'tipo_movimiento' => 'required|in:entrada,salida',
            'fecha'           => 'required|date',
            'cantidad'        => 'required|numeric|min:0.01',
            'costo_unitario'  => 'required|numeric|min:0',
            'observaciones'   => 'nullable|string',
        ];

        if ($request->tipo_movimiento === 'entrada') {
            $rules['numero_factura'] = 'nullable|string|max:50';
            $rules['numero_ingreso'] = 'nullable|string|max:50';
        } else {
            $rules['numero_salida']      = 'required|string|max:50';
            $rules['unidad_solicitante'] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $materialAnterior = $movimiento->material->nombre;
            $tipoAnterior     = $movimiento->tipo_movimiento;
            $cantidadAnterior = $movimiento->cantidad;

            $this->revertirMovimiento($movimiento);

            $material = Material::findOrFail($request->material_id);
            $detalle  = $material->detalleMaterial;

            $ultimoMovimiento = Movimiento::where('material_id', $request->material_id)
                ->where('id', '!=', $movimiento->id)
                ->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();

            $saldoAnterior      = $ultimoMovimiento ? $ultimoMovimiento->saldo_cantidad   : $detalle->cantidad_actual;
            $costoTotalAnterior = $ultimoMovimiento ? $ultimoMovimiento->saldo_costo_total : $detalle->costo_total;

            if ($request->tipo_movimiento === 'entrada') {
                $nuevoSaldoCantidad   = $saldoAnterior + $request->cantidad;
                $nuevoSaldoCostoTotal = $costoTotalAnterior + ($request->cantidad * $request->costo_unitario);
            } else {
                if ($saldoAnterior < $request->cantidad) {
                    throw new \Exception('Stock insuficiente. Disponible: ' . $saldoAnterior);
                }
                $nuevoSaldoCantidad   = $saldoAnterior - $request->cantidad;
                $nuevoSaldoCostoTotal = $costoTotalAnterior - ($request->cantidad * $request->costo_unitario);
            }

            $movimiento->update([
                'material_id'        => $request->material_id,
                'tipo_movimiento'    => $request->tipo_movimiento,
                'fecha'              => $request->fecha,
                'numero_factura'     => $request->numero_factura,
                'numero_ingreso'     => $request->numero_ingreso,
                'numero_salida'      => $request->numero_salida,
                'unidad_solicitante' => $request->unidad_solicitante,
                'cantidad'           => $request->cantidad,
                'costo_unitario'     => $request->costo_unitario,
                'total'              => $request->cantidad * $request->costo_unitario,
                'saldo_cantidad'     => $nuevoSaldoCantidad,
                'saldo_costo_total'  => $nuevoSaldoCostoTotal,
                'observaciones'      => $request->observaciones,
            ]);

            $detalle->update([
                'cantidad_actual' => $nuevoSaldoCantidad,
                'costo_total'     => $nuevoSaldoCostoTotal,
                'precio_unitario' => $nuevoSaldoCantidad > 0
                    ? $nuevoSaldoCostoTotal / $nuevoSaldoCantidad
                    : $detalle->precio_unitario,
            ]);

            Auditoria::registrar(
                'Movimientos',
                'Editar',
                'Editó movimiento de "' . $materialAnterior . '" - Tipo anterior: ' . $tipoAnterior . ' (' . $cantidadAnterior . ') → Nuevo tipo: ' . $request->tipo_movimiento . ' (' . $request->cantidad . ')'
            );

            DB::commit();
            return redirect()->route('movimientos.index')->with('success', 'Movimiento actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar movimiento: ' . $e->getMessage());
        }
    }

    public function destroy(Movimiento $movimiento)
    {
        DB::beginTransaction();
        try {
            $nombreMaterial = $movimiento->material->nombre;
            $tipo           = $movimiento->tipo_movimiento;
            $cantidad       = $movimiento->cantidad;

            $this->revertirMovimiento($movimiento);
            $movimiento->delete();

            Auditoria::registrar(
                'Movimientos',
                'Eliminar',
                'Eliminó movimiento de "' . $nombreMaterial . '" - Tipo: ' . $tipo . ' - Cantidad: ' . $cantidad
            );

            DB::commit();
            return redirect()->route('movimientos.index')->with('success', 'Movimiento eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar movimiento: ' . $e->getMessage());
        }
    }

    private function revertirMovimiento(Movimiento $movimiento)
    {
        $detalle = $movimiento->material->detalleMaterial;

        if ($movimiento->tipo_movimiento === 'entrada') {
            $nuevaCantidad   = $detalle->cantidad_actual - $movimiento->cantidad;
            $nuevoCostoTotal = $detalle->costo_total - $movimiento->total;
        } else {
            $nuevaCantidad   = $detalle->cantidad_actual + $movimiento->cantidad;
            $nuevoCostoTotal = $detalle->costo_total + $movimiento->total;
        }

        $detalle->update([
            'cantidad_actual' => $nuevaCantidad,
            'costo_total'     => $nuevoCostoTotal,
            'precio_unitario' => $nuevaCantidad > 0
                ? $nuevoCostoTotal / $nuevaCantidad
                : $detalle->precio_unitario,
        ]);
    }

    public function kardex($materialId)
    {
        $material    = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial'])->findOrFail($materialId);
        $movimientos = Movimiento::where('material_id', $materialId)
            ->orderBy('fecha', 'asc')->orderBy('created_at', 'asc')->get();

        return view('movimientos.kardex', compact('material', 'movimientos'));
    }

    public function exportarKardexPDF($materialId)
    {
        $material    = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial'])->findOrFail($materialId);
        $movimientos = Movimiento::where('material_id', $materialId)
            ->orderBy('fecha', 'asc')->orderBy('created_at', 'asc')->get();

        $pdf = Pdf::loadView('movimientos.kardex_pdf', compact('material', 'movimientos'))
            ->setPaper('legal', 'landscape')
            ->setOption('margin-top', 5)
            ->setOption('margin-right', 5)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 5);

        return $pdf->download('Kardex_' . $material->codigo . '_' . date('Y-m-d') . '.pdf');
    }

    public function exportarKardexExcel($materialId)
    {
        $material = Material::findOrFail($materialId);

        return (new \App\Exports\KardexExport($materialId))
            ->download('Kardex_' . $material->codigo . '_' . date('Y-m-d') . '.xlsx');
    }
}