@extends('layouts.plantilla')

@section('encabezado', 'Editar Producto')

@section('contenido')
    <div class="container-fluid p-0" style="max-width: 900px;">
        <div class="mb-5">
            <h1 class="h2 fw-bold text-white mb-1">Modificar Información</h1>
            <p class="text-white-50">Actualiza los detalles técnicos y ajusta el stock por sede</p>
        </div>

        <form action="{{ route('admin.productos.actualizar', $producto->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="glass-card p-4 p-md-5 mb-4">
                <h5 class="text-primary fw-bold mb-4 d-flex align-items-center">
                    <i class="fas fa-edit me-2 opacity-50"></i> Datos del Producto
                </h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Nombre Genérico</label>
                        <input type="text" name="nombre_generico" value="{{ $producto->nombre_generico }}" placeholder="Ej: Paracetamol, Ibuprofeno" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Nombre Comercial</label>
                        <input type="text" name="nombre_comercial" value="{{ $producto->nombre_comercial }}" placeholder="Ej: Panadol, Advil" class="form-control"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Unidad de Medida</label>
                        <select name="unidad_medida" class="form-select" required>
                            @foreach($unidades as $unidad)
                                <option value="{{ $unidad }}" {{ $producto->unidad_medida == $unidad ? 'selected' : '' }}>
                                    {{ $unidad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Categoría</label>
                        <select name="categoria_id" class="form-select" required>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-5 border-white-10">

                <h5 class="text-primary fw-bold mb-4 d-flex align-items-center">
                    <i class="fas fa-tag me-2 opacity-50"></i> Ajustes de Precio (Bs)
                </h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Costo Actual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white-5 border-white-10 text-white-50">Bs</span>
                            <input type="number" step="0.01" name="costo" value="{{ $producto->costo }}"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Precio de Venta</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white-5 border-white-10 text-white-50">Bs</span>
                            <input type="number" step="0.01" name="precio_venta" value="{{ $producto->precio_venta }}"
                                class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 p-md-5 mb-5">
                <h5 class="text-primary fw-bold mb-4 d-flex align-items-center">
                    <i class="fas fa-boxes me-2 opacity-50"></i> Ajuste de Stock
                </h5>
                <p class="small text-white-50 mb-4">Modifica manualmente el inventario existente en cada sucursal.</p>

                <div class="row g-4">
                    @foreach($sucursales as $sucursal)
                        @php
                            $stockSucursal = $producto->sucursales->where('id', $sucursal->id)->first()->pivot->stock ?? 0;
                        @endphp
                        <div class="col-md-4">
                            <label class="form-label text-white-50 small fw-bold text-uppercase">{{ $sucursal->nombre }}</label>
                            <input type="number" name="stock[{{ $sucursal->id }}]" value="{{ $stockSucursal }}" min="0"
                                class="form-control text-center fw-bold">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mb-5">
                <a href="{{ route('admin.productos.inicio') }}"
                    class="btn btn-outline-light px-4 py-2 border-white-10 text-white-50 rounded-pill">
                    Volver
                </a>
                <button type="submit" class="btn btn-glass px-5 py-2 shadow-lg">
                    <i class="fas fa-sync-alt me-2"></i> Actualizar Información
                </button>
            </div>
        </form>
    </div>

    <style>
        .border-white-10 {
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .bg-white-5 {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
@endsection