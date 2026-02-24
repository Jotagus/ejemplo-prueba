@extends('layouts.app')

@section('page-title')<i class="bi bi-activity text-warning me-2"></i>Control de Actividades
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

        .filter-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select {
            background-color: var(--card-bg) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }

        .badge-crear {
            background: rgba(16, 185, 129, 0.12);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-editar {
            background: rgba(59, 130, 246, 0.12);
            color: #3B82F6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge-eliminar {
            background: rgba(239, 68, 68, 0.12);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-generar {
            background: rgba(255, 193, 7, 0.12);
            color: #D97706;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .badge-descargar {
            background: rgba(139, 92, 246, 0.12);
            color: #8B5CF6;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .badge-accion {
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.22rem 0.6rem;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge-modulo {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            background: rgba(255, 107, 53, 0.08);
            color: #FF6B35;
            border: 1px solid rgba(255, 107, 53, 0.2);
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
            text-decoration: none;
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

        .avatar-user {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
            background: rgba(255, 193, 7, 0.15);
            color: #D97706;
        }

        .descripcion-text {
            font-size: 0.8rem;
            color: var(--text-dark);
            opacity: 0.75;
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            cursor: pointer;
        }

        .descripcion-text:hover {
            opacity: 1;
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-dark);
            opacity: 0.4;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .empty-state p {
            font-size: 0.9rem;
            margin: 0;
        }

        .detalle-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .detalle-item:last-child {
            border-bottom: none;
        }

        .detalle-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--text-dark);
            opacity: 0.45;
            margin-bottom: 0.2rem;
        }

        .detalle-value {
            font-size: 0.88rem;
            color: var(--text-dark);
            font-weight: 500;
            word-break: break-word;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        {{-- ── ENCABEZADO ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);"></h4>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-secondary bg-opacity-25 text-secondary fw-semibold px-3 py-2">
                    <i class="bi bi-list-ul me-1"></i>{{ $registros->total() }} registros
                </span>
                <button class="btn btn-outline-danger btn-sm fw-semibold" data-bs-toggle="modal"
                    data-bs-target="#modalLimpiar">
                    <i class="bi bi-trash2 me-1"></i> Limpiar registros antiguos
                </button>
            </div>
        </div>
        {{-- ── FILTROS DE BÚSQUEDA ── --}}
        <div class="filter-card shadow-sm">
            <form method="GET" action="{{ route('auditoria.index') }}" id="filtrosForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Módulo</label>
                    <select name="modulo" class="form-select">
                        <option value="">Todos los módulos</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                                {{ $modulo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Acción</label>
                    <select name="accion" class="form-select">
                        <option value="">Todas</option>
                        @foreach($acciones as $accion)
                            <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                                {{ $accion }}
                            </option>
                        @endforeach
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
                <input type="hidden" name="per_page" id="perPageHidden" value="{{ request('per_page', 10) }}">
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-warning text-white fw-bold">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card-emdell shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-4">N°</th>
                            <th>Usuario</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>IP</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registros as $registro)
                            @php
                                $partes = explode(' ', $registro->fecha ?? '');
                                $fecha = $partes[0] ?? '-';
                                $hora = $partes[1] ?? '-';

                                $accionLower = strtolower($registro->accion);
                                $badgeClass = match ($accionLower) {
                                    'crear' => 'badge-crear',
                                    'editar' => 'badge-editar',
                                    'eliminar' => 'badge-eliminar',
                                    'generar' => 'badge-generar',
                                    'descargar' => 'badge-descargar',
                                    default => 'badge-generar',
                                };
                                $iconAccion = match ($accionLower) {
                                    'crear' => 'bi-plus-circle-fill',
                                    'editar' => 'bi-pencil-fill',
                                    'eliminar' => 'bi-trash-fill',
                                    'generar' => 'bi-gear-fill',
                                    'descargar' => 'bi-download',
                                    default => 'bi-activity',
                                };

                                $iniciales = 'S';
                                if ($registro->usuario) {
                                    $parteNombre = explode(' ', trim($registro->usuario->nombre));
                                    $iniciales = strtoupper(substr($parteNombre[0], 0, 1));
                                    if (count($parteNombre) > 1)
                                        $iniciales .= strtoupper(substr($parteNombre[1], 0, 1));
                                }
                            @endphp
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4 small text-muted">{{ $registros->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-user">{{ $iniciales }}</div>
                                        <div>
                                            <span class="fw-bold small">{{ $registro->usuario->nombre ?? 'Sistema' }}</span>
                                            @if($registro->usuario)
                                                <br><small class="text-muted"
                                                    style="font-size:0.7rem;">{{ $registro->usuario->rol->nombre ?? '' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-modulo">{{ $registro->modulo }}</span></td>
                                <td>
                                    <span class="badge-accion {{ $badgeClass }}">
                                        <i class="bi {{ $iconAccion }}"></i>{{ $registro->accion }}
                                    </span>
                                </td>
                                <td>
                                    <span class="descripcion-text" onclick="verDetalle(
                                                                    '{{ $registro->usuario->nombre ?? 'Sistema' }}',
                                                                    '{{ $registro->modulo }}',
                                                                    '{{ $registro->accion }}',
                                                                    '{{ addslashes($registro->descripcion) }}',
                                                                    '{{ $registro->ip ?? '-' }}',
                                                                    '{{ $fecha }}',
                                                                    '{{ $hora }}'
                                                                )" title="Click para ver detalle completo">
                                        {{ $registro->descripcion }}
                                    </span>
                                </td>
                                <td class="small text-muted font-monospace">{{ $registro->ip ?? '-' }}</td>
                                <td class="small fw-semibold">{{ $fecha }}</td>
                                <td class="small text-muted">{{ $hora }}</td>
                                <td class="text-center">
                                    {{-- Ver detalle --}}
                                    <button class="btn btn-sm btn-outline-info me-1" onclick="verDetalle(
                                                                    '{{ $registro->usuario->nombre ?? 'Sistema' }}',
                                                                    '{{ $registro->modulo }}',
                                                                    '{{ $registro->accion }}',
                                                                    '{{ addslashes($registro->descripcion) }}',
                                                                    '{{ $registro->ip ?? '-' }}',
                                                                    '{{ $fecha }}',
                                                                    '{{ $hora }}'
                                                                )" title="Ver detalle">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-activity"></i>
                                        <p>Las acciones del sistema aparecerán aquí
                                            automáticamente.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($registros->total() > 0)
                <div class="pagination-wrapper">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rows-selector">
                            Mostrar
                            <select id="rowsPerPage">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            filas
                        </div>
                        <div class="pagination-info">
                            Mostrando {{ $registros->firstItem() }}–{{ $registros->lastItem() }}
                            de {{ $registros->total() }} registros
                        </div>
                    </div>

                    <div class="pagination-controls">
                        @if($registros->onFirstPage())
                            <button class="page-btn" disabled>‹</button>
                        @else
                            <a href="{{ $registros->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                                class="page-btn">‹</a>
                        @endif

                        @php
                            $currentPage = $registros->currentPage();
                            $lastPage = $registros->lastPage();
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $start + 4);
                            if ($end - $start < 4)
                                $start = max(1, $end - 4);
                        @endphp

                        @if($start > 1)
                            <a href="{{ $registros->url(1) }}&per_page={{ request('per_page', 10) }}" class="page-btn">1</a>
                            @if($start > 2)<span
                            style="padding:0 4px;opacity:0.4;font-size:0.85rem;line-height:32px;">…</span>@endif
                        @endif

                        @for($p = $start; $p <= $end; $p++)
                            <a href="{{ $registros->url($p) }}&per_page={{ request('per_page', 10) }}"
                                class="page-btn {{ $p === $currentPage ? 'active' : '' }}">{{ $p }}</a>
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)<span
                            style="padding:0 4px;opacity:0.4;font-size:0.85rem;line-height:32px;">…</span>@endif
                            <a href="{{ $registros->url($lastPage) }}&per_page={{ request('per_page', 10) }}"
                                class="page-btn">{{ $lastPage }}</a>
                        @endif

                        @if($registros->hasMorePages())
                            <a href="{{ $registros->nextPageUrl() }}&per_page={{ request('per_page', 10) }}" class="page-btn">›</a>
                        @else
                            <button class="page-btn" disabled>›</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- MODAL VER DETALLE --}}
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-journal-text text-info fs-5"></i>
                        </div>
                        <span>Detalle del Registro</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="detalle-item">
                        <div class="detalle-label">Usuario</div>
                        <div class="detalle-value" id="det-usuario">—</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-label">Módulo</div>
                        <div class="detalle-value" id="det-modulo">—</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-label">Acción</div>
                        <div class="detalle-value" id="det-accion">—</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-label">Descripción completa</div>
                        <div class="detalle-value" id="det-descripcion">—</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-label">Dirección IP</div>
                        <div class="detalle-value font-monospace" id="det-ip">—</div>
                    </div>
                    <div class="detalle-item">
                        <div class="detalle-label">Fecha y Hora</div>
                        <div class="detalle-value" id="det-fecha">—</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL LIMPIAR REGISTROS ANTIGUOS --}}
    <div class="modal fade" id="modalLimpiar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3">
                            <i class="bi bi-trash2-fill text-danger fs-5"></i>
                        </div>
                        <span>Limpiar Registros Antiguos</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('auditoria.limpiar') }}" method="POST" id="formLimpiar">
                    @csrf
                    <div class="modal-body px-4 pb-2">
                        <p class="text-muted small mb-3">
                            Se eliminarán permanentemente todos los registros anteriores al período seleccionado.
                            <strong>Esta acción no se puede deshacer.</strong>
                        </p>
                        <label class="form-label small fw-semibold">Eliminar registros anteriores a:</label>
                        <select name="dias" class="form-select">
                            <option value="0">⚠️ Todos los registros</option>
                            <option value="30">30 días</option>
                            <option value="60">60 días</option>
                            <option value="90" selected>90 días</option>
                            <option value="180">180 días (6 meses)</option>
                        </select>
                        <div class="mt-3 p-3 rounded-3"
                            style="background: rgba(239,68,68,0.07); border: 1px solid rgba(239,68,68,0.2);">
                            <small class="text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Los registros eliminados no podrán recuperarse.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-3">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none px-4"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger px-4 fw-bold" onclick="confirmarLimpiar()">
                            <i class="bi bi-trash2-fill me-1"></i> Limpiar registros
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
            // ── VER DETALLE ──
            function verDetalle(usuario, modulo, accion, descripcion, ip, fecha, hora) {
                document.getElementById('det-usuario').textContent = usuario;
                document.getElementById('det-modulo').textContent = modulo;
                document.getElementById('det-accion').textContent = accion;
                document.getElementById('det-descripcion').textContent = descripcion;
                document.getElementById('det-ip').textContent = ip;
                document.getElementById('det-fecha').textContent = fecha + ' ' + hora;
                new bootstrap.Modal(document.getElementById('modalDetalle')).show();
            }
        // ── CONFIRMAR LIMPIAR ──
        function confirmarLimpiar() {
            const dias = document.querySelector('#formLimpiar select[name="dias"]').value;
            Swal.fire({
                title: '¿Limpiar registros?',
                html: `Se eliminarán permanentemente todos los registros <strong>anteriores a ${dias} días</strong>.<br><br>Esta acción <strong>no se puede deshacer</strong>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="bi bi-trash2-fill me-1"></i> Sí, limpiar',
                cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)',
                color: 'var(--text-dark)'
            }).then(result => {
                if (result.isConfirmed) {
                    document.getElementById('formLimpiar').submit();
                }
            });
        }
        // ── CAMBIO DE FILAS ──
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