@extends('layouts.app')

@section('page-title')<i class="bi bi-people-fill text-warning me-2"></i>Gestión de Usuarios
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

        /* ── AVATAR ── */
        .avatar-ini {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            flex-shrink: 0;
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
        <div class="input-group shadow-sm" style="max-width:420px; flex-grow:1;">
            <span class="input-group-text bg-body-secondary border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="tablaBuscador" class="form-control border-start-0"
                placeholder="Buscar por nombre, correo, rol..." autocomplete="off">
            <button class="btn btn-outline-secondary border-start-0" id="btnLimpiar"
                title="Limpiar" style="display:none;">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <button class="btn btn-warning text-white fw-bold shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
        </button>
    </div>

    {{-- TABLA --}}
    <div class="card-emdell shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="small text-muted">
                        <th class="ps-4">Usuario</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuariosBody">
                    @forelse($usuarios as $usuario)
                        <tr style="border-color: var(--border-color);"
                            @if($usuario->id === session('usuario_id')) class="table-warning bg-opacity-10" @endif>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Avatar con iniciales --}}
                                    <div class="avatar-ini"
                                        style="background:rgba(255,193,7,0.15); color:#FFC107;">
                                        {{ $usuario->iniciales }}
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ $usuario->nombre }}</span>
                                        @if($usuario->id === session('usuario_id'))
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">Tú</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="small text-muted">{{ $usuario->email }}</td>
                            <td>
                                @php
                                    $rolColor = match($usuario->rol->nombre) {
                                        'admin'       => 'danger',
                                        'supervisor'  => 'primary',
                                        'almacenero'  => 'success',
                                        default       => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $rolColor }}-subtle text-{{ $rolColor }} border border-{{ $rolColor }}-subtle px-3 py-2">
                                    <i class="bi bi-shield-fill me-1"></i>{{ ucfirst($usuario->rol->nombre) }}
                                </span>
                            </td>
                            <td>
                                @if($usuario->estado)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        <i class="bi bi-x-circle-fill me-1"></i>Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                {{ $usuario->ultimo_acceso ? $usuario->ultimo_acceso->diffForHumans() : 'Nunca' }}
                            </td>
                            <td class="text-center pe-4">
                                {{-- Editar --}}
                                <button class="btn btn-sm btn-outline-warning me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $usuario->id }}"
                                    title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Activar / Desactivar --}}
                                @if($usuario->id !== session('usuario_id'))
                                    <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-sm {{ $usuario->estado ? 'btn-outline-secondary' : 'btn-outline-success' }} me-1"
                                            title="{{ $usuario->estado ? 'Desactivar' : 'Activar' }}">
                                            <i class="bi {{ $usuario->estado ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Eliminar --}}
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $usuario->id }}, '{{ $usuario->nombre }}')"
                                        title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-{{ $usuario->id }}"
                                        action="{{ route('usuarios.destroy', $usuario) }}" method="POST"
                                        style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr id="emptyRow">
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-people fs-1 d-block mb-2"></i>
                                <p class="mb-0">No hay usuarios registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="pagination-wrapper">
            <div class="d-flex align-items-center gap-3">
                <div class="rows-selector">
                    Mostrar
                    <select id="rowsPerPage">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                    </select>
                    filas
                </div>
                <div class="pagination-info" id="paginacionInfo"></div>
            </div>
            <div class="pagination-controls" id="paginacionControles"></div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════
    MODAL: CREAR USUARIO
═══════════════════════════════════ --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-person-plus-fill text-warning fs-5"></i>
                    </div>
                    <span>Nuevo Usuario</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                placeholder="Ej. Juan Pérez" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-muted">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                placeholder="correo@emdell.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Mín. 6 caracteres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Repite la contraseña" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Rol <span class="text-danger">*</span></label>
                            <select name="rol_id" class="form-select" required>
                                <option value="" disabled selected>Seleccionar...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select" required>
                                <option value="1" selected>🟢 Activo</option>
                                <option value="0">🔴 Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                        data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                        <i class="bi bi-save-fill me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════
    MODALES: EDITAR USUARIOS
═══════════════════════════════════ --}}
@foreach($usuarios as $usuario)
    <div class="modal fade" id="editModal{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-body-tertiary border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-pencil-fill text-warning fs-5"></i>
                        </div>
                        <span>Editar — {{ $usuario->nombre }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control"
                                    value="{{ $usuario->nombre }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ $usuario->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Nueva contraseña</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Dejar vacío para no cambiar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Repite la nueva contraseña">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Rol <span class="text-danger">*</span></label>
                                <select name="rol_id" class="form-select" required>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}"
                                            {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                            {{ ucfirst($rol->nombre) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="form-select" required>
                                    <option value="1" {{ $usuario->estado ? 'selected' : '' }}>🟢 Activo</option>
                                    <option value="0" {{ !$usuario->estado ? 'selected' : '' }}>🔴 Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-body-tertiary border-top-0 p-3">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                            <i class="bi bi-save-fill me-2"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@section('scripts')
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success', title: '¡Listo!',
                text: "{{ session('success') }}", timer: 2500, showConfirmButton: false,
                background: 'var(--card-bg)', color: 'var(--text-dark)', iconColor: '#FFC107'
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error', title: 'Error',
                text: "{{ session('error') }}",
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            });
        @endif

        function confirmDelete(id, nombre) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                html: `Se eliminará permanentemente a <strong>${nombre}</strong>.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#EF4444', cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)', color: 'var(--text-dark)'
            }).then(r => { if (r.isConfirmed) document.getElementById('delete-' + id).submit(); });
        }
    </script>

    {{-- BUSCADOR + PAGINACIÓN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input      = document.getElementById('tablaBuscador');
            const btnClear   = document.getElementById('btnLimpiar');
            const tbody      = document.getElementById('usuariosBody');
            const rowsSel    = document.getElementById('rowsPerPage');
            const paginInfo  = document.getElementById('paginacionInfo');
            const paginCtrls = document.getElementById('paginacionControles');

            let currentPage  = 1;
            let filteredRows = [];

            const noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.style.display = 'none';
            noResultRow.innerHTML = `<td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-search fs-1 d-block mb-2"></i>
                No se encontraron usuarios para "<span id="busquedaTexto"></span>"</td>`;
            tbody.appendChild(noResultRow);

            function getAllRows() {
                return Array.from(tbody.querySelectorAll('tr'))
                    .filter(r => r.id !== 'noResultRow' && r.id !== 'emptyRow');
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
                paginInfo.textContent = `Mostrando ${start + 1}–${end} de ${total} usuarios`;
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