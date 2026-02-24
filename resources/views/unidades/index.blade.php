@extends('layouts.app')

@section('title', 'Unidades de Medida')

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

        .form-control {
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
    </style>
@endsection

@section('content')
    <div class="container">
        {{-- ========================================
            ENCABEZADO
        ======================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--text-dark);">
                <i class="bi bi-rulers text-warning me-2"></i>Unidades de Medida
            </h4>
            <button class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal"
                data-bs-target="#createModal">
                <i class="bi bi-plus-lg"></i> Nueva Unidad
            </button>
        </div>

        {{-- ========================================
            TABLA DE UNIDADES
        ======================================== --}}
        <div class="card-emdell shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th class="ps-4">ID</th>
                            <th>Nombre</th>
                            <th>Abreviatura</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidad)
                            <tr style="border-color: var(--border-color);">
                                <td class="ps-4">
                                    <span class="badge bg-secondary">#{{ str_pad($unidad->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $unidad->nombre }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $unidad->abreviatura }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $unidad->id }}" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete({{ $unidad->id }}, '{{ $unidad->nombre }}')" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $unidad->id }}"
                                        action="{{ route('unidades.destroy', $unidad) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No hay unidades de medida registradas</p>
                                    <button class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal"
                                        data-bs-target="#createModal">
                                        <i class="bi bi-plus-lg"></i> Crear primera unidad
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================
        MODAL: CREAR UNIDAD
    ======================================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-rulers me-2"></i>Nueva Unidad de Medida
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('unidades.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                placeholder="Ej. Kilogramo, Metro, Litro" value="{{ old('nombre') }}" required autofocus>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Abreviatura <span class="text-danger">*</span></label>
                            <input type="text" name="abreviatura"
                                class="form-control @error('abreviatura') is-invalid @enderror" placeholder="Ej. kg, m, L"
                                value="{{ old('abreviatura') }}" maxlength="10" required>
                            <small class="text-muted">Máximo 10 caracteres</small>
                            @error('abreviatura')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

    {{-- ========================================
        MODALES: EDITAR UNIDADES
    ======================================== --}}
    @foreach($unidades as $unidad)
        <div class="modal fade" id="editModal{{ $unidad->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pencil-fill text-warning me-2"></i>Editar Unidad de Medida
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            style="filter: invert(0.5);"></button>
                    </div>
                    <form action="{{ route('unidades.update', $unidad) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control" value="{{ $unidad->nombre }}"
                                    required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Abreviatura <span class="text-danger">*</span></label>
                                <input type="text" name="abreviatura" class="form-control"
                                    value="{{ $unidad->abreviatura }}" maxlength="10" required>
                                <small class="text-muted">Máximo 10 caracteres</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning text-white fw-bold">
                                <i class="bi bi-check-lg"></i> Actualizar
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
        {{-- ========================================
            ALERTAS DE ÉXITO
        ======================================== --}}
        @if (session('success'))
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

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                background: 'var(--card-bg)',
                color: 'var(--text-dark)',
                confirmButtonColor: '#E63946'
            });
        @endif

        {{-- ========================================
            CONFIRMACIÓN DE ELIMINACIÓN - CORREGIDA
        ======================================== --}}
        function confirmDelete(id, nombre) {
            Swal.fire({
                title: '¿Eliminar unidad?',
                html: `Se eliminará la unidad <strong>"${nombre}"</strong>.<br>Esta acción no se puede deshacer.`,
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
                    // Buscar el formulario
                    const form = document.getElementById('delete-form-' + id);
                    
                    if (form) {
                        console.log('Enviando formulario de eliminación para ID:', id); // Para debug
                        form.submit();
                    } else {
                        console.error('No se encontró el formulario con ID: delete-form-' + id);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo encontrar el formulario de eliminación',
                            background: 'var(--card-bg)',
                            color: 'var(--text-dark)'
                        });
                    }
                }
            });
        }
    </script>
@endsection