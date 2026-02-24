<?php

namespace App\Exports;

use App\Models\Movimiento;
use App\Models\Material;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class KardexExport
{
    protected $materialId;
    protected $fechaInicio;
    protected $fechaFin;
    protected $material;
    protected $movimientos;
    protected $cantidadInicial;
    protected $costoInicial;

    public function __construct($materialId, $fechaInicio = null, $fechaFin = null)
    {
        $this->materialId  = $materialId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin    = $fechaFin;

        $this->material = Material::with([
            'subcategoria.categoria',
            'unidadMedida',
            'detalleMaterial'
        ])->findOrFail($materialId);

        $query = Movimiento::where('material_id', $materialId)
            ->orderBy('fecha', 'asc')
            ->orderBy('created_at', 'asc');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        $this->movimientos = $query->get();

        // Calcular saldo inicial (igual que en el blade)
        $primerMovimiento = $this->movimientos->first();
        if ($primerMovimiento) {
            $this->cantidadInicial = $primerMovimiento->tipo_movimiento === 'entrada'
                ? $primerMovimiento->saldo_cantidad - $primerMovimiento->cantidad
                : $primerMovimiento->saldo_cantidad + $primerMovimiento->cantidad;
            $this->costoInicial = $primerMovimiento->tipo_movimiento === 'entrada'
                ? $primerMovimiento->saldo_costo_total - $primerMovimiento->total
                : $primerMovimiento->saldo_costo_total + $primerMovimiento->total;
        } else {
            $this->cantidadInicial = $this->material->detalleMaterial->cantidad_actual ?? 0;
            $this->costoInicial    = $this->material->detalleMaterial->costo_total ?? 0;
        }
    }

    public function download(string $filename)
    {
        $rows = $this->buildRows();
        return (new FastExcel($rows))->download($filename);
    }

    protected function buildRows(): \Illuminate\Support\Collection
    {
        $rows = collect();
        $und  = $this->material->unidadMedida->abreviatura;

        $precioUnitInicial = $this->cantidadInicial > 0
            ? $this->costoInicial / $this->cantidadInicial
            : 0;

        // Fila SALDO INICIAL
        $rows->push([
            'FECHA'      => now()->startOfYear()->format('d/m/Y'),
            'N° DOC'     => 'SALDO INICIAL',
            'ENT. UND'   => '',
            'ENT. CANT'  => '',
            'ENT. C.U.'  => '',
            'ENT. TOTAL' => '',
            'SAL. UND'   => '',
            'SAL. CANT'  => '',
            'SAL. C.U.'  => '',
            'SAL. TOTAL' => '',
            'SALDO CANT' => number_format($this->cantidadInicial, 2),
            'SALDO C.U.' => number_format($precioUnitInicial, 2),
            'SALDO TOTAL'=> number_format($this->costoInicial, 2),
        ]);

        foreach ($this->movimientos as $mov) {
            // Número de documento
            if ($mov->tipo_movimiento === 'entrada') {
                $ndoc = $mov->numero_factura
                    ? 'F:' . $mov->numero_factura
                    : ($mov->numero_ingreso ? 'I:' . $mov->numero_ingreso : '-');
            } else {
                $ndoc = 'S:' . ($mov->numero_salida ?? '-');
            }

            $saldoCU = $mov->saldo_cantidad > 0
                ? $mov->saldo_costo_total / $mov->saldo_cantidad
                : 0;

            if ($mov->tipo_movimiento === 'entrada') {
                $rows->push([
                    'FECHA'      => $mov->fecha->format('d/m/Y'),
                    'N° DOC'     => $ndoc,
                    'ENT. UND'   => $und,
                    'ENT. CANT'  => number_format($mov->cantidad, 2),
                    'ENT. C.U.'  => number_format($mov->costo_unitario, 2),
                    'ENT. TOTAL' => number_format($mov->total, 2),
                    'SAL. UND'   => '',
                    'SAL. CANT'  => '',
                    'SAL. C.U.'  => '',
                    'SAL. TOTAL' => '',
                    'SALDO CANT' => number_format($mov->saldo_cantidad, 2),
                    'SALDO C.U.' => number_format($saldoCU, 2),
                    'SALDO TOTAL'=> number_format($mov->saldo_costo_total, 2),
                ]);
            } else {
                $rows->push([
                    'FECHA'      => $mov->fecha->format('d/m/Y'),
                    'N° DOC'     => $ndoc,
                    'ENT. UND'   => '',
                    'ENT. CANT'  => '',
                    'ENT. C.U.'  => '',
                    'ENT. TOTAL' => '',
                    'SAL. UND'   => $und,
                    'SAL. CANT'  => number_format($mov->cantidad, 2),
                    'SAL. C.U.'  => number_format($mov->costo_unitario, 2),
                    'SAL. TOTAL' => number_format($mov->total, 2),
                    'SALDO CANT' => number_format($mov->saldo_cantidad, 2),
                    'SALDO C.U.' => number_format($saldoCU, 2),
                    'SALDO TOTAL'=> number_format($mov->saldo_costo_total, 2),
                ]);

                // Fila solicitante
                if ($mov->unidad_solicitante) {
                    $obs = 'Solicitante: ' . $mov->unidad_solicitante;
                    if ($mov->observaciones) {
                        $obs .= ' | Obs: ' . Str::limit($mov->observaciones, 60);
                    }
                    $rows->push([
                        'FECHA'      => '',
                        'N° DOC'     => $obs,
                        'ENT. UND'   => '', 'ENT. CANT'  => '',
                        'ENT. C.U.'  => '', 'ENT. TOTAL' => '',
                        'SAL. UND'   => '', 'SAL. CANT'  => '',
                        'SAL. C.U.'  => '', 'SAL. TOTAL' => '',
                        'SALDO CANT' => '', 'SALDO C.U.' => '',
                        'SALDO TOTAL'=> '',
                    ]);
                }
            }
        }

        return $rows;
    }
}