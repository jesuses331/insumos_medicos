@extends('layouts.plantilla')

@section('encabezado', 'Gestión de Sucursales')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bold text-white mb-1">Sucursales</h1>
                <p class="text-white-50 mb-0">Administra los puntos de venta y centros de distribución</p>
            </div>
            @can('crear-sucursales')
                <a href="{{ route('admin.sucursales.crear') }}" class="btn btn-glass px-4 py-2">
                    <i class="fas fa-plus me-2"></i> Nueva Sucursal
                </a>
            @endcan
        </div>

        <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small text-uppercase fw-bold" style="background: rgba(255,255,255,0.03);">
                            <th class="px-4 py-3 border-0">Sucursal</th>
                            <th class="px-4 py-3 border-0">Ubicación</th>
                            <th class="px-4 py-3 border-0">Contacto</th>
                            <th class="px-4 py-3 border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-white-50">
                        @foreach($sucursales as $sucursal)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px; background: var(--primary-gradient); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                                            <i class="fas fa-store text-white"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-white">{{ $sucursal->nombre }}</div>
                                            <div class="small opacity-50">Sede Activa</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <i class="fas fa-map-marker-alt me-2 text-primary opacity-50"></i>
                                    {{ $sucursal->direccion ?? 'Sin dirección registrada' }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold"
                                        style="background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.8); border: 1px solid rgba(255,255,255,0.1);">
                                        <i class="fas fa-phone-alt me-2 small opacity-50"></i>{{ $sucursal->telefono ?? '---' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @can('editar-sucursales')
                                            <a href="{{ route('admin.sucursales.editar', $sucursal->id) }}"
                                                class="btn btn-sm btn-outline-light rounded-circle p-2 opacity-50 hover-opacity-100"
                                                style="width: 34px; height: 34px;">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                        @endcan
                                        @can('borrar-sucursales')
                                            <form action="{{ route('admin.sucursales.eliminar', $sucursal->id) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar esta sucursal?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-circle p-2 opacity-50 hover-opacity-100"
                                                    style="width: 34px; height: 34px;">
                                                    <i class="fas fa-trash small"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($sucursales->hasPages())
            <div class="mt-4 d-flex justify-content-center pagination-premium">
                {{ $sucursales->links() }}
            </div>
        @endif
    </div>
@endsection