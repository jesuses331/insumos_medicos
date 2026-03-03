@extends('layouts.plantilla')

@section('encabezado', 'Resumen de Existencias')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="mb-5">
            <h1 class="h2 fw-bold text-white mb-1">Inventario Global</h1>
            <p class="text-white-50">Distribución consolidada de stock en todas las sucursales</p>
        </div>

        <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small text-uppercase fw-bold text-center"
                            style="background: rgba(255,255,255,0.03);">
                            <th class="px-4 py-3 border-0 text-start">Producto (Genérico / Comercial)</th>
                            @foreach($sucursales as $sucursal)
                                <th class="px-4 py-3 border-0">{{ $sucursal->nombre }}</th>
                            @endforeach
                            <th class="px-4 py-3 border-0 bg-white-5">Stock Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-white-50">
                        @forelse($productos as $producto)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-4 text-start">
                                    <div class="fw-bold text-white mb-1">
                                        @if($producto->nombre_generico)
                                            <span class="text-white-50 small text-uppercase d-block"
                                                style="font-size: 0.6rem;">{{ $producto->nombre_generico }}</span>
                                        @endif
                                        {{ $producto->nombre_comercial }}
                                    </div>
                                    <div class="small opacity-50 text-uppercase" style="font-size: 0.6rem;">
                                        {{ $producto->categoria->nombre }}
                                    </div>
                                </td>
                                @php $totalGlobal = 0; @endphp
                                @foreach($sucursales as $sucursal)
                                    @php
                                        $stock = $producto->sucursales->where('id', $sucursal->id)->first()->pivot->stock ?? 0;
                                        $totalGlobal += $stock;
                                    @endphp
                                    <td class="px-4 py-4 text-center">
                                        @php
                                            $bgColor = $stock <= 2 ? 'rgba(239, 68, 68, 0.15)' : ($stock <= 5 ? 'rgba(245, 158, 11, 0.15)' : 'rgba(255,255,255,0.03)');
                                            $textColor = $stock <= 2 ? '#ef4444' : ($stock <= 5 ? '#f59e0b' : 'rgba(255,255,255,0.8)');
                                            $borderColor = $stock <= 2 ? 'rgba(239, 68, 68, 0.3)' : ($stock <= 5 ? 'rgba(245, 158, 11, 0.3)' : 'rgba(255,255,255,0.05)');
                                        @endphp
                                        <span class="badge rounded-pill px-3 py-2 fw-bold"
                                            style="font-size: 0.8rem; background: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $borderColor }};">
                                            {{ $stock }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="px-4 py-4 text-center bg-white-5">
                                    <span class="h5 fw-bold mb-0 text-primary">{{ $totalGlobal }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($sucursales) + 2 }}" class="text-center py-5">
                                    <i class="fas fa-layer-group text-white-10 display-3 mb-3 d-block"></i>
                                    <p class="text-white-50">No hay datos disponibles para mostrar</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .bg-white-5 {
            background: rgba(255, 255, 255, 0.05);
        }
    </style>
@endsection