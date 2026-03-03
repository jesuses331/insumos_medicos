@extends('layouts.plantilla')

@section('titulo', isset($sucursal) ? 'Editar Sucursal' : 'Nueva Sucursal')
@section('encabezado', isset($sucursal) ? 'Editar Sucursal' : 'Nueva Sucursal')

@section('contenido')
    <div class="mb-3">
        <a href="{{ route('admin.sucursales.inicio') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver a la lista
        </a>
    </div>

    <div class="glass-card">
        <form
            action="{{ isset($sucursal) ? route('admin.sucursales.actualizar', $sucursal->id) : route('admin.sucursales.guardar') }}"
            method="POST">
            @csrf
            @if(isset($sucursal))
                @method('PUT')
            @endif

            <div class="row g-4">
                <!-- Nombre -->
                <div class="col-12">
                    <label class="form-label fw-medium">Nombre de la Sucursal <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $sucursal->nombre ?? '') }}" required
                        class="form-control @error('nombre') is-invalid @enderror"
                        placeholder="Ej. Sede Central Miraflores">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="col-12">
                    <label class="form-label fw-medium">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $sucursal->direccion ?? '') }}"
                        class="form-control @error('direccion') is-invalid @enderror"
                        placeholder="Av. Las Camelias 456, Lima">
                    @error('direccion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="col-md-6">
                    <label class="form-label fw-medium">Teléfono / WhatsApp</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $sucursal->telefono ?? '') }}"
                        class="form-control @error('telefono') is-invalid @enderror" placeholder="+51 987 654 321">
                    @error('telefono')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-premium">
                    <i class="fas fa-save me-2"></i>
                    {{ isset($sucursal) ? 'Guardar Cambios' : 'Crear Sucursal' }}
                </button>
            </div>
        </form>
    </div>

    <style>
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #dee2e6;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .form-label {
            color: #495057;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection