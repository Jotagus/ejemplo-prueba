@extends('layouts.app')

@section('page-title')<i class="bi bi-clipboard2-data-fill text-warning me-2"></i>Control de Inventario
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

        .badge-stock-ok {
            background-color: #10B981;
        }

        .badge-stock-low {
            background-color: #F59E0B;
        }

        .badge-stock-critical {
            background-color: #EF4444;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid var(--border-color);
        }

        .nav-tabs-custom .nav-link {
            color: var(--text-dark);
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #FF6B35;
            border-bottom-color: rgba(255, 107, 53, 0.3);
        }

        .nav-tabs-custom .nav-link.active {
            color: #FF6B35;
            border-bottom-color: #FF6B35;
            background: transparent;
        }

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
<div class="container-fluid">

    @php
        $criticos = $materiales->filter(fn($m) => $m->detalleMaterial && $m->detalleMaterial->cantidad_actual <= 0);
        $bajos = $materiales->filter(fn($m) => $m->detalleMaterial
            && $m->detalleMaterial->cantidad_actual > 0
            && $m->detalleMaterial->cantidad_actual <= $m->detalleMaterial->cantidad_minima);
        $alertas = $criticos->merge($bajos)->sortBy(fn($m) => $m->detalleMaterial->cantidad_actual);
    @endphp

    <ul class="nav nav-tabs nav-tabs-custom mb-3" id="invTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button"
                role="tab">
                <i class="bi bi-clipboard2-data me-2"></i>Control de Stock
                <span class="badge bg-warning text-dark ms-2">{{ $materiales->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="criticos-tab" data-bs-toggle="tab" data-bs-target="#criticos" type="button"
                role="tab">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Niveles Críticos
                @if($alertas->count() > 0)
                    <span class="badge bg-danger ms-2">{{ $alertas->count() }}</span>
                @else
                    <span class="badge bg-success ms-2">0</span>
                @endif
            </button>
        </li>
    </ul>

    <div class="tab-content" id="invTabContent">

        <div class="tab-pane fade show active" id="stock" role="tabpanel">

            <div class="d-flex align-items-center mb-3 gap-3">
                <div class="input-group shadow-sm" style="max-width: 450px;">
                    <span class="input-group-text bg-body-secondary border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="stockBuscador" class="form-control border-start-0"
                        placeholder="Buscar por código, nombre, categoría..." autocomplete="off">
                    <button class="btn btn-outline-secondary border-start-0" id="btnLimpiarStock" title="Limpiar"
                        style="display:none;">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="card-emdell shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4">Código</th>
                                <th>Material</th>
                                <th>Categoría</th>
                                <th>Subcategoría</th>
                                <th>Stock Actual</th>
                                <th>Stock Mín.</th>
                                <th>Precio Unit.</th>
                                <th>Costo Total</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="stockBody">
                            @forelse($materiales as $material)
                            @php
                                $det = $material->detalleMaterial;
                                $stockClass = 'badge-stock-ok';
                                $stockIcon = 'bi-check-circle-fill';
                                if ($det) {
                                    if ($det->cantidad_actual <= 0) {
                                        $stockClass = 'badge-stock-critical';
                                        $stockIcon = 'bi-x-circle-fill';
                                    } elseif ($det->cantidad_actual <= $det->cantidad_minima) {
                                        $stockClass = 'badge-stock-low';
                                        $stockIcon = 'bi-exclamation-triangle-fill';
                                    }
                                }
                            @endphp
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4">
                                    <span class="badge bg-secondary">{{ $material->codigo }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $material->nombre }}</span>
                                    @if($material->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($material->descripcion, 40) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border shadow-sm">
                                        {{ $material->subcategoria->categoria->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        {{ $material->subcategoria->nombre }}
                                    </span>
                                </td>
                                <td>
                                    @if($det)
                                        <span class="badge {{ $stockClass }} text-white">
                                            <i class="bi {{ $stockIcon }} me-1"></i>
                                            {{ number_format($det->cantidad_actual, 2) }}
                                            {{ $material->unidadMedida->abreviatura }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Sin registro</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $det ? number_format($det->cantidad_minima, 2) : '—' }}
                                </td>
                                <td>
                                    {{ $det ? '$' . number_format($det->precio_unitario, 2) : '—' }}
                                </td>
                                <td>
                                    @if($det)
                                        <span class="fw-bold text-success">${{ number_format($det->costo_total, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Botón Ver (todos los roles) --}}
                                    <button class="btn btn-sm btn-outline-info me-1" data-bs-toggle="modal"
                                        data-bs-target="#showModal{{ $material->id }}" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @canEdit
                                    {{-- Botón Editar --}}
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#editStockModal{{ $material->id }}" title="Actualizar stock">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endcanEdit
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyStock">
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay materiales registrados en inventario</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rows-selector">
                            Mostrar
                            <select id="rowsStock">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            filas
                        </div>
                        <div class="pagination-info" id="stockInfo">Mostrando — de — materiales</div>
                    </div>
                    <div class="pagination-controls" id="stockControles"></div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="criticos" role="tabpanel">

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                <small class="text-muted">Materiales que requieren atención o reposición inmediata</small>
                <div class="ms-auto d-flex gap-2 flex-wrap">
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                        <i class="bi bi-x-circle-fill me-1"></i>Sin stock: {{ $criticos->count() }}
                    </span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Stock bajo: {{ $bajos->count() }}
                    </span>
                </div>
            </div>

            <div class="card-emdell shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="small text-muted">
                                <th class="ps-4" style="width:36px;">Nº</th>
                                <th>Material</th>
                                <th>Categoría</th>
                                <th>Nivel de Alerta</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Déficit</th>
                                <th>Precio Unit.</th>
                                <th>Costo Reposición Est.</th>
                            </tr>
                        </thead>
                        <tbody id="criticosBody">
                            @if($alertas->count() === 0)
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="d-inline-flex flex-column align-items-center gap-2">
                                            <span
                                                class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                                                style="width:56px; height:56px; font-size:1.6rem;">
                                                <i class="bi bi-check-circle-fill"></i>
                                            </span>
                                            <span class="fw-bold text-body fs-6">¡Todo bajo control!</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @foreach($alertas as $i => $material)
                                @php
                                    $det = $material->detalleMaterial;
                                    $deficit = max(0, $det->cantidad_minima - $det->cantidad_actual);
                                    $costoRep = $deficit * $det->precio_unitario;
                                    $esCrit = $det->cantidad_actual <= 0;
                                    $pct = $det->cantidad_minima > 0
                                        ? min(100, ($det->cantidad_actual / $det->cantidad_minima) * 100)
                                        : 0;
                                @endphp
                                <tr style="border-color: var(--border-color);">
                                    <td class="ps-4"><span class="text-muted small">{{ $i + 1 }}</span></td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $material->nombre }}</span>
                                        <small class="text-muted">
                                            <span
                                                class="badge bg-secondary bg-opacity-25 text-secondary">{{ $material->codigo }}</span>
                                            &nbsp;{{ $material->unidadMedida->abreviatura }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border shadow-sm d-block mb-1"
                                            style="width:fit-content;">
                                            {{ $material->subcategoria->categoria->nombre }}
                                        </span>
                                        <span
                                            class="badge bg-warning text-dark">{{ $material->subcategoria->nombre }}</span>
                                    </td>
                                    <td>
                                        @if($esCrit)
                                            <span class="badge bg-danger px-2 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i>Sin Stock
                                            </span>
                                            <small class="text-danger fw-semibold d-block mt-1">Reposición urgente</small>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-2">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Stock Bajo
                                            </span>
                                            <small class="text-warning fw-semibold d-block mt-1">Necesita reposición</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ $esCrit ? 'text-danger' : 'text-warning' }}">
                                            {{ number_format($det->cantidad_actual, 2) }}
                                        </span>
                                        <div class="progress mt-1" style="height:4px; width:80px;">
                                            <div class="progress-bar {{ $esCrit ? 'bg-danger' : 'bg-warning' }}"
                                                style="width:{{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted">{{ number_format($det->cantidad_minima, 2) }}</span></td>
                                    <td>
                                        <span class="fw-bold text-danger">
                                            {{ $deficit > 0 ? '+' . number_format($deficit, 2) : '—' }}
                                        </span>
                                        <small class="text-muted d-block">unidades</small>
                                    </td>
                                    <td><span class="fw-bold">${{ number_format($det->precio_unitario, 2) }}</span></td>
                                    <td>
                                        @if($costoRep > 0)
                                            <span class="fw-bold text-danger">${{ number_format($costoRep, 2) }}</span>
                                            <small class="text-muted d-block">para alcanzar mínimo</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rows-selector">
                            Mostrar
                            <select id="rowsCriticos">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            filas
                        </div>
                        <div class="pagination-info" id="criticosInfo">Mostrando — de — alertas</div>
                    </div>
                    <div class="pagination-controls" id="criticosControles"></div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══ MODALES VER ═══ --}}
@foreach($materiales as $material)
    <div class="modal fade" id="showModal{{ $material->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-box-seam text-info fs-4"></i>
                        </div>
                        <div>
                            <span class="text-uppercase small text-muted d-block" style="font-size:0.7rem; letter-spacing:1px;">Detalles del Material</span>
                            <span class="text-body">{{ $material->nombre }}</span>
                        </div>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">Código</label>
                            <div class="p-2 bg-body-secondary rounded border-start border-info border-3">
                                <code class="fw-bold text-info-emphasis">{{ $material->codigo }}</code>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">Unidad de Medida</label>
                            <div class="p-2 bg-body-secondary rounded">
                                {{ $material->unidadMedida->nombre }} ({{ $material->unidadMedida->abreviatura }})
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">Categoría</label>
                            <div>
                                <span class="badge rounded-pill bg-body text-body border px-3 py-2">
                                    <i class="bi bi-tag-fill me-1 text-secondary"></i>
                                    {{ $material->subcategoria->categoria->nombre }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">Subcategoría</label>
                            <div>
                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 border border-warning-subtle">
                                    <i class="bi bi-diagram-3-fill me-1"></i>
                                    {{ $material->subcategoria->nombre }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">Estado</label>
                            <div>
                                @if($material->estado)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                        <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted fw-semibold small mb-1">Descripción</label>
                            <p class="text-secondary bg-body-secondary p-3 rounded mb-0" style="font-size:0.9rem; min-height:70px;">
                                {{ $material->descripcion ?? 'Sin descripción registrada.' }}
                            </p>
                        </div>

                        {{-- ── DATOS DE STOCK ── --}}
                        @if($material->detalleMaterial)
                        @php $det = $material->detalleMaterial; @endphp
                        <div class="col-12 mt-1">
                            <hr class="opacity-25">
                            <p class="text-uppercase small fw-bold text-muted mb-3" style="letter-spacing:0.08em;">
                                <i class="bi bi-box-seam me-1"></i>Información de Stock
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Stock Actual</label>
                            <div class="p-2 bg-body-secondary rounded text-center">
                                @php
                                    $colorStock = 'text-success';
                                    if ($det->cantidad_actual <= 0) $colorStock = 'text-danger';
                                    elseif ($det->cantidad_actual <= $det->cantidad_minima) $colorStock = 'text-warning';
                                @endphp
                                <span class="fw-bold fs-5 {{ $colorStock }}">
                                    {{ number_format($det->cantidad_actual, 2) }}
                                </span>
                                <small class="text-muted d-block">{{ $material->unidadMedida->abreviatura }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Stock Mínimo</label>
                            <div class="p-2 bg-body-secondary rounded text-center">
                                <span class="fw-bold fs-5 text-secondary">
                                    {{ number_format($det->cantidad_minima, 2) }}
                                </span>
                                <small class="text-muted d-block">{{ $material->unidadMedida->abreviatura }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Precio Unitario</label>
                            <div class="p-2 bg-body-secondary rounded text-center">
                                <span class="fw-bold fs-5 text-info">
                                    ${{ number_format($det->precio_unitario, 2) }}
                                </span>
                                <small class="text-muted d-block">por unidad</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Valor Total</label>
                            <div class="p-2 bg-body-secondary rounded text-center">
                                <span class="fw-bold fs-5 text-success">
                                    ${{ number_format($det->costo_total, 2) }}
                                </span>
                                <small class="text-muted d-block">en inventario</small>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
                <div class="modal-footer bg-body-tertiary border-top-0 d-flex justify-content-between p-3">
                    <div class="d-flex gap-3 text-muted" style="font-size:0.75rem;">
                        <span><i class="bi bi-calendar-plus me-1"></i><strong>Creado:</strong> {{ $material->created_at->format('d/m/Y H:i') }}</span>
                        <span><i class="bi bi-calendar-check me-1"></i><strong>Actualizado:</strong> {{ $material->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- ═══ MODALES EDITAR ═══ --}}
@canEdit
@foreach($materiales as $material)
    <div class="modal fade" id="editStockModal{{ $material->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-pencil-square text-warning fs-5"></i>
                        </div>
                        <div>
                            <span class="text-uppercase small text-muted d-block"
                                style="font-size:0.7rem; letter-spacing:1px;">Actualizar Stock</span>
                            <span class="text-body fs-6">{{ $material->nombre }}</span>
                        </div>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('inventario.update', $material) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        <div class="mb-3 p-3 bg-body-secondary rounded">
                            <span class="small text-muted">Código: </span>
                            <code class="text-info fw-bold">{{ $material->codigo }}</code>
                            <span class="ms-3 small text-muted">Unidad: </span>
                            <span class="fw-semibold">{{ $material->unidadMedida->abreviatura }}</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">
                                    Cantidad Inicial <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="cantidad_actual"
                                    class="form-control border-success-subtle"
                                    value="{{ $material->detalleMaterial->cantidad_actual ?? 0 }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">
                                    Cantidad Mínima <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" name="cantidad_minima"
                                    class="form-control border-danger-subtle"
                                    value="{{ $material->detalleMaterial->cantidad_minima ?? 0 }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">
                                    Precio Unitario <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span
                                        class="input-group-text bg-success bg-opacity-10 text-success border-success-subtle">$</span>
                                    <input type="number" step="0.01" name="precio_unitario"
                                        class="form-control border-success-subtle"
                                        value="{{ $material->detalleMaterial->precio_unitario ?? 0 }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                            <i class="bi bi-save-fill me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endcanEdit

@endsection

@section('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success', title: '¡Operación Exitosa!',
                text: "{{ session('success') }}", timer: 2500, showConfirmButton: false,
                background: 'var(--card-bg)', color: 'var(--text-dark)', iconColor: '#FFC107'
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error', title: 'Error', text: "{{ session('error') }}",
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            });
        @endif
    </script>

    <script>
        function setupPagination({ tbodyId, rowsSelId, infoId, ctrlsId, emptyId, label }) {
            const tbody = document.getElementById(tbodyId);
            const rowsSel = document.getElementById(rowsSelId);
            const paginInfo = document.getElementById(infoId);
            const paginCtrls = document.getElementById(ctrlsId);
            if (!tbody) return;

            let currentPage = 1;

            function getAllRows() {
                return Array.from(tbody.querySelectorAll('tr')).filter(r => !emptyId || r.id !== emptyId);
            }

            function render() {
                const rows = getAllRows();
                const total = rows.length;
                if (total === 0) { paginInfo.textContent = ''; paginCtrls.innerHTML = ''; return; }

                const perPage = parseInt(rowsSel.value);
                const totalPages = Math.ceil(total / perPage);
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end = Math.min(start + perPage, total);

                rows.forEach((r, i) => { r.style.display = (i >= start && i < end) ? '' : 'none'; });
                paginInfo.textContent = `Mostrando ${start + 1}–${end} de ${total} ${label}`;
                buildControls(totalPages);
            }

            function buildControls(totalPages) {
                paginCtrls.innerHTML = '';
                paginCtrls.appendChild(makeBtn('‹', currentPage === 1, () => { currentPage--; render(); }));
                let s = Math.max(1, currentPage - 2), e = Math.min(totalPages, s + 4);
                if (e - s < 4) s = Math.max(1, e - 4);
                if (s > 1) { paginCtrls.appendChild(makeBtn('1', false, () => { currentPage = 1; render(); })); if (s > 2) paginCtrls.appendChild(makeEllipsis()); }
                for (let p = s; p <= e; p++) { const pg = p, btn = makeBtn(p, false, () => { currentPage = pg; render(); }); if (p === currentPage) btn.classList.add('active'); paginCtrls.appendChild(btn); }
                if (e < totalPages) { if (e < totalPages - 1) paginCtrls.appendChild(makeEllipsis()); paginCtrls.appendChild(makeBtn(totalPages, false, () => { currentPage = totalPages; render(); })); }
                paginCtrls.appendChild(makeBtn('›', currentPage === totalPages, () => { currentPage++; render(); }));
            }

            function makeBtn(label, disabled, onClick) {
                const b = document.createElement('button');
                b.className = 'page-btn'; b.textContent = label; b.disabled = disabled;
                if (!disabled) b.addEventListener('click', onClick);
                return b;
            }
            function makeEllipsis() {
                const s = document.createElement('span');
                s.textContent = '…'; s.style.cssText = 'padding:0 4px;opacity:.4;font-size:.85rem;line-height:32px;';
                return s;
            }

            rowsSel.addEventListener('change', () => { currentPage = 1; render(); });
            render();
        }

        document.addEventListener('DOMContentLoaded', function () {

            setupPagination({ tbodyId: 'criticosBody', rowsSelId: 'rowsCriticos', infoId: 'criticosInfo', ctrlsId: 'criticosControles', label: 'alertas' });

            const input = document.getElementById('stockBuscador');
            const btnClear = document.getElementById('btnLimpiarStock');
            const tbody = document.getElementById('stockBody');
            const rowsSel = document.getElementById('rowsStock');
            const paginInfo = document.getElementById('stockInfo');
            const paginCtrls = document.getElementById('stockControles');

            let currentPage = 1;
            let filteredRows = [];

            const noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.style.display = 'none';
            noResultRow.innerHTML = `<td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                                    No se encontraron materiales para "<span id="busquedaTexto"></span>"</td>`;
            tbody.appendChild(noResultRow);

            function getAllRows() {
                return Array.from(tbody.querySelectorAll('tr'))
                    .filter(r => r.id !== 'noResultRow' && r.id !== 'emptyStock');
            }

            function applyFilter(term) {
                const q = term.trim().toLowerCase();
                const all = getAllRows();
                filteredRows = q === '' ? all : all.filter(r => r.innerText.toLowerCase().includes(q));
                btnClear.style.display = q ? '' : 'none';
                currentPage = 1;
                render();
            }

            function render() {
                const total = filteredRows.length;
                getAllRows().forEach(r => r.style.display = 'none');
                noResultRow.style.display = 'none';

                if (total === 0) {
                    noResultRow.style.display = '';
                    const sp = document.getElementById('busquedaTexto');
                    if (sp) sp.textContent = input.value.trim();
                    paginInfo.textContent = 'Sin resultados';
                    paginCtrls.innerHTML = '';
                    return;
                }

                const perPage = parseInt(rowsSel.value);
                const totalPages = Math.ceil(total / perPage);
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end = Math.min(start + perPage, total);

                filteredRows.slice(start, end).forEach(r => r.style.display = '');
                paginInfo.textContent = `Mostrando ${start + 1}–${end} de ${total} materiales`;
                buildControls(totalPages);
            }

            function buildControls(totalPages) {
                paginCtrls.innerHTML = '';
                paginCtrls.appendChild(makeBtn('‹', currentPage === 1, () => { currentPage--; render(); }));
                let s = Math.max(1, currentPage - 2), e = Math.min(totalPages, s + 4);
                if (e - s < 4) s = Math.max(1, e - 4);
                if (s > 1) { paginCtrls.appendChild(makeBtn('1', false, () => { currentPage = 1; render(); })); if (s > 2) paginCtrls.appendChild(makeEllipsis()); }
                for (let p = s; p <= e; p++) { const pg = p, btn = makeBtn(p, false, () => { currentPage = pg; render(); }); if (p === currentPage) btn.classList.add('active'); paginCtrls.appendChild(btn); }
                if (e < totalPages) { if (e < totalPages - 1) paginCtrls.appendChild(makeEllipsis()); paginCtrls.appendChild(makeBtn(totalPages, false, () => { currentPage = totalPages; render(); })); }
                paginCtrls.appendChild(makeBtn('›', currentPage === totalPages, () => { currentPage++; render(); }));
            }

            function makeBtn(label, disabled, onClick) {
                const b = document.createElement('button');
                b.className = 'page-btn'; b.textContent = label; b.disabled = disabled;
                if (!disabled) b.addEventListener('click', onClick);
                return b;
            }
            function makeEllipsis() {
                const s = document.createElement('span');
                s.textContent = '…'; s.style.cssText = 'padding:0 4px;opacity:.4;font-size:.85rem;line-height:32px;';
                return s;
            }

            input.addEventListener('input', () => applyFilter(input.value));
            btnClear.addEventListener('click', () => { input.value = ''; applyFilter(''); input.focus(); });
            rowsSel.addEventListener('change', () => { currentPage = 1; render(); });
            applyFilter('');
        });
    </script>
@endsection