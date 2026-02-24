<?php

namespace App\Exports;

use Rap2hpoutre\FastExcel\FastExcel;

class InventarioGeneralExport
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

        foreach ($this->data['materiales'] as $material) {
            $detalle = $material->detalleMaterial;
            $rows->push([
                'CÓDIGO'          => $material->codigo,
                'MATERIAL'        => strtoupper($material->nombre),
                'CATEGORÍA'       => $material->subcategoria->categoria->nombre,
                'UND'             => $material->unidadMedida->abreviatura,
                'STOCK ACTUAL'    => number_format($detalle->cantidad_actual ?? 0, 2),
                'PRECIO UNITARIO' => number_format($detalle->precio_unitario ?? 0, 2),
                'VALOR TOTAL'     => number_format($detalle->costo_total ?? 0, 2),
            ]);
        }

        return $rows;
    }
}