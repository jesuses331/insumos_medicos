@extends('layouts.plantilla')

@section('titulo', 'Crear Usuario')
@section('encabezado', 'Nuevo Usuario')

@section('contenido')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card">
                <div class="mb-4">
                    <a href="{{ route('admin.usuarios.inicio') }}" class="text-decoration-none text-muted small">
                        <i class="fas fa-arrow-left me-1"></i> Volver al listado
                    </a>
                </div>

                <form action="{{ route('admin.usuarios.guardar') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre"
                                class="form-control rounded-pill @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre') }}" required placeholder="Ej. Juan Pérez">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" name="correo"
                                class="form-control rounded-pill @error('correo') is-invalid @enderror"
                                value="{{ old('correo') }}" required placeholder="usuario@ejemplo.com">
                            @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contraseña</label>
                            <input type="password" name="contrasena"
                                class="form-control rounded-pill @error('contrasena') is-invalid @enderror" required
                                placeholder="Mínimo 8 caracteres">
                            @error('contrasena') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirmar Contraseña</label>
                            <input type="password" name="contrasena_confirmation" class="form-control rounded-pill" required
                                placeholder="Repite la contraseña">
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold">Asignar Roles</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($roles as $rol)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $rol->name }}"
                                            id="rol_{{ $rol->id }}">
                                        <label class="form-check-label" for="rol_{{ $rol->id }}">
                                            {{ $rol->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-premium px-5">
                                <i class="fas fa-save me-2"></i> Guardar Usuario
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection