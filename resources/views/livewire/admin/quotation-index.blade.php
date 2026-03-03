@section('titulo', 'Cotizaciones')
@section('encabezado', 'Listado de Cotizaciones')

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div class="d-flex flex-grow-1 gap-2">
                    <div class="input-group" style="max-width: 400px;">
                        <span class="input-group-text bg-white-5 border-white-10 text-white-50"><i
                                class="fas fa-search"></i></span>
                        <input wire:model.live="search" type="text" placeholder="Buscar por cliente o folio..."
                            class="form-control bg-white-5 border-white-10 text-white">
                    </div>
                    <select wire:model.live="status" class="form-select bg-white-5 border-white-10 text-white w-auto">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Confirmada">Confirmada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <a href="{{ route('admin.quotations.create') }}" class="btn btn-glass px-4">
                    <i class="fas fa-plus me-2"></i> NUEVA COTIZACIÓN
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-white-50 small text-uppercase">
                        <tr>
                            <th class="border-white-10 text-nowrap">Folio</th>
                            <th class="border-white-10 text-nowrap d-none d-md-table-cell">Fecha</th>
                            <th class="border-white-10">Cliente</th>
                            <th class="border-white-10 text-nowrap">Total</th>
                            <th class="border-white-10 text-center">Estado</th>
                            <th class="border-white-10 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($quotations as $q)
                            <tr>
                                <td class="border-white-10 fw-bold text-nowrap">#{{ str_pad($q->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="border-white-10 text-white-50 d-none d-md-table-cell">
                                    {{ $q->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="border-white-10">{{ $q->client_name }}</td>
                                <td class="border-white-10 fw-bold text-success text-nowrap">Bs
                                    {{ number_format($q->total, 2) }}
                                </td>
                                <td class="border-white-10 text-center">
                                    <span
                                        class="badge rounded-pill px-3 py-2 text-nowrap {{ $q->status == 'Pendiente' ? 'bg-warning-subtle text-warning' : ($q->status == 'Confirmada' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') }}">
                                        {{ $q->status }}
                                    </span>
                                </td>
                                <td class="border-white-10 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($q->status == 'Pendiente')
                                            <button onclick="confirmConversion({{ $q->id }})"
                                                class="btn btn-sm btn-outline-success" title="Convertir a Venta">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <a href="{{ route('admin.quotations.edit', $q->id) }}"
                                                class="btn btn-sm btn-outline-info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.quotations.pdf', $q->id) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary" title="Ver PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                        <button wire:click="sendByWhatsApp({{ $q->id }})"
                                            class="btn btn-sm btn-outline-success" title="Enviar WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                        </button>

                                        <button onclick="confirmDelete({{ $q->id }})" class="btn btn-sm btn-outline-danger"
                                            title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-white-50">No se encontraron cotizaciones</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $quotations->links() }}
            </div>
        </div>
    </div>

    <script>
        function confirmConversion(id) {
            Swal.fire({
                title: '¿Convertir a Venta?',
                text: "Esta acción generará una venta y descontará el stock correspondiente.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar',
                background: '#1a1c23',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.convertToSale(id);
                }
            })
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '¿Eliminar Cotización?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1a1c23',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.deleteQuotation(id);
                }
            })
        }
        window.addEventListener('openInNewTab', event => {
            window.open(event.detail[0].url, '_blank');
        });
    </script>
</div>