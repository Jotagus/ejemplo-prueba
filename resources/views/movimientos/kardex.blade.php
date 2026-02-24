@extends('layouts.app')

@section('title', 'Tarjeta Kardex')
@section('page-title')<i class="bi bi-file-earmark-ruled text-warning me-2"></i>
    Tarjeta Kardex
@endsection


@section('styles')
<style>
    /* Configuración de página horizontal */
    @page {
        size: legal landscape; /* Tamaño legal horizontal */
        margin: 1cm;
    }
    
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .no-print { 
            display: none !important; 
        }
        .kardex-container { 
            box-shadow: none;
            padding: 0;
            margin: 0;
        }
        /* Forzar orientación horizontal */
        @page {
            size: legal landscape;
            margin: 0.5cm;
        }
    }
    
    .kardex-container {
        background: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 100%;
        overflow-x: auto;
    }
    
    .kardex-header {
        text-align: center;
        margin-bottom: 1rem;
        border: 2px solid #333;
        padding: 0.5rem;
        background: #f8f9fa;
    }
    
    .kardex-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: bold;
    }
    
    .kardex-header h6 {
        margin: 0.3rem 0 0 0;
        font-size: 0.95rem;
    }
    
    .kardex-info {
        background: #fff;
        padding: 0.5rem;
        border: 1px solid #333;
        margin-bottom: 1rem;
        font-size: 0.8rem;
    }
    
    .kardex-info .row > div {
        padding: 0.2rem 0.5rem;
    }
    
    .kardex-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.7rem;
        table-layout: fixed;
    }
    
    .kardex-table th {
        background: #4A90E2;
        color: white;
        padding: 0.3rem 0.2rem;
        text-align: center;
        border: 1px solid #333;
        font-weight: 600;
        font-size: 0.65rem;
        line-height: 1.2;
        vertical-align: middle;
    }
    
    .kardex-table td {
        padding: 0.25rem 0.2rem;
        border: 1px solid #666;
        text-align: center;
        font-size: 0.7rem;
        line-height: 1.3;
        vertical-align: middle;
        word-wrap: break-word;
    }
    
    .kardex-table .group-header {
        background: #2C5F8D;
        color: white;
        font-weight: 700;
        font-size: 0.7rem;
    }
    
    .row-entrada {
        background: rgba(16, 185, 129, 0.15);
    }
    
    .row-salida {
        background: rgba(239, 68, 68, 0.15);
    }
    
    .row-saldo-inicial {
        background: #fff3cd;
        font-weight: 700;
    }
    
    /* Anchos específicos para columnas */
    .col-fecha { width: 5%; }
    .col-doc { width: 6%; }
    .col-unidad { width: 4%; }
    .col-cantidad { width: 5%; }
    .col-costo { width: 5%; }
    .col-total { width: 6%; }
    .col-solicitante { width: 8%; }
    
    .text-end { text-align: right; }
    .text-start { text-align: left; }
    
    .firma-section {
        margin-top: 2rem;
        page-break-inside: avoid;
    }
    
    .firma-box {
        text-align: center;
        margin-top: 3rem;
    }
    
    .firma-line {
        border-top: 1px solid #333;
        width: 180px;
        margin: 0 auto;
        padding-top: 0.3rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="fw-bold mb-0" style="color: var(--text-dark);">
        </h4>
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Exportar
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('materiales.kardex.pdf', $material->id) }}">
                            <i class="bi bi-file-pdf text-danger"></i> Exportar a PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('materiales.kardex.excel', $material->id) }}">
                            <i class="bi bi-file-earmark-excel text-success"></i> Exportar a Excel
                        </a>
                    </li>
                </ul>
            </div>
            <button onclick="window.print()" class="btn btn-warning text-white">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="kardex-container">
        {{-- ENCABEZADO --}}
        <div class="kardex-header">
            <h5>TARJETA KARDEX - CONTROL DE INVENTARIO {{ date('Y') }}</h5>
            <h6 class="text-uppercase">{{ $material->nombre }}</h6>
        </div>

        {{-- INFORMACIÓN DEL MATERIAL --}}
        <div class="kardex-info">
            <div class="row g-1">
                <div class="col-3">
                    <strong>Código:</strong> {{ $material->codigo }}
                </div>
                <div class="col-3">
                    <strong>Categoría:</strong> {{ $material->subcategoria->categoria->nombre }}
                </div>
                <div class="col-3">
                    <strong>Subcategoría:</strong> {{ $material->subcategoria->nombre }}
                </div>
                <div class="col-3">
                    <strong>U. Medida:</strong> {{ $material->unidadMedida->nombre }} ({{ $material->unidadMedida->abreviatura }})
                </div>
            </div>
            @if($material->descripcion)
            <div class="row g-1 mt-1">
                <div class="col-12">
                    <strong>Descripción:</strong> {{ $material->descripcion }}
                </div>
            </div>
            @endif
        </div>

        {{-- TABLA KARDEX --}}
        <div class="table-responsive">
            <table class="kardex-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="col-fecha">FECHA</th>
                        <th rowspan="2" class="col-doc">N° DOC</th>
                        <th colspan="4" class="group-header">ENTRADA</th>
                        <th colspan="4" class="group-header">SALIDA</th>
                        <th colspan="3" class="group-header">SALDOS</th>
                    </tr>
                    <tr>
                        {{-- ENTRADA --}}
                        <th class="col-unidad">UND</th>
                        <th class="col-cantidad">CANT.</th>
                        <th class="col-costo">C.U.</th>
                        <th class="col-total">TOTAL</th>
                        
                        {{-- SALIDA --}}
                        <th class="col-unidad">UND</th>
                        <th class="col-cantidad">CANT.</th>
                        <th class="col-costo">C.U.</th>
                        <th class="col-total">TOTAL</th>
                        
                        {{-- SALDOS --}}
                        <th class="col-cantidad">CANT.</th>
                        <th class="col-costo">C.U.</th>
                        <th class="col-total">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- SALDO INICIAL --}}
                    @php
                        $saldoInicial = $material->detalleMaterial;
                        $primerMovimiento = $movimientos->first();
                        
                        if ($primerMovimiento) {
                            $cantidadInicial = $primerMovimiento->tipo_movimiento === 'entrada' 
                                ? $primerMovimiento->saldo_cantidad - $primerMovimiento->cantidad
                                : $primerMovimiento->saldo_cantidad + $primerMovimiento->cantidad;
                            $costoInicial = $primerMovimiento->tipo_movimiento === 'entrada'
                                ? $primerMovimiento->saldo_costo_total - $primerMovimiento->total
                                : $primerMovimiento->saldo_costo_total + $primerMovimiento->total;
                        } else {
                            $cantidadInicial = $saldoInicial->cantidad_actual ?? 0;
                            $costoInicial = $saldoInicial->costo_total ?? 0;
                        }
                        $precioUnitInicial = $cantidadInicial > 0 ? $costoInicial / $cantidadInicial : 0;
                    @endphp
                    <tr class="row-saldo-inicial">
                        <td>{{ now()->startOfYear()->format('d/m/Y') }}</td>
                        <td colspan="5" style="text-align: center; font-weight: bold;">SALDO INICIAL</td>
                        <td colspan="4"></td>
                        <td>{{ number_format($cantidadInicial, 2) }}</td>
                        <td>{{ number_format($precioUnitInicial, 2) }}</td>
                        <td>{{ number_format($costoInicial, 2) }}</td>
                    </tr>

                    {{-- MOVIMIENTOS --}}
                    @foreach($movimientos as $mov)
                    <tr class="{{ $mov->tipo_movimiento === 'entrada' ? 'row-entrada' : 'row-salida' }}">
                        <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                        <td style="font-size: 0.65rem;">
                            @if($mov->tipo_movimiento === 'entrada')
                                {{ $mov->numero_factura ? 'F:' . $mov->numero_factura : ($mov->numero_ingreso ? 'I:' . $mov->numero_ingreso : '-') }}
                            @else
                                {{ 'S:' . ($mov->numero_salida ?? '-') }}
                            @endif
                        </td>
                        
                        @if($mov->tipo_movimiento === 'entrada')
                            {{-- ENTRADA --}}
                            <td>{{ $material->unidadMedida->abreviatura }}</td>
                            <td><strong>{{ number_format($mov->cantidad, 2) }}</strong></td>
                            <td>{{ number_format($mov->costo_unitario, 2) }}</td>
                            <td><strong>{{ number_format($mov->total, 2) }}</strong></td>
                            {{-- SALIDA vacía --}}
                            <td colspan="4" style="background: #f5f5f5;"></td>
                        @else
                            {{-- ENTRADA vacía --}}
                            <td colspan="4" style="background: #f5f5f5;"></td>
                            {{-- SALIDA --}}
                            <td>{{ $material->unidadMedida->abreviatura }}</td>
                            <td><strong>{{ number_format($mov->cantidad, 2) }}</strong></td>
                            <td>{{ number_format($mov->costo_unitario, 2) }}</td>
                            <td><strong>{{ number_format($mov->total, 2) }}</strong></td>
                        @endif
                        
                        {{-- SALDOS --}}
                        <td><strong>{{ number_format($mov->saldo_cantidad, 2) }}</strong></td>
                        <td>{{ number_format($mov->saldo_cantidad > 0 ? $mov->saldo_costo_total / $mov->saldo_cantidad : 0, 2) }}</td>
                        <td><strong>{{ number_format($mov->saldo_costo_total, 2) }}</strong></td>
                    </tr>
                    
                    @if($mov->unidad_solicitante && $mov->tipo_movimiento === 'salida')
                    <tr class="row-salida">
                        <td colspan="13" class="text-start" style="padding-left: 1rem; font-size: 0.65rem; font-style: italic;">
                            <strong>Solicitante:</strong> {{ $mov->unidad_solicitante }}
                            @if($mov->observaciones)
                                | <strong>Obs:</strong> {{ Str::limit($mov->observaciones, 50) }}
                            @endif
                        </td>
                    </tr>
                    @endif
                    @endforeach

                    @if($movimientos->isEmpty())
                    <tr>
                        <td colspan="13" class="text-center py-3 text-muted">
                            No hay movimientos registrados para este material
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection