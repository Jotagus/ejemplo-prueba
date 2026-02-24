@extends('layouts.app')

@section('page-title')<i class="bi bi-folder-fill text-warning me-2"></i>
Gestión de Categorías y Subcategorías
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

        .nav-tabs-custom { border-bottom: 2px solid var(--border-color); }

        .nav-tabs-custom .nav-link {
            color: var(--text-dark);
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-tabs-custom .nav-link:hover { color: #FF6B35; border-bottom-color: rgba(255,107,53,0.3); }
        .nav-tabs-custom .nav-link.active { color: #FF6B35; border-bottom-color: #FF6B35; background: transparent; }

        .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dropdown-item { color: var(--text-dark); padding: 0.5rem 1rem; transition: all 0.2s; }
        .dropdown-item:hover { background-color: rgba(255,107,53,0.1); color: #FF6B35; }
        .dropdown-item i { width: 20px; text-align: center; }

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);"></h4>
            @canEdit
                <div class="dropdown">
                    <button class="btn btn-warning text-white fw-bold shadow-sm dropdown-toggle"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-plus-circle me-1"></i> Nuevo
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createCategoriaModal">
                                <i class="bi bi-folder-fill text-warning me-2"></i> Nueva Categoría
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#createSubcategoriaModal">
                                <i class="bi bi-folder2-open text-info me-2"></i> Nueva Subcategoría
                            </a>
                        </li>
                    </ul>
                </div>
            @endcanEdit
        </div>
        <ul class="nav nav-tabs nav-tabs-custom mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="categorias-tab" data-bs-toggle="tab"
                    data-bs-target="#categorias" type="button" role="tab">
                    <i class="bi bi-folder-fill me-2"></i>Categorías
                    <span class="badge bg-warning text-dark ms-2">{{ $categorias->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subcategorias-tab" data-bs-toggle="tab"
                    data-bs-target="#subcategorias" type="button" role="tab">
                    <i class="bi bi-folder2-open me-2"></i>Subcategorías
                    <span class="badge bg-info text-dark ms-2">{{ $subcategorias->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="categorias" role="tabpanel">
                <div class="card-emdell shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="small text-muted">
                                    <th class="ps-4">Nº</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Subcategorías</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="categoriasBody">
                                @forelse($categorias as $categoria)
                                    <tr style="border-color: var(--border-color);">
                                        <td class="ps-4">
                                            <span class="badge bg-secondary">{{ $categoria->id }}</span>
                                        </td>
                                        <td>
                                            <i class="bi bi-folder-fill text-warning me-2"></i>
                                            <span class="fw-bold">{{ $categoria->nombre }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ Str::limit($categoria->descripcion ?? 'Sin descripción', 50) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $categoria->subcategorias->count() }}</span>
                                        </td>
                                        <td class="text-center">
                                            @canEdit
                                                <button class="btn btn-sm btn-outline-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCategoriaModal{{ $categoria->id }}"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDeleteCategoria({{ $categoria->id }}, '{{ $categoria->nombre }}')"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="delete-categoria-{{ $categoria->id }}"
                                                    action="{{ route('categorias.destroy', $categoria) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="bi bi-eye me-1"></i>Solo lectura
                                                </span>
                                            @endcanEdit
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyCategorias">
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <p class="mb-0">No hay categorías registradas</p>
                                            @canEdit
                                                <button class="btn btn-sm btn-warning mt-2"
                                                    data-bs-toggle="modal" data-bs-target="#createCategoriaModal">
                                                    <i class="bi bi-plus-lg"></i> Crear primera categoría
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
                                <select id="rowsCategoria">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                filas
                            </div>
                            <div class="pagination-info" id="categoriasInfo">Mostrando — de — categorías</div>
                        </div>
                        <div class="pagination-controls" id="categoriasControles"></div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="subcategorias" role="tabpanel">
                <div class="card-emdell shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="small text-muted">
                                    <th class="ps-4">Nº</th>
                                    <th>Categoría</th>
                                    <th>Subcategoría</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="subcategoriasBody">
                                @forelse($subcategorias as $subcategoria)
                                    <tr style="border-color: var(--border-color);">
                                        <td class="ps-4">
                                            <span class="badge bg-secondary">{{ $subcategoria->id }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border shadow-sm">
                                                <i class="bi bi-folder-fill text-warning me-1"></i>
                                                {{ $subcategoria->categoria->nombre }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-folder2-open text-info me-2"></i>
                                            <span class="fw-bold">{{ $subcategoria->nombre }}</span>
                                        </td>
                                        <td class="small text-muted">
                                            {{ Str::limit($subcategoria->descripcion ?? 'Sin descripción', 50) }}
                                        </td>
                                        <td class="text-center">
                                            @canEdit
                                                <button class="btn btn-sm btn-outline-warning me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editSubcategoriaModal{{ $subcategoria->id }}"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDeleteSubcategoria({{ $subcategoria->id }}, '{{ $subcategoria->nombre }}')"
                                                    title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <form id="delete-subcategoria-{{ $subcategoria->id }}"
                                                    action="{{ route('subcategorias.destroy', $subcategoria) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                            @else
                                                <span class="text-muted small">
                                                    <i class="bi bi-eye me-1"></i>Solo lectura
                                                </span>
                                            @endcanEdit
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptySubcategorias">
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <p class="mb-0">No hay subcategorías registradas</p>
                                            @canEdit
                                                @if($categorias->count() > 0)
                                                    <button class="btn btn-sm btn-warning mt-2"
                                                        data-bs-toggle="modal" data-bs-target="#createSubcategoriaModal">
                                                        <i class="bi bi-plus-lg"></i> Crear primera subcategoría
                                                    </button>
                                                @else
                                                    <p class="small text-muted mt-2">Primero debes crear una categoría</p>
                                                @endif
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
                                <select id="rowsSubcategoria">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                filas
                            </div>
                            <div class="pagination-info" id="subcategoriasInfo">Mostrando — de — subcategorías</div>
                        </div>
                        <div class="pagination-controls" id="subcategoriasControles"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @canEdit
        <div class="modal fade" id="createCategoriaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-folder-fill me-2"></i>Nueva Categoría
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('categorias.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ej. Ferretería, Electricidad" required autofocus>
                            </div>
                            <div>
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"
                                    placeholder="Descripción opcional"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning text-white fw-bold">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="createSubcategoriaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-folder2-open me-2"></i>Nueva Subcategoría
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('subcategorias.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Categoría Padre <span class="text-danger">*</span></label>
                                <select name="categoria_id" class="form-select" required>
                                    <option value="" disabled selected>Selecciona una categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                    placeholder="Ej. Tornillos, Cables, Tuercas" required>
                            </div>
                            <div>
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"
                                    placeholder="Descripción opcional"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-info text-white fw-bold">
                                <i class="bi bi-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @foreach($categorias as $categoria)
            <div class="modal fade" id="editCategoriaModal{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-fill text-warning me-2"></i>Editar Categoría
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(0.5);"></button>
                        </div>
                        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control"
                                        value="{{ $categoria->nombre }}" required>
                                </div>
                                <div>
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3">{{ $categoria->descripcion }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning text-white fw-bold">
                                    <i class="bi bi-check-lg"></i> Actualizar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @foreach($subcategorias as $subcategoria)
            <div class="modal fade" id="editSubcategoriaModal{{ $subcategoria->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-fill text-info me-2"></i>Editar Subcategoría
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(0.5);"></button>
                        </div>
                        <form action="{{ route('subcategorias.update', $subcategoria) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Categoría Padre <span class="text-danger">*</span></label>
                                    <select name="categoria_id" class="form-select" required>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ $subcategoria->categoria_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" class="form-control"
                                        value="{{ $subcategoria->nombre }}" required>
                                </div>
                                <div>
                                    <label class="form-label">Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="3">{{ $subcategoria->descripcion }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-info text-white fw-bold">
                                    <i class="bi bi-check-lg"></i> Actualizar
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

        @canEdit
        function confirmDeleteCategoria(id, nombre) {
            Swal.fire({
                title: '¿Eliminar categoría?',
                html: `Se eliminará <strong>"${nombre}"</strong> y todas sus subcategorías.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#E63946', cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            }).then(r => { if (r.isConfirmed) document.getElementById('delete-categoria-' + id).submit(); });
        }

        function confirmDeleteSubcategoria(id, nombre) {
            Swal.fire({
                title: '¿Eliminar subcategoría?',
                html: `Se eliminará la subcategoría <strong>"${nombre}"</strong>.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#E63946', cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            }).then(r => { if (r.isConfirmed) document.getElementById('delete-subcategoria-' + id).submit(); });
        }
        @endcanEdit
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setupPagination({ tbodyId: 'categoriasBody',    rowsSelId: 'rowsCategoria',    infoId: 'categoriasInfo',    ctrlsId: 'categoriasControles',    emptyId: 'emptyCategorias',    label: 'categorías' });
            setupPagination({ tbodyId: 'subcategoriasBody', rowsSelId: 'rowsSubcategoria', infoId: 'subcategoriasInfo', ctrlsId: 'subcategoriasControles', emptyId: 'emptySubcategorias', label: 'subcategorías' });
        });

        function setupPagination({ tbodyId, rowsSelId, infoId, ctrlsId, emptyId, label }) {
            const tbody = document.getElementById(tbodyId);
            const rowsSel = document.getElementById(rowsSelId);
            const paginInfo = document.getElementById(infoId);
            const paginCtrls = document.getElementById(ctrlsId);
            if (!tbody) return;

            let currentPage = 1;

            function getAllRows() {
                return Array.from(tbody.querySelectorAll('tr')).filter(r => r.id !== emptyId);
            }

            function render() {
                const rows = getAllRows(), total = rows.length;
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
    </script>
@endsection