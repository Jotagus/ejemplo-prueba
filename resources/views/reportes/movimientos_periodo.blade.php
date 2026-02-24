@extends('layouts.app')

@section('title', 'Movimientos por Período')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-arrow-left-right text-warning me-2"></i>Movimientos por Período
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

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Reporte de Movimientos</h5>
            <small>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</small>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <strong>Total Entradas:</strong> ${{ number_format($totalEntradas, 2) }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-danger">
                        <strong>Total Salidas:</strong> ${{ number_format($totalSalidas, 2) }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-info">
                        <strong>Movimientos:</strong> {{ $movimientos->count() }}
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Material</th>
                            <th>Cantidad</th>
                            <th>C. Unitario</th>
                            <th>Total</th>
                            <th>N° Doc</th>
                            <th>Solicitante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $mov)
                        <tr>
                            <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $mov->tipo_movimiento === 'entrada' ? 'bg-success' : 'bg-danger' }}">
                                    {{ strtoupper($mov->tipo_movimiento) }}
                                </span>
                            </td>
                            <td>{{ $mov->material->codigo }} - {{ $mov->material->nombre }}</td>
                            <td class="text-end">{{ number_format($mov->cantidad, 2) }} {{ $mov->material->unidadMedida->abreviatura }}</td>
                            <td class="text-end">${{ number_format($mov->costo_unitario, 2) }}</td>
                            <td class="text-end"><strong>${{ number_format($mov->total, 2) }}</strong></td>
                            <td>{{ $mov->numero_factura ?? $mov->numero_ingreso ?? $mov->numero_salida ?? '-' }}</td>
                            <td>{{ $mov->unidad_solicitante ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection