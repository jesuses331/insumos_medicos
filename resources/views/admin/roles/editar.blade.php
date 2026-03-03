@extends('layouts.plantilla')

@section('titulo', 'Editar Rol')
@section('encabezado', 'Editar Rol: ' . $rol->name)

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="glass-card">
                <div class="mb-4">
                    <a href="{{ route('admin.roles.inicio') }}" class="text-decoration-none text-muted small">
                        <i class="fas fa-arrow-left me-1"></i> Volver al listado
                    </a>
                </div>

                <form action="{{ route('admin.roles.actualizar', $rol->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nombre del Rol</label>
                            <input type="text" name="nombre"
                                class="form-control rounded-pill @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $rol->name) }}" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold mb-3 d-block">Modificar Permisos del Rol</label>
                            <div class="row g-3">
                                @foreach($permisos as $permiso)
                                    <div class="col-md-3">
                                        <div
                                            class="form-check card-checkbox p-3 border rounded shadow-sm {{ $rol->hasPermissionTo($permiso->name) ? 'bg-soft-blue border-primary' : '' }}">
                                            <input class="form-check-input" type="checkbox" name="permisos[]"
                                                value="{{ $permiso->name }}" id="permiso_{{ $permiso->id }}" {{ $rol->hasPermissionTo($permiso->name) ? 'checked' : '' }}>
                                            <label class="form-check-label ms-2" for="permiso_{{ $permiso->id }}">
                                                {{ $permiso->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 text-end mt-5">
                            <button type="submit" class="btn btn-premium px-5">
                                <i class="fas fa-sync me-2"></i> Actualizar Rol
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .card-checkbox {
            transition: all 0.2s;
            cursor: pointer;
        }

        .card-checkbox:hover {
            background-color: #f8f9fa;
            border-color: #667eea;
        }

        .bg-soft-blue {
            background-color: #ebf4ff;
        }
    </style>
@endsection