@extends('layouts.plantilla')

@section('titulo', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')
@section('encabezado', isset($cliente) ? 'Editar Cliente' : 'Nuevo Cliente')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="glass-card p-4">
            <form action="{{ isset($cliente) ? route('admin.clientes.actualizar', $cliente->id) : route('admin.clientes.guardar') }}" method="POST">
                @csrf
                @if(isset($cliente)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label text-white-50">Nombre completo</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $cliente->name ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Teléfono</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $cliente->phone ?? '') }}">
                </div>

                <div class="text-end">
                    <button class="btn btn-glass">{{ isset($cliente) ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
