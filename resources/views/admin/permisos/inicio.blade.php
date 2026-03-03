@extends('layouts.plantilla')

@section('titulo', 'Permisos')
@section('encabezado', 'Gestión de Permisos')

@section('contenido')
    <div class="row">
        <!-- Formulario Crear Permiso -->
        <div class="col-md-4">
            <div class="glass-card mb-4">
                <h5 class="fw-bold mb-4">Nuevo Permiso</h5>
                <form action="{{ route('admin.permisos.guardar') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del Permiso</label>
                        <input type="text" name="nombre" class="form-control rounded-pill" required
                            placeholder="Ej. crear-reportes">
                    </div>
                    <button type="submit" class="btn btn-premium w-100">
                        <i class="fas fa-plus me-2"></i> Crear Permiso
                    </button>
                </form>
            </div>
        </div>

        <!-- Listado de Permisos -->
        <div class="col-md-8">
            <div class="glass-card">
                <h5 class="fw-bold mb-4">Permisos del Sistema</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permisos as $permiso)
                                <tr>
                                    <td><code>{{ $permiso->name }}</code></td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.permisos.eliminar', $permiso->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este permiso?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $permisos->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
