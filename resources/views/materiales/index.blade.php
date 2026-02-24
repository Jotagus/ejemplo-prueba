@extends('layouts.app')

@section('page-title')<i class="bi bi-box-seam-fill text-warning me-2"></i>Catálogo de Materiales
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

        .table { color: var(--text-dark); }
        .table thead { background-color: rgba(255, 107, 53, 0.05); }

        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-dark);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .modal-header, .modal-footer { border-color: var(--border-color); }

        .form-control, .form-select {
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

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 1.25rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pagination-info { font-size: 0.8rem; color: var(--text-dark); opacity: 0.55; }
        .pagination-controls { display: flex; gap: 0.25rem; align-items: center; }

        .page-btn {
            min-width: 32px; height: 32px; padding: 0 0.45rem;
            border-radius: 8px; border: 1px solid var(--border-color);
            background: var(--card-bg); color: var(--text-dark);
            font-size: 0.8rem; font-weight: 600; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.18s ease; opacity: 0.75;
        }

        .page-btn:hover:not(:disabled) { border-color: #FFC107; color: #FFC107; opacity: 1; }
        .page-btn.active { background: #FFC107; border-color: #FFC107; color: #000; opacity: 1; font-weight: 700; }
        .page-btn:disabled { opacity: 0.25; cursor: not-allowed; }

        .rows-selector {
            display: flex; align-items: center; gap: 0.4rem;
            font-size: 0.8rem; color: var(--text-dark); opacity: 0.6;
        }

        .rows-selector select {
            padding: 0.2rem 0.5rem; border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--card-bg); color: var(--text-dark);
            font-size: 0.8rem; cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- ENCABEZADO --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="input-group shadow-sm" style="max-width: 450px; flex-grow: 1;">
                <span class="input-group-text bg-body-secondary border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="tablaBuscador" class="form-control border-start-0"
                    placeholder="Buscar por código, nombre, categoría..." autocomplete="off">
                <button class="btn btn-outline-secondary border-start-0" id="btnLimpiarBusqueda"
                    title="Limpiar" style="display:none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- Solo admin y almacenero ven el botón Nuevo Material --}}
            @canEdit
                <button class="btn btn-warning text-white fw-bold shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle me-1"></i> Nuevo Material
                </button>
            @endcanEdit
        </div>

        {{-- TABLA --}}
        <div class="card-emdell shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-4">Código</th>
                            <th>Material</th>
                            <th>Estado</th>
                            <th>Categoría</th>
                            <th>Subcategoría</th>
                            <th>Unidad</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaBody">
                        @forelse($materiales as $material)
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4">
                                    <span class="badge bg-secondary">{{ $material->codigo }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $material->nombre }}</span>
                                    @if($material->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($material->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($material->estado)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-check-circle-fill me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                        </span>
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
                                <td class="small text-muted">
                                    {{ $material->unidadMedida->nombre }}
                                    <span class="text-muted">({{ $material->unidadMedida->abreviatura }})</span>
                                </td>
                                <td class="text-center">
                                    {{-- Ver — todos los roles --}}
                                    <button class="btn btn-sm btn-outline-info me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#showModal{{ $material->id }}"
                                        title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Editar y Eliminar — solo admin y almacenero --}}
                                    @canEdit
                                        <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $material->id }}"
                                            title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $material->id }}, '{{ $material->nombre }}')"
                                            title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $material->id }}"
                                            action="{{ route('materiales.destroy', $material) }}"
                                            method="POST" style="display:none;">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endcanEdit
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyOriginal">
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay materiales registrados</p>
                                    @canEdit
                                        <button class="btn btn-sm btn-warning mt-2"
                                            data-bs-toggle="modal" data-bs-target="#createModal">
                                            <i class="bi bi-plus-lg"></i> Crear primer material
                                        </button>
                                    @endcanEdit
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
                        <select id="rowsPerPage">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        filas
                    </div>
                    <div class="pagination-info" id="paginacionInfo">Mostrando — de — materiales</div>
                </div>
                <div class="pagination-controls" id="paginacionControles"></div>
            </div>
        </div>
    </div>

    {{-- MODAL VER — todos los roles --}}
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

    {{-- MODALES EDITAR — solo admin y almacenero --}}
    @canEdit
        @foreach($materiales as $material)
            <div class="modal fade" id="editModal{{ $material->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                    <i class="bi bi-pencil-fill text-warning fs-5"></i>
                                </div>
                                <span class="text-body">Editar Material</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('materiales.update', $material) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Código <span class="text-danger">*</span></label>
                                        <input type="text" name="codigo" class="form-control" value="{{ $material->codigo }}" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold text-muted">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre" class="form-control" value="{{ $material->nombre }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-muted">Estado <span class="text-danger">*</span></label>
                                        <select name="estado" class="form-select" required>
                                            <option value="1" {{ $material->estado ? 'selected' : '' }}>🟢 Activo</option>
                                            <option value="0" {{ !$material->estado ? 'selected' : '' }}>🔴 Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold text-muted">Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="2">{{ $material->descripcion }}</textarea>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">Subcategoría <span class="text-danger">*</span></label>
                                        <select name="subcategoria_id" class="form-select" required>
                                            @foreach($subcategorias as $sub)
                                                <option value="{{ $sub->id }}" {{ $material->subcategoria_id == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->categoria->nombre }} / {{ $sub->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold text-muted">Unidad de Medida <span class="text-danger">*</span></label>
                                        <select name="unidad_medida_id" class="form-select" required>
                                            @foreach($unidadesMedida as $unidad)
                                                <option value="{{ $unidad->id }}" {{ $material->unidad_medida_id == $unidad->id ? 'selected' : '' }}>
                                                    {{ $unidad->nombre }} ({{ $unidad->abreviatura }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                                    <i class="bi bi-save-fill me-2"></i>Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endcanEdit

    {{-- MODAL CREAR — solo admin y almacenero --}}
    @canEdit
        <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0">
                    <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                        <h5 class="modal-title fw-bold d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="bi bi-box-seam-fill text-warning fs-4"></i>
                            </div>
                            <span class="text-body">Nuevo Material</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('materiales.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Código <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-body-secondary border-end-0"><i class="bi bi-hash text-muted"></i></span>
                                        <input type="text" name="codigo" class="form-control border-start-0" placeholder="Ej. MAT-001" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-muted">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Tornillo hexagonal M8" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-muted">Estado <span class="text-danger">*</span></label>
                                    <select name="estado" class="form-select" required>
                                        <option value="1" selected>🟢 Activo</option>
                                        <option value="0">🔴 Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold text-muted">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="2"
                                        placeholder="Detalles técnicos o notas adicionales..."></textarea>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Categoría <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-body-secondary"><i class="bi bi-grid-fill text-muted"></i></span>
                                        <select id="categoria_select" class="form-select" required>
                                            <option value="" disabled selected>Elegir...</option>
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Subcategoría <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-body-secondary"><i class="bi bi-node-plus text-muted"></i></span>
                                        <select name="subcategoria_id" id="subcategoria_select" class="form-select" required>
                                            <option value="" disabled selected>Elegir...</option>
                                            @foreach($subcategorias as $sub)
                                                <option value="{{ $sub->id }}" data-categoria="{{ $sub->categoria_id }}">
                                                    {{ $sub->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-muted">Unidad de Medida <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select name="unidad_medida_id" class="form-select" required>
                                            <option value="" disabled selected>Unidad...</option>
                                            @foreach($unidadesMedida as $unidad)
                                                <option value="{{ $unidad->id }}">{{ $unidad->abreviatura }}</option>
                                            @endforeach
                                        </select>
                                        <a href="{{ route('unidades.create') }}" class="btn btn-outline-warning">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 rounded-4 bg-body-tertiary border border-dashed">
                                <p class="small text-muted mb-3 fw-semibold">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Podrá ajustar las cantidades en el módulo (Inventario)
                                </p>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Cantidad Inicial</label>
                                        <input type="number" step="0.01" name="cantidad_actual"
                                            class="form-control border-success-subtle" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Stock Mínimo</label>
                                        <input type="number" step="0.01" name="cantidad_minima"
                                            class="form-control border-danger-subtle" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-muted">Precio Unitario</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success bg-opacity-10 text-success border-success-subtle">$</span>
                                            <input type="number" step="0.01" name="precio_unitario"
                                                class="form-control border-success-subtle" placeholder="0.00" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                            <button type="button" class="btn btn-link text-secondary text-decoration-none px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i>Registrar Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

        @canEdit
        function confirmDelete(id, name) {
            Swal.fire({
                title: '¿Eliminar material?',
                text: `Estás a punto de eliminar "${name}". Esta acción no se puede deshacer.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#EF4444', cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            }).then(r => { if (r.isConfirmed) document.getElementById('delete-form-' + id).submit(); });
        }
        @endcanEdit

        document.addEventListener('DOMContentLoaded', function () {
            const catSel = document.getElementById('categoria_select');
            const subSel = document.getElementById('subcategoria_select');
            if (catSel && subSel) {
                const allOpts = Array.from(subSel.querySelectorAll('option'));
                catSel.addEventListener('change', function () {
                    subSel.innerHTML = '<option value="" disabled selected>Elegir...</option>';
                    const filtered = allOpts.filter(o => o.value && o.dataset.categoria === this.value);
                    filtered.forEach(o => subSel.appendChild(o.cloneNode(true)));
                    subSel.disabled = filtered.length === 0;
                    subSel.value = '';
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input      = document.getElementById('tablaBuscador');
            const btnClear   = document.getElementById('btnLimpiarBusqueda');
            const tbody      = document.getElementById('tablaBody');
            const rowsSel    = document.getElementById('rowsPerPage');
            const paginInfo  = document.getElementById('paginacionInfo');
            const paginCtrls = document.getElementById('paginacionControles');

            let currentPage  = 1;
            let filteredRows = [];

            const noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.style.display = 'none';
            noResultRow.innerHTML = `<td colspan="7" class="text-center text-muted py-4">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                No se encontraron materiales para "<span id="busquedaTexto"></span>"</td>`;
            tbody.appendChild(noResultRow);

            function getAllRows() {
                return Array.from(tbody.querySelectorAll('tr'))
                    .filter(r => r.id !== 'noResultRow' && r.id !== 'emptyOriginal');
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
                    paginCtrls.innerHTML  = '';
                    return;
                }

                const perPage    = parseInt(rowsSel.value);
                const totalPages = Math.ceil(total / perPage);
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end   = Math.min(start + perPage, total);

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