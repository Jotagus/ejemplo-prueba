@extends('layouts.app')

@section('title', 'Kardex Material')

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

        {{-- Usar la misma vista del kardex normal --}}
        @include('movimientos.kardex', [
            'material' => $material,
            'movimientos' => $movimientos
        ])
        </div>
@endsection