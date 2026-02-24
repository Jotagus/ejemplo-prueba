<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Movimiento;
use App\Models\Categoria;
use App\Models\Subcategoria;
use App\Helpers\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        $materiales = Material::with('subcategoria')->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $subcategorias = Subcategoria::orderBy('nombre')->get();

        return view('reportes.index', compact('materiales', 'categorias', 'subcategorias'));
    }

    public function inventarioGeneral(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'categoria_id' => 'nullable|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
        ]);

        $fechaInicio = $request->fecha_inicio;
        $fechaFin = $request->fecha_fin;

        $query = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial']);

        if ($request->categoria_id) {
            $query->whereHas('subcategoria', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria_id);
            });
        }

        if ($request->subcategoria_id) {
            $query->where('subcategoria_id', $request->subcategoria_id);
        }

        $materiales = $query->orderBy('codigo')->get();
        $datosReporte = [];

        foreach ($materiales as $material) {
            $movimientoAnterior = Movimiento::where('material_id', $material->id)
                ->where('fecha', '<', $fechaInicio)
                ->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();

            $cantidadInicial = $movimientoAnterior ? $movimientoAnterior->saldo_cantidad : 0;
            $costoInicial = $movimientoAnterior ? $movimientoAnterior->saldo_costo_total : 0;

            $movimientoFinal = Movimiento::where('material_id', $material->id)
                ->where('fecha', '<=', $fechaFin)
                ->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();

            $cantidadFinal = $movimientoFinal ? $movimientoFinal->saldo_cantidad : $cantidadInicial;
            $costoFinal = $movimientoFinal ? $movimientoFinal->saldo_costo_total : $costoInicial;
            $precioUnitario = $material->detalleMaterial ? $material->detalleMaterial->precio_unitario : 0;

            $datosReporte[] = [
                'item' => $material->id,
                'codigo' => $material->codigo,
                'nombre' => $material->nombre,
                'categoria' => $material->subcategoria->categoria->nombre,
                'subcategoria' => $material->subcategoria->nombre,
                'unidad' => $material->unidadMedida->abreviatura,
                'cantidad_inicial' => $cantidadInicial,
                'saldo_inicial' => $costoInicial,
                'precio_unitario' => $precioUnitario,
                'cantidad_final' => $cantidadFinal,
                'saldo_final' => $costoFinal,
            ];
        }

        $data = [
            'titulo' => 'INVENTARIO DE BIENES DE CONSUMO',
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'materiales' => collect($datosReporte),
            'totalInicial' => collect($datosReporte)->sum('saldo_inicial'),
            'totalFinal' => collect($datosReporte)->sum('saldo_final'),
        ];

        $formato = $request->formato ?? 'vista';
        Auditoria::registrar(
            'Reportes',
            'Generar',
            'Generó reporte de Inventario General - Período: ' . $fechaInicio . ' al ' . $fechaFin . ' - Formato: ' . strtoupper($formato)
        );

        if ($request->formato === 'pdf')
            return $this->generarInventarioPDF($data);
        if ($request->formato === 'excel')
            return $this->generarInventarioExcel($data);

        return view('reportes.inventario_general', $data);
    }

    public function kardexMaterial(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materiales,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $material = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial'])
            ->findOrFail($request->material_id);

        $movimientos = Movimiento::where('material_id', $request->material_id)
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
            ->orderBy('fecha', 'asc')->orderBy('created_at', 'asc')->get();

        $movimientoAnterior = Movimiento::where('material_id', $request->material_id)
            ->where('fecha', '<', $request->fecha_inicio)
            ->orderBy('fecha', 'desc')->orderBy('created_at', 'desc')->first();

        $data = [
            'material' => $material,
            'movimientos' => $movimientos,
            'movimientoAnterior' => $movimientoAnterior,
            'fechaInicio' => $request->fecha_inicio,
            'fechaFin' => $request->fecha_fin,
        ];

        $formato = $request->formato ?? 'vista';
        Auditoria::registrar(
            'Reportes',
            'Generar',
            'Generó Kardex de "' . $material->nombre . '" - Período: ' . $request->fecha_inicio . ' al ' . $request->fecha_fin . ' - Formato: ' . strtoupper($formato)
        );

        if ($request->formato === 'pdf')
            return $this->generarKardexPDF($data);
        if ($request->formato === 'excel')
            return $this->generarKardexExcel($data);

        return view('reportes.kardex_material', $data);
    }

    public function movimientosPeriodo(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_movimiento' => 'nullable|in:entrada,salida',
        ]);

        $query = Movimiento::with(['material.subcategoria.categoria', 'material.unidadMedida'])
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->tipo_movimiento) {
            $query->where('tipo_movimiento', $request->tipo_movimiento);
        }

        $movimientos = $query->orderBy('fecha', 'desc')->get();

        $data = [
            'movimientos' => $movimientos,
            'fechaInicio' => $request->fecha_inicio,
            'fechaFin' => $request->fecha_fin,
            'tipoMovimiento' => $request->tipo_movimiento,
            'totalEntradas' => $movimientos->where('tipo_movimiento', 'entrada')->sum('total'),
            'totalSalidas' => $movimientos->where('tipo_movimiento', 'salida')->sum('total'),
        ];

        $tipo = $request->tipo_movimiento ?? 'todos';
        $formato = $request->formato ?? 'vista';
        Auditoria::registrar(
            'Reportes',
            'Generar',
            'Generó reporte de Movimientos - Período: ' . $request->fecha_inicio . ' al ' . $request->fecha_fin . ' - Tipo: ' . $tipo . ' - Formato: ' . strtoupper($formato)
        );

        if ($request->formato === 'pdf')
            return $this->generarMovimientosPDF($data);
        if ($request->formato === 'excel')
            return $this->generarMovimientosExcel($data);

        return view('reportes.movimientos_periodo', $data);
    }

    public function stockMinimo(Request $request)
    {
        $materialesBajoStock = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial'])
            ->whereHas('detalleMaterial', function ($query) {
                $query->whereRaw('cantidad_actual <= cantidad_minima');
            })
            ->orderBy('nombre')->get();

        $data = [
            'materiales' => $materialesBajoStock,
            'titulo' => 'REPORTE DE MATERIALES CON STOCK MÍNIMO',
        ];

        $formato = $request->formato ?? 'vista';
        Auditoria::registrar(
            'Reportes',
            'Generar',
            'Generó reporte de Stock Mínimo - ' . $materialesBajoStock->count() . ' materiales bajo mínimo - Formato: ' . strtoupper($formato)
        );

        if ($request->formato === 'pdf')
            return $this->generarStockMinimoPDF($data);
        if ($request->formato === 'excel')
            return $this->generarStockMinimoExcel($data);

        return view('reportes.stock_minimo', $data);
    }

    public function valorizacionInventario(Request $request)
    {
        $materiales = Material::with(['subcategoria.categoria', 'unidadMedida', 'detalleMaterial'])
            ->whereHas('detalleMaterial')
            ->orderBy('nombre')->get();

        $totalValorizado = $materiales->sum(function ($material) {
            return $material->detalleMaterial ? $material->detalleMaterial->costo_total : 0;
        });

        $data = [
            'materiales' => $materiales,
            'totalValorizado' => $totalValorizado,
            'fecha' => now(),
        ];

        $formato = $request->formato ?? 'vista';
        Auditoria::registrar(
            'Reportes',
            'Generar',
            'Generó reporte de Valorización de Inventario - Total: Bs. ' . number_format($totalValorizado, 2) . ' - Formato: ' . strtoupper($formato)
        );

        if ($request->formato === 'pdf')
            return $this->generarValorizacionPDF($data);
        if ($request->formato === 'excel')
            return $this->generarValorizacionExcel($data);

        return view('reportes.valorizacion', $data);
    }

    // ── MÉTODOS PRIVADOS PDF ──

    private function generarInventarioPDF($data)
    {
        $pdf = Pdf::loadView('reportes.pdf.inventario_general', $data)
            ->setPaper('legal', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->download('Inventario_General_' . date('Y-m-d') . '.pdf');
    }

    private function generarKardexPDF($data)
    {
        $pdf = Pdf::loadView('reportes.pdf.kardex_material', $data)
            ->setPaper('legal', 'landscape')
            ->setOption('margin-top', 10);

        return $pdf->download('Kardex_' . $data['material']->codigo . '_' . date('Y-m-d') . '.pdf');
    }

    private function generarMovimientosPDF($data)
    {
        $pdf = Pdf::loadView('reportes.pdf.movimientos_periodo', $data)
            ->setPaper('legal', 'landscape');

        return $pdf->download('Movimientos_' . date('Y-m-d') . '.pdf');
    }

    private function generarStockMinimoPDF($data)
    {
        $pdf = Pdf::loadView('reportes.pdf.stock_minimo', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->download('Stock_Minimo_' . date('Y-m-d') . '.pdf');
    }

    private function generarValorizacionPDF($data)
    {
        $pdf = Pdf::loadView('reportes.pdf.valorizacion', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->download('Valorizacion_Inventario_' . date('Y-m-d') . '.pdf');
    }

    // ── MÉTODOS PRIVADOS EXCEL ──

    private function generarInventarioExcel($data)
    {
        $filename = 'Inventario_General_' . date('Y-m-d') . '.xlsx';
        return (new \App\Exports\InventarioGeneralExport($data))->download($filename);
    }

    private function generarKardexExcel($data)
    {
        $filename = 'Kardex_' . $data['material']->codigo . '_' . date('Y-m-d') . '.xlsx';
        return (new \App\Exports\KardexExport(
            $data['material']->id,
            $data['fechaInicio'],
            $data['fechaFin']
        ))->download($filename);
    }

    private function generarMovimientosExcel($data)
    {
        $filename = 'Movimientos_' . date('Y-m-d') . '.xlsx';
        return (new \App\Exports\MovimientosExport($data))->download($filename);
    }

    private function generarStockMinimoExcel($data)
    {
        // Pendiente de implementar
        abort(501, 'Exportación Excel de Stock Mínimo no implementada.');
    }

    private function generarValorizacionExcel($data)
    {
        // Pendiente de implementar
        abort(501, 'Exportación Excel de Valorización no implementada.');
    }
}