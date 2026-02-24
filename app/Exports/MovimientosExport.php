<?php

namespace App\Exports;

use Rap2hpoutre\FastExcel\FastExcel;

class MovimientosExport
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function download(string $filename)
    {
        $rows = $this->buildRows();
        return (new FastExcel($rows))->download($filename);
    }

    protected function buildRows(): \Illuminate\Support\Collection
    {
        $rows = collect();

        foreach ($this->data['movimientos'] as $mov) {
            $rows->push([
                'FECHA'       => $mov->fecha->format('d/m/Y'),
                'TIPO'        => strtoupper($mov->tipo_movimiento),
                'CÓDIGO'      => $mov->material->codigo,
                'MATERIAL'    => strtoupper($mov->material->nombre),
                'UND'         => $mov->material->unidadMedida->abreviatura,
                'CANTIDAD'    => number_format($mov->cantidad, 2),
                'C. UNITARIO' => number_format($mov->costo_unitario, 2),
                'TOTAL'       => number_format($mov->total, 2),
                'N° DOC'      => $mov->numero_factura ?? $mov->numero_ingreso ?? $mov->numero_salida ?? '-',
                'SOLICITANTE' => $mov->unidad_solicitante ?? '-',
            ]);
        }

        return $rows;
    }
}