@extends('layouts.app')

@section('title', 'Subcategorías')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .card-emdell { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
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
</style>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: var(--text-dark);">
            <i class="bi bi-tags-fill text-warning me-2"></i>Subcategorías
        </h4>
        <button class="btn btn-warning text-white fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg"></i> Nueva Subcategoría
        </button>
    </div>

    <div class="card-emdell shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="small text-muted">
                        <th class="ps-4">ID</th>
                        <th>Categoría Padre</th>
                        <th>Subcategoría</th>
                        <th>Descripción</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subcategorias as $subcategoria)
                    <tr style="border-color: var(--border-color);">
                        <td class="ps-4 text-muted">#{{ str_pad($subcategoria->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border shadow-sm">
                                {{ $subcategoria->categoria->nombre }}
                            </span>
                        </td>
                        <td><span class="fw-bold">{{ $subcategoria->nombre }}</span></td>
                        <td class="small {{ $subcategoria->descripcion ? 'text-muted' : 'text-secondary opacity-50 italic' }}">
                            {{ $subcategoria->descripcion ?? 'Sin descripción' }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal{{ $subcategoria->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $subcategoria->id }}, '{{ $subcategoria->nombre }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="delete-form-{{ $subcategoria->id }}" action="{{ route('subcategorias.destroy', $subcategoria) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODALES DE EDICIÓN (FUERA DE LA TABLA) --}}
@foreach($subcategorias as $subcategoria)
<div class="modal fade" id="editModal{{ $subcategoria->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Editar Subcategoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(0.5);"></button>
            </div>
            <form action="{{ route('subcategorias.update', $subcategoria) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Categoría Padre</label>
                        <select name="categoria_id" class="form-select" required>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ $subcategoria->categoria_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $subcategoria->nombre }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Sin descripción">{{ $subcategoria->descripcion }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white fw-bold">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- MODAL CREAR --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nueva Subcategoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(0.5);"></button>
            </div>
            <form action="{{ route('subcategorias.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Categoría Padre</label>
                        <select name="categoria_id" class="form-select" required>
                            <option value="" disabled selected>Selecciona una categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Tornillos" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Sin descripción"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning text-white fw-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Alertas de éxito con SweetAlert2
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Operación Exitosa!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false,
            background: 'var(--card-bg)',
            color: 'var(--text-dark)',
            iconColor: '#FFC107'
        });
    @endif

    // Confirmación de eliminación
    function confirmDelete(id, nombre) {
        Swal.fire({
            title: '¿Eliminar subcategoría?',
            text: `La subcategoría "${nombre}" se borrará permanentemente.`,
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
</script>
@endsection