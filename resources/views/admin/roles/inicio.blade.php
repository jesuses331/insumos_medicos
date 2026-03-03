@extends('layouts.plantilla')

@section('titulo', 'Roles')
@section('encabezado', 'Gestión de Roles')

@section('contenido')
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold m-0"><i class="fas fa-user-tag me-2"></i> Listado de Roles</h5>
            <a href="{{ route('admin.roles.crear') }}" class="btn btn-premium">
                <i class="fas fa-plus me-2"></i> Nuevo Rol
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre del Rol</th>
                        <th>Permisos Asignados</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $rol)
                        <tr>
                            <td class="fw-bold text-primary">{{ $rol->name }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($rol->permissions as $permiso)
                                        <span class="badge bg-light text-dark border rounded-pill">{{ $permiso->name }}</span>
                                    @endforeach
                                    @if($rol->permissions->count() == 0)
                                        <span class="text-muted small italic">Sin permisos</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end">
                                @if($rol->name !== 'super-admin')
                                    <div class="btn-group">
                                        <a href="{{ route('admin.roles.editar', $rol->id) }}"
                                            class="btn btn-sm btn-light text-primary me-2 rounded">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.roles.eliminar', $rol->id) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">Protegido</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
@endsection