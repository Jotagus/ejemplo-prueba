@extends('layouts.app')

@section('title', 'Movimientos de Inventario')
@section('page-title')<i class="bi bi-arrow-left-right text-warning me-2"></i>
    Gestión de Movimientos
@endsection

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .card-emdell {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }

        .table {
            color: var(--text-dark);
        }

        .table thead {
            background-color: rgba(255, 107, 53, 0.05);
        }

        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .modal-header,
        .modal-footer {
            border-color: var(--border-color);
        }

        .form-control,
        .form-select {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }

        .swal2-popup {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 15px !important;
        }

        .badge-entrada {
            background-color: #10B981;
        }

        .badge-salida {
            background-color: #EF4444;
        }

        .filter-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        /* ── PAGINACIÓN ── */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 1.25rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pagination-info {
            font-size: 0.8rem;
            color: var(--text-dark);
            opacity: 0.55;
        }

        .pagination-controls {
            display: flex;
            gap: 0.25rem;
            align-items: center;
        }

        .page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 0.45rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            color: var(--text-dark);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.18s ease;
            opacity: 0.75;
        }

        .page-btn:hover:not(:disabled) {
            border-color: #FFC107;
            color: #FFC107;
            opacity: 1;
        }

        .page-btn.active {
            background: #FFC107;
            border-color: #FFC107;
            color: #000;
            opacity: 1;
            font-weight: 700;
        }

        .page-btn:disabled {
            opacity: 0.25;
            cursor: not-allowed;
        }

        .rows-selector {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: var(--text-dark);
            opacity: 0.6;
        }

        .rows-selector select {
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--card-bg);
            color: var(--text-dark);
            font-size: 0.8rem;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        {{-- ── ENCABEZADO ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);">
            </h4>
            <div class="d-flex gap-2">
                @canEdit
                    <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#entradaModal">
                        <i class="bi bi-box-arrow-in-down"></i> Entrada
                    </button>
                    <button class="btn btn-danger fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#salidaModal">
                        <i class="bi bi-box-arrow-up"></i> Salida
                    </button>
                @endcanEdit
            </div>
        </div>

        {{-- ── FILTROS ── --}}
        <div class="filter-card shadow-sm">
            <form method="GET" action="{{ route('movimientos.index') }}" id="filtrosForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Material</label>
                    <select name="material_id" class="form-select">
                        <option value="">Todos los materiales</option>
                        @foreach($materiales as $mat)
                            <option value="{{ $mat->id }}" {{ request('material_id') == $mat->id ? 'selected' : '' }}>
                                {{ $mat->codigo }} - {{ $mat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tipo</label>
                    <select name="tipo_movimiento" class="form-select">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entradas
                        </option>
                        <option value="salida" {{ request('tipo_movimiento') == 'salida' ? 'selected' : '' }}>Salidas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
                {{-- CAMPO OCULTO: mantiene per_page al filtrar --}}
                <input type="hidden" name="per_page" id="perPageHidden" value="{{ request('per_page', 10) }}">
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-warning text-white">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        {{-- ── TABLA ── --}}
        <div class="card-emdell shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-4">Fecha</th>
                            <th>Tipo</th>
                            <th>Material</th>
                            <th>N° Doc</th>
                            <th>Cantidad</th>
                            <th>C. Unitario</th>
                            <th>Total</th>
                            <th>Saldo</th>
                            <th>Solicitante</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="movTableBody">
                        @forelse($movimientos as $mov)
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4 small">{{ $mov->fecha->format('d/m/Y') }}</td>
                                <td>
                                    <span
                                        class="badge {{ $mov->tipo_movimiento === 'entrada' ? 'badge-entrada' : 'badge-salida' }} text-white">
                                        <i
                                            class="bi bi-{{ $mov->tipo_movimiento === 'entrada' ? 'arrow-down-circle' : 'arrow-up-circle' }}"></i>
                                        {{ $mov->tipo_movimiento_texto }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold">{{ $mov->material->nombre }}</span>
                                        <br><small class="text-muted">{{ $mov->material->codigo }}</small>
                                    </div>
                                </td>
                                <td class="small">
                                    @if($mov->tipo_movimiento === 'entrada')
                                        @if($mov->numero_factura)
                                            <span class="badge bg-info">F: {{ $mov->numero_factura }}</span>
                                        @endif
                                        @if($mov->numero_ingreso)
                                            <span class="badge bg-secondary">I: {{ $mov->numero_ingreso }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">S: {{ $mov->numero_salida }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($mov->cantidad, 2) }}</span>
                                    <small class="text-muted">{{ $mov->material->unidadMedida->abreviatura }}</small>
                                </td>
                                <td>${{ number_format($mov->costo_unitario, 2) }}</td>
                                <td class="fw-bold">${{ number_format($mov->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ number_format($mov->saldo_cantidad, 2) }}
                                        {{ $mov->material->unidadMedida->abreviatura }}
                                    </span>
                                </td>
                                <td class="small">{{ $mov->unidad_solicitante ?? '-' }}</td>
                                <td class="text-center">
                                    {{-- Ver detalles: visible para todos los roles con acceso al módulo --}}
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                        data-bs-target="#showModal{{ $mov->id }}" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @canEdit
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $mov->id }}" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $mov->id }}, '{{ $mov->material->nombre }}')"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $mov->id }}" action="{{ route('movimientos.destroy', $mov) }}"
                                            method="POST" style="display: none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endcanEdit
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRow">
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay movimientos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINACIÓN ── --}}
            <div class="pagination-wrapper">
                <div class="d-flex align-items-center gap-3">
                    <div class="rows-selector">
                        Mostrar
                        <select id="rowsPerPage">
                            <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        filas
                    </div>
                    <div class="pagination-info" id="paginationInfo">
                        Mostrando {{ $movimientos->firstItem() ?? 0 }}–{{ $movimientos->lastItem() ?? 0 }}
                        de {{ $movimientos->total() }} registros
                    </div>
                </div>
                {{-- Controles de página del servidor --}}
                <div class="pagination-controls">
                    {{-- Anterior --}}
                    @if($movimientos->onFirstPage())
                        <button class="page-btn" disabled>‹</button>
                    @else
                        <a href="{{ $movimientos->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                            class="page-btn">‹</a>
                    @endif

                    {{-- Números de página --}}
                    @php
                        $currentPage = $movimientos->currentPage();
                        $lastPage = $movimientos->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $start + 4);
                        if ($end - $start < 4)
                            $start = max(1, $end - 4);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $movimientos->url(1) }}&per_page={{ request('per_page', 10) }}" class="page-btn">1</a>
                        @if($start > 2)
                            <span style="padding:0 4px;opacity:0.4;font-size:0.85rem;line-height:32px;">…</span>
                        @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $movimientos->url($p) }}&per_page={{ request('per_page', 10) }}"
                            class="page-btn {{ $p === $currentPage ? 'active' : '' }}">{{ $p }}</a>
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span style="padding:0 4px;opacity:0.4;font-size:0.85rem;line-height:32px;">…</span>
                        @endif
                        <a href="{{ $movimientos->url($lastPage) }}&per_page={{ request('per_page', 10) }}"
                            class="page-btn">{{ $lastPage }}</a>
                    @endif

                    {{-- Siguiente --}}
                    @if($movimientos->hasMorePages())
                        <a href="{{ $movimientos->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                            class="page-btn">›</a>
                    @else
                        <button class="page-btn" disabled>›</button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ════════════════════════════════
        MODAL: REGISTRAR ENTRADA — solo admin y almacenero
        ════════════════════════════════ --}}
        @canEdit
            <div class="modal fade" id="entradaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-box-arrow-in-down text-success fs-4"></i>
                                </div>
                                <span class="text-body">Registrar Entrada de Material</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('movimientos.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo_movimiento" value="entrada">
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold text-muted">Material <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary border-end-0">
                                                <i class="bi bi-search text-muted"></i>
                                            </span>
                                            <select name="material_id" class="form-select border-start-0" required>
                                                <option value="" disabled selected>Seleccione material...</option>
                                                @foreach($materiales as $mat)
                                                    <option value="{{ $mat->id }}">{{ $mat->codigo }} - {{ $mat->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Fecha <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">N° Factura</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary"><i
                                                    class="bi bi-receipt text-muted"></i></span>
                                            <input type="text" name="numero_factura" class="form-control"
                                                placeholder="Ej. 001-001-000123">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">N° Ingreso</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary"><i
                                                    class="bi bi-hash text-muted"></i></span>
                                            <input type="text" name="numero_ingreso" class="form-control"
                                                placeholder="Ej. ING-2024-001">
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4 bg-body-tertiary border border-dashed mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Cantidad <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="cantidad" id="cantidad_entrada"
                                                class="form-control border-success-subtle" placeholder="0.00" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Costo Unitario <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-success bg-opacity-10 text-success border-success-subtle">$</span>
                                                <input type="number" step="0.01" name="costo_unitario"
                                                    id="costo_unitario_entrada" class="form-control border-success-subtle"
                                                    placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted text-primary">Total
                                                Estimado</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-primary bg-opacity-10 text-primary border-primary-subtle">$</span>
                                                <input type="text" id="total_entrada"
                                                    class="form-control border-primary-subtle bg-transparent fw-bold text-primary"
                                                    placeholder="0.00" readonly tabindex="-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold text-muted">Observaciones</label>
                                    <textarea name="observaciones" class="form-control" rows="2"
                                        placeholder="Detalles adicionales sobre esta entrada..."></textarea>
                                </div>
                            </div>

                            <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success px-4 shadow-sm fw-bold text-white">
                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i>Registrar Entrada
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcanEdit

        {{-- ════════════════════════════════
        MODAL: REGISTRAR SALIDA — solo admin y almacenero
        ════════════════════════════════ --}}
        @canEdit
            <div class="modal fade" id="salidaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-box-arrow-up text-danger fs-4"></i>
                                </div>
                                <span class="text-body">Registrar Salida de Material</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('movimientos.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo_movimiento" value="salida">
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold text-muted">Material <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary border-end-0">
                                                <i class="bi bi-search text-muted"></i>
                                            </span>
                                            <select name="material_id" class="form-select border-start-0" required>
                                                <option value="" disabled selected>Seleccione material a egresar...</option>
                                                @foreach($materiales as $mat)
                                                    <option value="{{ $mat->id }}">{{ $mat->codigo }} - {{ $mat->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Fecha de Salida <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">N° Salida <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary"><i
                                                    class="bi bi-file-earmark-text text-muted"></i></span>
                                            <input type="text" name="numero_salida" class="form-control"
                                                placeholder="Ej. SAL-2024-001" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">Unidad Solicitante <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary"><i
                                                    class="bi bi-person-badge text-muted"></i></span>
                                            <input type="text" name="unidad_solicitante" class="form-control"
                                                placeholder="Ej. Departamento de Mantenimiento" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-4 bg-body-tertiary border border-dashed mb-4">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Cantidad <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="cantidad" id="cantidad_salida"
                                                class="form-control border-danger-subtle" placeholder="0.00" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Costo Unitario <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-danger bg-opacity-10 text-danger border-danger-subtle">$</span>
                                                <input type="number" step="0.01" name="costo_unitario"
                                                    id="costo_unitario_salida" class="form-control border-danger-subtle"
                                                    placeholder="0.00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted text-primary">Total
                                                Valorizado</label>
                                            <div class="input-group">
                                                <span
                                                    class="input-group-text bg-primary bg-opacity-10 text-primary border-primary-subtle">$</span>
                                                <input type="text" id="total_salida"
                                                    class="form-control border-primary-subtle bg-transparent fw-bold text-primary"
                                                    placeholder="0.00" readonly tabindex="-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold text-muted">Observaciones de Salida</label>
                                    <textarea name="observaciones" class="form-control" rows="2"
                                        placeholder="Indique el motivo de la salida o destino final..."></textarea>
                                </div>
                            </div>

                            <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger px-4 shadow-sm fw-bold">
                                    <i class="bi bi-cloud-arrow-down-fill me-2"></i>Registrar Salida
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcanEdit

        {{-- ════════════════════════════════
        MODALES: VER DETALLES — visible para todos los roles con acceso
        ════════════════════════════════ --}}
        @foreach($movimientos as $mov)
            <div class="modal fade" id="showModal{{ $mov->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg border-0">

                        <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <div
                                    class="bg-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }} bg-opacity-10 p-2 rounded-3 me-3">
                                    <i
                                        class="bi bi-{{ $mov->tipo_movimiento === 'entrada' ? 'box-arrow-in-down' : 'box-arrow-up' }}-fill text-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }} fs-4"></i>
                                </div>
                                <div>
                                    <span class="text-uppercase small text-muted d-block"
                                        style="font-size: 0.7rem; letter-spacing: 1px;">
                                        Detalles del Movimiento
                                    </span>
                                    <span class="text-body">{{ $mov->tipo_movimiento_texto }}</span>
                                </div>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label text-muted fw-semibold small mb-1">Tipo de
                                                Movimiento</label>
                                            <div>
                                                @if($mov->tipo_movimiento === 'entrada')
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                                        <i
                                                            class="bi bi-arrow-down-circle-fill me-1"></i>{{ $mov->tipo_movimiento_texto }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger px-3 py-2">
                                                        <i
                                                            class="bi bi-arrow-up-circle-fill me-1"></i>{{ $mov->tipo_movimiento_texto }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label text-muted fw-semibold small mb-1">Fecha de
                                                Registro</label>
                                            <div class="p-2 bg-body-secondary rounded">
                                                <i class="bi bi-calendar-event me-1 text-primary"></i>
                                                <span class="fw-semibold">{{ $mov->fecha->format('d/m/Y') }}</span>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label text-muted fw-semibold small mb-1">Material
                                                Asociado</label>
                                            <div class="p-3 bg-body-secondary rounded border-start border-warning border-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-box-seam-fill text-warning fs-5 me-3"></i>
                                                    <div>
                                                        <div class="fw-bold text-body">{{ $mov->material->nombre }}</div>
                                                        <small class="text-muted">Código: <code
                                                                class="text-info">{{ $mov->material->codigo }}</code></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($mov->tipo_movimiento === 'entrada')
                                            <div class="col-12">
                                                <div class="row g-2">
                                                    @if($mov->numero_factura)
                                                        <div class="col-6">
                                                            <label class="form-label text-muted fw-semibold small mb-1">N°
                                                                Factura</label>
                                                            <div class="p-2 bg-body-secondary rounded">
                                                                <i class="bi bi-receipt me-1"></i>{{ $mov->numero_factura }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($mov->numero_ingreso)
                                                        <div class="col-6">
                                                            <label class="form-label text-muted fw-semibold small mb-1">N°
                                                                Ingreso</label>
                                                            <div class="p-2 bg-body-secondary rounded">
                                                                <i class="bi bi-file-earmark-check me-1"></i>{{ $mov->numero_ingreso }}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <label class="form-label text-muted fw-semibold small mb-1">N°
                                                            Salida</label>
                                                        <div class="p-2 bg-body-secondary rounded">
                                                            <i
                                                                class="bi bi-file-earmark-arrow-up me-1"></i>{{ $mov->numero_salida }}
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label text-muted fw-semibold small mb-1">Unidad
                                                            Solicitante</label>
                                                        <div class="p-2 bg-body-secondary rounded">
                                                            <i class="bi bi-building me-1"></i>{{ $mov->unidad_solicitante }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($mov->observaciones)
                                            <div class="col-12">
                                                <label class="form-label text-muted fw-semibold small mb-1">Observaciones</label>
                                                <p class="text-secondary bg-body-secondary p-3 rounded mb-0"
                                                    style="font-size: 0.9rem;">
                                                    {{ $mov->observaciones }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="card border-0 shadow-sm bg-body-tertiary mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center text-primary">
                                                <i class="bi bi-calculator me-2"></i>Valores del Movimiento
                                            </h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted small">Cantidad:</span>
                                                <span class="fw-bold text-body">
                                                    {{ number_format($mov->cantidad, 2) }}
                                                    {{ $mov->material->unidadMedida->abreviatura }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted small">Costo Unitario:</span>
                                                <span
                                                    class="fw-bold text-body">${{ number_format($mov->costo_unitario, 2) }}</span>
                                            </div>
                                            <hr class="opacity-25 my-2">
                                            <div
                                                class="p-2 bg-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }} bg-opacity-10 border border-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }} border-opacity-25 rounded text-center">
                                                <label
                                                    class="text-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }} small d-block fw-semibold">Total
                                                    del Movimiento</label>
                                                <span
                                                    class="h4 fw-bold text-{{ $mov->tipo_movimiento === 'entrada' ? 'success' : 'danger' }}">${{ number_format($mov->total, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                                        <div class="card-body p-3">
                                            <h6 class="fw-bold mb-3 d-flex align-items-center text-warning">
                                                <i class="bi bi-wallet2 me-2"></i>Saldo Resultante
                                            </h6>
                                            <div class="mb-2">
                                                <label class="text-muted small d-block">Saldo en Cantidad</label>
                                                <span class="h5 fw-bold text-warning">
                                                    {{ number_format($mov->saldo_cantidad, 2) }}
                                                    {{ $mov->material->unidadMedida->abreviatura }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="text-muted small d-block">Saldo en Valor</label>
                                                <span
                                                    class="h5 fw-bold text-warning">${{ number_format($mov->saldo_costo_total, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-body-tertiary border-top-0 d-flex justify-content-between p-3">
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock-history me-1"></i>
                                <strong>Registrado:</strong> {{ $mov->created_at->format('d/m/Y H:i') }}
                            </div>
                            <button type="button" class="btn btn-secondary px-4 shadow-sm"
                                data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- ════════════════════════════════
        MODALES: EDITAR MOVIMIENTO — solo admin y almacenero
        ════════════════════════════════ --}}
        @canEdit
            @foreach($movimientos as $mov)
                <div class="modal fade" id="editModal{{ $mov->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content shadow-lg border-0">
                            <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                                <h5 class="modal-title fw-bold d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                        <i class="bi bi-pencil-fill text-warning fs-5"></i>
                                    </div>
                                    <span class="text-body">Editar Movimiento</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('movimientos.update', $mov) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-muted">Material <span
                                                    class="text-danger">*</span></label>
                                            <select name="material_id" class="form-select" required>
                                                @foreach($materiales as $mat)
                                                    <option value="{{ $mat->id }}" {{ $mov->material_id == $mat->id ? 'selected' : '' }}>
                                                        {{ $mat->codigo }} - {{ $mat->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold text-muted">Tipo <span
                                                    class="text-danger">*</span></label>
                                            <select name="tipo_movimiento" class="form-select tipo-mov-select"
                                                data-modal="{{ $mov->id }}" required>
                                                <option value="entrada" {{ $mov->tipo_movimiento == 'entrada' ? 'selected' : '' }}>
                                                    Entrada</option>
                                                <option value="salida" {{ $mov->tipo_movimiento == 'salida' ? 'selected' : '' }}>
                                                    Salida</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold text-muted">Fecha <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" name="fecha" class="form-control"
                                                value="{{ $mov->fecha->format('Y-m-d') }}" required>
                                        </div>

                                        <div class="col-md-6 campos-entrada-{{ $mov->id }}"
                                            style="display: {{ $mov->tipo_movimiento == 'entrada' ? 'block' : 'none' }}">
                                            <label class="form-label small fw-semibold text-muted">N° Factura</label>
                                            <input type="text" name="numero_factura" class="form-control"
                                                value="{{ $mov->numero_factura }}" placeholder="Ej. 001-001-000123">
                                        </div>
                                        <div class="col-md-6 campos-entrada-{{ $mov->id }}"
                                            style="display: {{ $mov->tipo_movimiento == 'entrada' ? 'block' : 'none' }}">
                                            <label class="form-label small fw-semibold text-muted">N° Ingreso</label>
                                            <input type="text" name="numero_ingreso" class="form-control"
                                                value="{{ $mov->numero_ingreso }}" placeholder="Ej. ING-2024-001">
                                        </div>

                                        <div class="col-md-6 campos-salida-{{ $mov->id }}"
                                            style="display: {{ $mov->tipo_movimiento == 'salida' ? 'block' : 'none' }}">
                                            <label class="form-label small fw-semibold text-muted">N° Salida <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="numero_salida" class="form-control"
                                                value="{{ $mov->numero_salida }}" placeholder="Ej. SAL-2024-001">
                                        </div>
                                        <div class="col-md-6 campos-salida-{{ $mov->id }}"
                                            style="display: {{ $mov->tipo_movimiento == 'salida' ? 'block' : 'none' }}">
                                            <label class="form-label small fw-semibold text-muted">Unidad Solicitante <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="unidad_solicitante" class="form-control"
                                                value="{{ $mov->unidad_solicitante }}" placeholder="Ej. Jefe Técnico">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Cantidad <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="cantidad" class="form-control"
                                                value="{{ $mov->cantidad }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Costo Unitario <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="costo_unitario" class="form-control"
                                                value="{{ $mov->costo_unitario }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-muted">Total (calculado)</label>
                                            <input type="text" class="form-control" value="${{ number_format($mov->total, 2) }}"
                                                disabled>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label small fw-semibold text-muted">Observaciones</label>
                                            <textarea name="observaciones" class="form-control" rows="2"
                                                placeholder="Observaciones opcionales">{{ $mov->observaciones }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                                    <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                                        <i class="bi bi-save-fill me-2"></i>Actualizar Movimiento
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endcanEdit

    </div>
@endsection

@section('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                background: 'var(--card-bg)',
                color: 'var(--text-dark)',
                iconColor: '#FFC107'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                background: 'var(--card-bg)',
                color: 'var(--text-dark)',
                confirmButtonColor: '#E63946'
            });
        @endif

        function confirmDelete(id, material) {
            Swal.fire({
                title: '¿Eliminar movimiento?',
                html: `Se eliminará el movimiento del material <strong>"${material}"</strong>.<br>El inventario se ajustará automáticamente.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E63946',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)',
                color: 'var(--text-dark)'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        document.querySelectorAll('.tipo-mov-select').forEach(select => {
            select.addEventListener('change', function () {
                const tipo = this.value;
                const modalId = this.dataset.modal;
                document.querySelectorAll('.campos-entrada-' + modalId).forEach(el => el.style.display = tipo === 'entrada' ? 'block' : 'none');
                document.querySelectorAll('.campos-salida-' + modalId).forEach(el => el.style.display = tipo === 'salida' ? 'block' : 'none');
            });
        });

        document.addEventListener('DOMContentLoaded', function () {

            // ── Calculadora de totales ──
            function setupCalc(idQty, idPrice, idTotal) {
                const qty = document.getElementById(idQty);
                const price = document.getElementById(idPrice);
                const total = document.getElementById(idTotal);
                if (!qty || !price || !total) return;
                const calc = () => {
                    const v = (parseFloat(qty.value) || 0) * (parseFloat(price.value) || 0);
                    total.value = v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                };
                qty.addEventListener('input', calc);
                price.addEventListener('input', calc);
            }

            setupCalc('cantidad_entrada', 'costo_unitario_entrada', 'total_entrada');
            setupCalc('cantidad_salida', 'costo_unitario_salida', 'total_salida');

            // ── Paginación cliente ──
            const tbody = document.getElementById('movTableBody');
            const rowsSel = document.getElementById('rowsPerPage');
            const paginInfo = document.getElementById('paginationInfo');
            const paginCtrls = document.getElementById('paginationControls');

            let currentPage = 1;

            function getRows() {
                return Array.from(tbody.querySelectorAll('tr')).filter(r => !r.id || r.id !== 'emptyRow');
            }

            function render() {
                const rows = getRows();
                const perPage = parseInt(rowsSel.value);
                const total = rows.length;

                if (total === 0) { paginInfo.textContent = ''; paginCtrls.innerHTML = ''; return; }

                const totalPages = Math.ceil(total / perPage);
                if (currentPage > totalPages) currentPage = totalPages;

                const start = (currentPage - 1) * perPage;
                const end = Math.min(start + perPage, total);

                rows.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });

                paginInfo.textContent = `Mostrando ${start + 1}–${end} de ${total} registros`;
                buildControls(totalPages);
            }

            function buildControls(totalPages) {
                paginCtrls.innerHTML = '';
                paginCtrls.appendChild(makeBtn('‹', currentPage === 1, () => { currentPage--; render(); }));

                let s = Math.max(1, currentPage - 2);
                let e = Math.min(totalPages, s + 4);
                if (e - s < 4) s = Math.max(1, e - 4);

                if (s > 1) {
                    paginCtrls.appendChild(makeBtn('1', false, () => { currentPage = 1; render(); }));
                    if (s > 2) paginCtrls.appendChild(makeEllipsis());
                }

                for (let p = s; p <= e; p++) {
                    const pg = p;
                    const btn = makeBtn(p, false, () => { currentPage = pg; render(); });
                    if (p === currentPage) btn.classList.add('active');
                    paginCtrls.appendChild(btn);
                }

                if (e < totalPages) {
                    if (e < totalPages - 1) paginCtrls.appendChild(makeEllipsis());
                    paginCtrls.appendChild(makeBtn(totalPages, false, () => { currentPage = totalPages; render(); }));
                }

                paginCtrls.appendChild(makeBtn('›', currentPage === totalPages, () => { currentPage++; render(); }));
            }

            function makeBtn(label, disabled, onClick) {
                const b = document.createElement('button');
                b.className = 'page-btn';
                b.textContent = label;
                b.disabled = disabled;
                if (!disabled) b.addEventListener('click', onClick);
                return b;
            }

            function makeEllipsis() {
                const s = document.createElement('span');
                s.textContent = '…';
                s.style.cssText = 'padding: 0 4px; opacity: 0.4; font-size: 0.85rem; line-height: 32px;';
                return s;
            }

            rowsSel.addEventListener('change', () => { currentPage = 1; render(); });
            render();
        });
    </script>

    <script>
        // El selector de filas ahora recarga la página con per_page vía el form de filtros
        document.addEventListener('DOMContentLoaded', function () {
            const rowsSel = document.getElementById('rowsPerPage');
            const perPageInput = document.getElementById('perPageHidden');
            const filtrosForm = document.getElementById('filtrosForm');

            rowsSel.addEventListener('change', function () {
                perPageInput.value = this.value;
                filtrosForm.submit();
            });
        });
    </script>
@endsection