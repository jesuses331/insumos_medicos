@section('titulo', 'Traslados')
@section('encabezado', 'Traslados de Inventario')

<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h1 class="h2 fw-bold text-white mb-1">Traslados de Inventario</h1>
            <p class="text-white-50 mb-0">Gestión de envíos y recepción entre sucursales</p>
        </div>
        <a href="{{ route('admin.traslados.crear') }}" class="btn btn-glass px-4 py-2 shadow-lg">
            <i class="fas fa-plus me-2"></i> Nuevo Traslado
        </a>
    </div>

    <div class="glass-card p-0 overflow-hidden border-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0 align-middle">
                <thead>
                    <tr class="text-white-50 small text-uppercase fw-bold" style="background: rgba(255,255,255,0.03);">
                        <th class="px-4 py-3 border-0">Fecha</th>
                        <th class="px-4 py-3 border-0">Origen</th>
                        <th class="px-4 py-3 border-0">Destino</th>
                        <th class="px-4 py-3 border-0">Estado</th>
                        <th class="px-4 py-3 border-0 text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-white-50">
                    @forelse($transfers as $transfer)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="px-4 py-4 small">{{ $transfer->fecha }}</td>
                            <td class="px-4 py-4 fw-bold" style="color: #ff9a9e;">
                                {{ $transfer->fromSucursal->nombre ?? 'Sucursal Eliminada' }}
                            </td>
                            <td class="px-4 py-4 fw-bold" style="color: #667eea;">
                                {{ $transfer->toSucursal->nombre ?? 'Sucursal Eliminada' }}
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge rounded-pill px-3 py-2 fw-bold text-uppercase"
                                    style="font-size: 0.65rem; 
                                                    @if($transfer->status == 'Enviado') background: rgba(102, 126, 234, 0.15); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.2);
                                                    @elseif($transfer->status == 'Recibido') background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);
                                                    @else background: rgba(255, 255, 255, 0.05); color: rgba(255,255,255,0.6); @endif">
                                    {{ $transfer->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-end">
                                @if($transfer->status == 'Enviado' && session('active_sucursal_id') == $transfer->to_branch_id)
                                    <button wire:click="updateStatus({{ $transfer->id }}, 'Recibido')"
                                        class="btn btn-sm btn-success rounded-pill px-4 fw-bold">
                                        RECOGER <i class="fas fa-arrow-down ms-1 small"></i>
                                    </button>
                                @endif
                                <button class="btn btn-link text-white-50 p-0 ms-2 opacity-50"><i
                                        class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-history text-white-10 h1 mb-3 d-block"></i>
                                <p class="mb-0">No hay traslados registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>