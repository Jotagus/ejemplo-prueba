@extends('layouts.app')

@section('page-title')<i class="bi bi-database-fill-gear text-warning me-2"></i>Gestión de Respaldos
@endsection

@section('styles')
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

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .btn-backup {
            background: linear-gradient(135deg, #FF6B35, #FFC107);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.7rem 1.5rem;
            font-size: 0.9rem;
            box-shadow: 0 4px 14px rgba(255, 107, 53, 0.35);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-backup:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.45);
            color: #fff;
        }

        .btn-backup:active {
            transform: translateY(0);
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

        .info-railway {
            background: rgba(255, 193, 7, 0.08);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.82rem;
            color: var(--text-dark);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Aviso Railway --}}
        <div class="info-railway">
            <i class="bi bi-info-circle-fill text-warning fs-5"></i>
            <span>Los respaldos se <strong>descargan directamente</strong> a tu equipo al generarlos. No se almacenan en el servidor.</span>
        </div>

        <div class="section-header">
            <div class="ms-auto">
                <a href="{{ route('backups.generate') }}" class="btn-backup" id="btnGenerar">
                    <i class="bi bi-cloud-arrow-down-fill"></i>
                    <span>Generar Nuevo Respaldo</span>
                </a>
            </div>
        </div>

        <div class="card-emdell shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-4">N°</th>
                            <th>Archivo</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Tamaño</th>
                            <th>Estado</th>
                            <th class="text-center pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="backupsBody">
                        @forelse($files as $i => $file)
                            @php
                                $partesFecha = explode(' ', $file['fecha']);
                                $fecha = $partesFecha[0] ?? '-';
                                $hora  = $partesFecha[1] ?? '-';
                            @endphp
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4 text-muted small">{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:34px;height:34px;border-radius:9px;background:rgba(255,193,7,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="bi bi-file-earmark-zip-fill text-warning"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold small">{{ $file['nombre'] }}</span>
                                            <br><span class="text-muted" style="font-size:0.7rem;">Respaldo de base de datos</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small fw-semibold">{{ $fecha }}</td>
                                <td class="small text-muted">{{ $hora }}</td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary fw-semibold">
                                        {{ $file['tamaño'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>Completo
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('backups.download', $file['nombre']) }}"
                                        class="btn btn-sm btn-outline-primary me-1" title="Descargar">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Eliminar"
                                        onclick="confirmarEliminar('{{ $file['nombre'] }}', '{{ route('backups.delete', $file['nombre']) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Los backups se descargan directamente a tu equipo.<br>Usa el botón de arriba para generar un respaldo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($files->count() > 0)
                <div class="pagination-wrapper">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rows-selector">
                            Mostrar
                            <select id="rowsBackups">
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            filas
                        </div>
                        <div class="pagination-info" id="backupsInfo"></div>
                    </div>
                    <div class="pagination-controls" id="backupsControles"></div>
                </div>
            @endif
        </div>

        <form id="formEliminar" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        function confirmarEliminar(nombre, url) {
            Swal.fire({
                title: '¿Eliminar respaldo?',
                html: `Se eliminará permanentemente <strong>${nombre}</strong>.<br><span style="font-size:0.8rem;opacity:0.6;">Esta acción no se puede deshacer.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="bi bi-trash me-1"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: 'var(--card-bg)',
                color: 'var(--text-dark)'
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.getElementById('formEliminar');
                    form.action = url;
                    form.submit();
                }
            });
        }

        function setupPagination({ tbodyId, rowsSelId, infoId, ctrlsId, label }) {
            const tbody   = document.getElementById(tbodyId);
            const rowsSel = document.getElementById(rowsSelId);
            const info    = document.getElementById(infoId);
            const ctrls   = document.getElementById(ctrlsId);
            if (!tbody || !rowsSel) return;

            let page = 1;

            function rows() { return Array.from(tbody.querySelectorAll('tr')); }

            function render() {
                const all = rows(), total = all.length;
                if (!total) { if (info) info.textContent = ''; if (ctrls) ctrls.innerHTML = ''; return; }
                const perPage = parseInt(rowsSel.value);
                const totalPages = Math.ceil(total / perPage);
                if (page > totalPages) page = totalPages;
                const start = (page - 1) * perPage, end = Math.min(start + perPage, total);
                all.forEach((r, i) => r.style.display = (i >= start && i < end) ? '' : 'none');
                if (info) info.textContent = `Mostrando ${start + 1}–${end} de ${total} ${label}`;
                buildCtrl(totalPages);
            }

            function buildCtrl(totalPages) {
                ctrls.innerHTML = '';
                ctrls.appendChild(mkBtn('‹', page === 1, () => { page--; render(); }));
                let s = Math.max(1, page - 2), e = Math.min(totalPages, s + 4);
                if (e - s < 4) s = Math.max(1, e - 4);
                if (s > 1) { ctrls.appendChild(mkBtn('1', false, () => { page = 1; render(); })); if (s > 2) ctrls.appendChild(mkDot()); }
                for (let p = s; p <= e; p++) { const pg = p, b = mkBtn(p, false, () => { page = pg; render(); }); if (p === page) b.classList.add('active'); ctrls.appendChild(b); }
                if (e < totalPages) { if (e < totalPages - 1) ctrls.appendChild(mkDot()); ctrls.appendChild(mkBtn(totalPages, false, () => { page = totalPages; render(); })); }
                ctrls.appendChild(mkBtn('›', page === totalPages, () => { page++; render(); }));
            }

            function mkBtn(lbl, disabled, onClick) {
                const b = document.createElement('button');
                b.className = 'page-btn'; b.textContent = lbl; b.disabled = disabled;
                if (!disabled) b.addEventListener('click', onClick);
                return b;
            }

            function mkDot() {
                const s = document.createElement('span');
                s.textContent = '…'; s.style.cssText = 'padding:0 4px;opacity:.4;font-size:.85rem;line-height:32px;';
                return s;
            }

            rowsSel.addEventListener('change', () => { page = 1; render(); });
            render();
        }

        document.addEventListener('DOMContentLoaded', function () {
            setupPagination({
                tbodyId:   'backupsBody',
                rowsSelId: 'rowsBackups',
                infoId:    'backupsInfo',
                ctrlsId:   'backupsControles',
                label:     'respaldos'
            });
        });
    </script>
@endsection