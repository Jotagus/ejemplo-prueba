@extends('layouts.app')

@section('title', 'Inventario General')
@section('page-title')<i class="bi bi-file-earmark-bar-graph text-warning me-2"></i>
    Inventario General
@endsection

@section('styles')
<style>
    .report-container {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .report-header {
        text-align: center;
        margin-bottom: 2rem;
        border-bottom: 3px solid #333;
        padding-bottom: 1rem;
    }
    
    .company-name {
        font-size: 1.2rem;
        font-weight: bold;
        color: #2C3E50;
    }
    
    .report-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #E74C3C;
        margin: 0.5rem 0;
    }
    
    .info-box {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }
    
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    
    .inventory-table th {
        background: #4A90E2;
        color: white;
        padding: 0.75rem 0.5rem;
        text-align: center;
        border: 1px solid #333;
        font-weight: 600;
    }
    
    .inventory-table td {
        padding: 0.5rem;
        border: 1px solid #ddd;
        text-align: center;
    }
    
    .category-header {
        background: #E8F4F8;
        font-weight: bold;
        text-align: left !important;
        padding: 0.75rem !important;
    }
    
    .total-row {
        background: #fffacd;
        font-weight: bold;
        font-size: 1rem;
    }
    
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    
    @media print {
        .no-print { display: none !important; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h4 class="fw-bold mb-0">
        </h4>
        <div>
            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>

            <button onclick="window.print()" class="btn btn-warning text-white">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="report-container">
        {{-- ENCABEZADO --}}
        <div class="report-header">
            <div class="company-name">EMPRESA MUNICIPAL DE DISTRIBUCIÓN DE ENERGÍA ELÉCTRICA</div>
            <div class="company-name">"E.M.D.E.LL."</div>
            <div class="report-title">{{ $titulo }}</div>
            <div style="margin-top: 0.5rem; font-size: 1rem; color: #555;">
                AL {{ \Carbon\Carbon::parse($fechaFin)->format('d \d\e F \d\e Y') }}
            </div>
            <div style="font-size: 0.9rem; color: #666;">(EXPRESADO EN BOLIVIANOS)</div>
        </div>

        {{-- INFORMACIÓN --}}
        <div class="info-box">
            <strong>PERÍODO:</strong> Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>TOTAL MATERIALES:</strong> {{ $materiales->count() }}
        </div>

        {{-- TABLA --}}
        <div class="table-responsive">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 4%;">ÍTEM</th>
                        <th rowspan="2" style="width: 8%;">CÓDIGO</th>
                        <th rowspan="2" style="width: 30%;">DETALLE</th>
                        <th rowspan="2" style="width: 6%;">UNIDAD</th>
                        <th colspan="2">SALDO INICIAL<br><small>{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</small></th>
                        <th rowspan="2" style="width: 8%;">P/U</th>
                        <th colspan="2">SALDO FINAL<br><small>{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</small></th>
                    </tr>
                    <tr>
                        <th style="width: 10%;">CANTIDAD</th>
                        <th style="width: 10%;">SALDO Bs.</th>
                        <th style="width: 10%;">CANTIDAD</th>
                        <th style="width: 10%;">SALDO Bs.</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentCategoria = null;
                        $itemNumber = 1;
                    @endphp

                    @foreach($materiales as $material)
                        {{-- Separador por categoría --}}
                        @if($currentCategoria !== $material['categoria'])
                            @php $currentCategoria = $material['categoria']; @endphp
                            <tr>
                                <td colspan="9" class="category-header">
                                    {{ strtoupper($material['categoria']) }}
                                    @if(isset($material['subcategoria']))
                                        / {{ strtoupper($material['subcategoria']) }}
                                    @endif
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <td>{{ $itemNumber++ }}</td>
                            <td><strong>{{ $material['codigo'] }}</strong></td>
                            <td class="text-left" style="padding-left: 1rem;">{{ strtoupper($material['nombre']) }}</td>
                            <td>{{ $material['unidad'] }}</td>
                            <td class="text-right">{{ number_format($material['cantidad_inicial'], 2) }}</td>
                            <td class="text-right">{{ number_format($material['saldo_inicial'], 2) }}</td>
                            <td class="text-right">{{ number_format($material['precio_unitario'], 2) }}</td>
                            <td class="text-right">{{ number_format($material['cantidad_final'], 2) }}</td>
                            <td class="text-right"><strong>{{ number_format($material['saldo_final'], 2) }}</strong></td>
                        </tr>
                    @endforeach

                    {{-- TOTALES --}}
                    <tr class="total-row">
                        <td colspan="5" class="text-right" style="padding-right: 1rem;">TOTAL VALORIZADO:</td>
                        <td class="text-right"><strong>{{ number_format($totalInicial, 2) }}</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong>{{ number_format($totalFinal, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- PIE --}}
        <div class="mt-4 text-center text-muted">
            <small>Generado el {{ now()->format('d/m/Y H:i') }}</small>
        </div>
    </div>
</div>
@endsection