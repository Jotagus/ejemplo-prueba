@extends('layouts.app')

@section('title', 'Reportes de Inventario')
@section('page-title')<i class="bi bi-bar-chart-line text-warning me-2"></i>
    Centro de Reportes
@endsection

@section('styles')
    <style>
        .report-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .report-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .report-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .report-description {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .filter-section {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }

        .btn-generate {
            min-width: 150px;
        }

        .icon-pdf {
            color: #dc3545;
        }

        .icon-excel {
            color: #198754;
        }

        .icon-view {
            color: #0dcaf0;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);">

            </h4>
        </div>

        {{-- GRID DE REPORTES DISPONIBLES --}}
        <div class="row">
            {{-- REPORTE 1: INVENTARIO GENERAL --}}
            <div class="col-md-6 col-lg-4">
                <div class="report-card">
                    <div class="text-center">
                        <div class="report-icon">📊</div>
                        <h5 class="report-title">Inventario General</h5>
                        <p class="report-description">
                            Reporte completo de todos los materiales con saldos iniciales y finales por período
                        </p>
                        <button class="btn btn-warning text-white btn-sm" data-bs-toggle="modal"
                            data-bs-target="#inventarioModal">
                            <i class="bi bi-gear-fill"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </div>

            {{-- REPORTE 2: KARDEX POR MATERIAL --}}
            <div class="col-md-6 col-lg-4">
                <div class="report-card">
                    <div class="text-center">
                        <div class="report-icon">📋</div>
                        <h5 class="report-title">Kardex por Material</h5>
                        <p class="report-description">
                            Tarjeta kardex de un material específico en un rango de fechas
                        </p>
                        <button class="btn btn-warning text-white btn-sm" data-bs-toggle="modal"
                            data-bs-target="#kardexModal">
                            <i class="bi bi-gear-fill"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </div>

            {{-- REPORTE 3: MOVIMIENTOS --}}
            <div class="col-md-6 col-lg-4">
                <div class="report-card">
                    <div class="text-center">
                        <div class="report-icon">🔄</div>
                        <h5 class="report-title">Movimientos por Período</h5>
                        <p class="report-description">
                            Listado de todas las entradas y salidas en un rango de fechas
                        </p>
                        <button class="btn btn-warning text-white btn-sm" data-bs-toggle="modal"
                            data-bs-target="#movimientosModal">
                            <i class="bi bi-gear-fill"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL: INVENTARIO GENERAL --}}
    <div class="modal fade" id="inventarioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">📊 Inventario General</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('reportes.inventario-general') }}" method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoría (Opcional)</label>
                                <select name="categoria_id" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subcategoría (Opcional)</label>
                                <select name="subcategoria_id" class="form-select">
                                    <option value="">Todas las subcategorías</option>
                                    @foreach($subcategorias as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="formato" value="ver" class="btn btn-info text-white">
                            <i class="bi bi-eye-fill"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="bi bi-file-pdf-fill"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="bi bi-file-excel-fill"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: KARDEX POR MATERIAL --}}
    <div class="modal fade" id="kardexModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">📋 Kardex por Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('reportes.kardex-material') }}" method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Material <span class="text-danger">*</span></label>
                                <select name="material_id" class="form-select" required>
                                    <option value="">Selecciona un material</option>
                                    @foreach($materiales as $mat)
                                        <option value="{{ $mat->id }}">{{ $mat->codigo }} - {{ $mat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="formato" value="ver" class="btn btn-info text-white">
                            <i class="bi bi-eye-fill"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="bi bi-file-pdf-fill"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="bi bi-file-excel-fill"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: MOVIMIENTOS POR PERÍODO --}}
    <div class="modal fade" id="movimientosModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">🔄 Movimientos por Período</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('reportes.movimientos-periodo') }}" method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tipo de Movimiento</label>
                                <select name="tipo_movimiento" class="form-select">
                                    <option value="">Todos (Entradas y Salidas)</option>
                                    <option value="entrada">Solo Entradas</option>
                                    <option value="salida">Solo Salidas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="formato" value="ver" class="btn btn-info text-white">
                            <i class="bi bi-eye-fill"></i> Ver
                        </button>
                        <button type="submit" name="formato" value="pdf" class="btn btn-danger">
                            <i class="bi bi-file-pdf-fill"></i> PDF
                        </button>
                        <button type="submit" name="formato" value="excel" class="btn btn-success">
                            <i class="bi bi-file-excel-fill"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection