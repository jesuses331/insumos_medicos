@section('titulo', 'Ventas')
@section('encabezado', 'Historial de Ventas')

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div class="d-flex flex-grow-1 gap-3 flex-wrap">
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white-5 border-white-10 text-white-50"><i
                                    class="fas fa-search"></i></span>
                            <input wire:model.live="search" type="text" placeholder="Cliente o folio..."
                                class="form-control bg-white-5 border-white-10 text-white">
                        </div>
                    </div>
                    <div style="min-width: 150px;">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Fecha Inicio</label>
                        <input wire:model.live="fecha_inicio" type="date"
                            class="form-control bg-white-5 border-white-10 text-white">
                    </div>
                    <div style="min-width: 150px;">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Fecha Fin</label>
                        <input wire:model.live="fecha_fin" type="date"
                            class="form-control bg-white-5 border-white-10 text-white">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-white-50 small text-uppercase">
                        <tr>
                            <th class="border-white-10 text-nowrap">Folio</th>
                            <th class="border-white-10 d-none d-md-table-cell">Fecha</th>
                            <th class="border-white-10">Cliente</th>
                            <th class="border-white-10 d-none d-lg-table-cell">Usuario</th>
                            <th class="border-white-10 d-none d-lg-table-cell">Sucursal</th>
                            <th class="border-white-10 d-none d-xl-table-cell">Caja</th>
                            <th class="border-white-10 d-none d-sm-table-cell">Método</th>
                            <th class="border-white-10 text-nowrap">Total</th>
                            <th class="border-white-10 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="border-white-10 fw-bold text-nowrap">
                                    #{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="border-white-10 text-white-50 d-none d-md-table-cell">
                                    {{ \Carbon\Carbon::parse($sale->fecha)->format('d/m/Y H:i') }}
                                </td>
                                <td class="border-white-10">{{ $sale->client_name }}</td>
                                <td class="border-white-10 d-none d-lg-table-cell text-white-50">
                                    {{ $sale->user->name ?? 'N/A' }}
                                </td>
                                <td class="border-white-10 d-none d-lg-table-cell text-white-50">
                                    {{ $sale->sucursal->nombre ?? 'N/A' }}
                                </td>
                                <td class="border-white-10 d-none d-xl-table-cell text-white-50">
                                    {{ $sale->cashRegister->caja->nombre ?? 'N/A' }}
                                </td>
                                <td class="border-white-10 d-none d-sm-table-cell"><span
                                        class="small opacity-75">{{ $sale->payment_method }}</span></td>
                                <td class="border-white-10 fw-bold text-success text-nowrap">Bs
                                    {{ number_format($sale->total, 2) }}
                                </td>
                                <td class="border-white-10 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button wire:click="viewDetails({{ $sale->id }})"
                                            class="btn btn-sm btn-glass text-info" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.sales.pdf', $sale->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-white-50">No hay ventas registradas en este
                                    rango</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Detalles de Venta -->
    <div class="modal fade" id="saleDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-white-10 shadow-lg">
                <div class="modal-header border-bottom border-white-10">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                        Detalle de Venta #{{ $selected_sale ? str_pad($selected_sale->id, 6, '0', STR_PAD_LEFT) : '' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($selected_sale)
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Cliente</label>
                                <div class="h6 mb-0">{{ $selected_sale->client_name }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Usuario (Vendedor)</label>
                                <div class="h6 mb-0">{{ $selected_sale->user->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Fecha</label>
                                <div class="h6 mb-0">{{ \Carbon\Carbon::parse($selected_sale->fecha)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Sucursal</label>
                                <div class="h6 mb-0">{{ $selected_sale->sucursal->nombre ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Caja</label>
                                <div class="h6 mb-0">{{ $selected_sale->cashRegister->caja->nombre ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-white-50 small text-uppercase fw-bold">Método de Pago</label>
                                <div class="h6 mb-0">{{ $selected_sale->payment_method }}</div>
                            </div>
                        </div>

                        <div class="table-responsive rounded-3 border border-white-10">
                            <table class="table table-dark mb-0">
                                <thead class="small text-white-50">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Precio</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selected_sale->details as $detail)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $detail->product->marca }}</div>
                                                <div class="small text-white-50">{{ $detail->product->modelo }}</div>
                                            </td>
                                            <td class="text-center">{{ $detail->cantidad }}</td>
                                            <td class="text-end">Bs {{ number_format($detail->precio_unitario, 2) }}</td>
                                            <td class="text-end fw-bold">
                                                Bs {{ number_format($detail->cantidad * $detail->precio_unitario, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top border-white-10">
                                    <tr>
                                        <td colspan="3" class="text-end text-white-50">Total Final:</td>
                                        <td class="text-end h5 fw-bold text-success">
                                            Bs {{ number_format($selected_sale->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top border-white-10">
                    <button type="button" class="btn btn-outline-light px-4" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary px-4"><i class="fas fa-print me-2"></i>Imprimir
                        Tiket</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('openDetailModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
            modal.show();
        });
    </script>
</div>