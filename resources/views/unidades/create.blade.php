@extends('layouts.app')

@section('title', 'Nueva Unidad de Medida')

@section('styles')
    <style>
        .card-emdell {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }

        .form-control,
        .form-select {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);">
                <i class="bi bi-rulers text-warning me-2"></i>Nueva Unidad de Medida
            </h4>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        {{-- ========================================
            FORMULARIO
        ======================================== --}}
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-emdell shadow-sm p-4">
                    <form action="{{ route('unidades.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                placeholder="Ej. Kilogramo, Metro, Litro, Unidad" 
                                value="{{ old('nombre') }}" required autofocus>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Abreviatura <span class="text-danger">*</span></label>
                            <input type="text" name="abreviatura" class="form-control @error('abreviatura') is-invalid @enderror" 
                                placeholder="Ej. kg, m, L, und" 
                                value="{{ old('abreviatura') }}" required maxlength="10">
                            <small class="text-muted">Máximo 10 caracteres</small>
                            @error('abreviatura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary flex-fill">
                                <i class="bi bi-x-lg"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning text-white fw-bold flex-fill">
                                <i class="bi bi-save"></i> Guardar Unidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection