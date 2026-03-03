@extends('layouts.plantilla')

@section('encabezado', 'Reporte de Productos Defectuosos')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="mb-5 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 fw-bold text-white mb-1">Mermas y Defectuosos</h1>
                <p class="text-white-50">Listado de equipos reportados con fallas para reposición</p>
            </div>
            <a href="{{ route('admin.reportes.pdf.defectuosos') }}" class="btn btn-danger px-4 py-2 shadow-sm fw-bold">
                <i class="fas fa-file-pdf me-2"></i> Exportar PDF
            </a>
        </div>

        <div class="glass-card p-0 overflow-hidden border-0 shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small text-uppercase fw-bold" style="background: rgba(255,255,255,0.03);">
                            <th class="px-4 py-3 border-0">Fecha</th>
                            <th class="px-4 py-3 border-0">Producto</th>
                            <th class="px-4 py-3 border-0">Sucursal</th>
                            <th class="px-4 py-3 border-0 text-center">Cant.</th>
                            <th class="px-4 py-3 border-0">Detalle</th>
                            <th class="px-4 py-3 border-0">Reportado por</th>
                            <th class="px-4 py-3 border-0">Estado</th>
                            <th class="px-4 py-3 border-0 text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="text-white-50">
                        @forelse($defectuosos as $item)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-4 small">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4 fw-bold text-white">
                                    {{ $item->product->nombre_comercial }}
                                    <div class="small text-white-50 fw-normal">{{ $item->product->nombre_generico }}</div>
                                </td>
                                <td class="px-4 py-4">{{ $item->sucursal->nombre }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span
                                        class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-3 py-2">
                                        {{ $item->cantidad }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 small">{{ $item->detalle }}</td>
                                <td class="px-4 py-4 opacity-75">{{ $item->user->name }}</td>
                                <td class="px-4 py-4">
                                    @if($item->estado == 'Pendiente')
                                        <span class="badge bg-warning text-dark px-3 py-2">Pendiente</span>
                                    @else
                                        <span class="badge bg-success text-white px-3 py-2">Repuesto</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-end">
                                    @if($item->estado == 'Pendiente')
                                        <form action="{{ route('admin.reportes.defectuoso.repuesto', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-glass px-3 py-2 shadow-sm"
                                                onclick="return confirm('¿Marcar como repuesto/solucionado?')">
                                                <i class="fas fa-check me-1"></i> Reponer
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-success"><i class="fas fa-check-double"></i> ok</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-clipboard-check text-white-10 display-4 mb-3 d-block"></i>
                                    <p class="mb-0 text-white-50">No hay reportes de productos defectuosos pendientes</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($defectuosos->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $defectuosos->links() }}
            </div>
        @endif
    </div>
@endsection