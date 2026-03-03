<div>
    @section('titulo', 'Caja')
    @section('encabezado', 'Gestión de Caja (Apertura y Cierre)')

    <div class="container-fluid px-0 px-md-2">
        <div class="row g-4">
            <!-- Columna de Apertura/Estado -->
            <div class="col-xl-4 col-lg-5">
                <div class="glass-card p-4 h-100 shadow-lg border-0">
                    <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-white-10">
                        <div class="bg-primary-subtle p-3 rounded-circle me-3">
                            <i class="fas fa-cash-register text-primary"></i>
                        </div>
                        <h5 class="text-white mb-0 fw-bold">
                            @if ($active_register)
                                Estado: <span class="text-success pulse-text">ABIERTA</span>
                            @else
                                Apertura de Turno
                            @endif
                        </h5>
                    </div>

                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show bg-success-subtle text-success border-0 mb-4"
                            role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (!$active_register)
                        <form wire:submit.prevent="abrirCaja" class="form-opening">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label text-white-50 small fw-bold text-uppercase">Estación de
                                        Caja</label>
                                    <select wire:model="caja_id" class="form-select">
                                        <option value="">Seleccione una caja...</option>
                                        @foreach ($available_cajas as $c)
                                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('caja_id')
                                        <span class="text-danger small mt-1 d-block"><i
                                                class="fas fa-exclamation-triangle me-1"></i> {{ $message }}</span>
                                    @enderror

                                    @if ($available_cajas->isEmpty())
                                        <div
                                            class="text-warning small mt-2 bg-warning-subtle p-2 rounded border border-warning-20">
                                            <i class="fas fa-exclamation-circle me-1"></i> No hay cajas activas.
                                            <a href="{{ route('admin.cajas.index') }}"
                                                class="text-primary fw-bold text-decoration-none ms-1">Configurar cajas</a>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-white-50 small fw-bold text-uppercase">Usuario /
                                        Vendedor</label>
                                    <select wire:model="user_id" class="form-select">
                                        <option value="">Seleccione al responsable...</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="text-danger small mt-1 d-block"><i
                                                class="fas fa-exclamation-triangle me-1"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-white-50 small fw-bold text-uppercase">Monto Inicial
                                        (Bs)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white-5 border-white-10 text-white-50">Bs</span>
                                        <input wire:model="monto_apertura" type="number" step="0.01" class="form-control"
                                            placeholder="0.00">
                                    </div>
                                    @error('monto_apertura')
                                        <span class="text-danger small mt-1 d-block"><i
                                                class="fas fa-exclamation-triangle me-1"></i> {{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label
                                        class="form-label text-white-50 small fw-bold text-uppercase">Observaciones</label>
                                    <textarea wire:model="observaciones" class="form-control" rows="3"
                                        placeholder="Nota opcional para el turno..."></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit"
                                        class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm transition-all shadow-hover">
                                        <i class="fas fa-unlock me-2"></i> ABRIR CAJA AHORA
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="active-register-details mt-2">
                            <div class="p-4 bg-white-5 rounded-4 border border-white-10 shadow-inner mb-4">
                                <div class="row g-4">
                                    <div class="col-6">
                                        <label
                                            class="text-white-50 small text-uppercase fw-bold d-block mb-1">Apertura</label>
                                        <span
                                            class="text-white h6 mb-0">{{ \Carbon\Carbon::parse($active_register->fecha_apertura)->format('H:i') }}
                                            <small
                                                class="text-white-50">{{ \Carbon\Carbon::parse($active_register->fecha_apertura)->format('d/m') }}</small></span>
                                    </div>
                                    <div class="col-6">
                                        <label class="text-white-50 small text-uppercase fw-bold d-block mb-1">Monto
                                            Inicial</label>
                                        <span class="text-white h6 fw-bold mb-0">Bs
                                            {{ number_format($active_register->monto_apertura, 2) }}</span>
                                    </div>
                                    <div class="col-12 border-top border-white-10 pt-3">
                                        <label
                                            class="text-white-50 small text-uppercase fw-bold d-block mb-1">Responsable</label>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                {{ strtoupper(substr($active_register->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-white fw-bold">{{ $active_register->user->name }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 border-top border-white-10 pt-3">
                                        <label class="text-white-50 small text-uppercase fw-bold d-block mb-2">Ventas del
                                            Turno</label>
                                        <span class="text-success h2 fw-bold mb-0">Bs
                                            {{ number_format($active_register->total_ventas, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <button onclick="confirmCierre()"
                                class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm shadow-hover">
                                <i class="fas fa-lock me-2"></i> CERRAR ESTE TURNO
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Columna de Historial -->
            <div class="col-xl-8 col-lg-7">
                <div class="glass-card p-4 h-100 shadow-lg border-0">
                    <div
                        class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-white-10">
                        <h5 class="text-white mb-0 fw-bold">Historial Reciente</h5>
                        <span class="badge bg-white-10 text-white-50 px-3 py-2 rounded-pill small">Últimos
                            movimientos</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0 custom-glass-table">
                            <thead class="text-white-50 small text-uppercase">
                                <tr>
                                    <th class="border-white-10 py-3">Fecha y Hora</th>
                                    <th class="border-white-10 py-3 d-none d-md-table-cell">Sucursal</th>
                                    <th class="border-white-10 py-3">Caja / Usuario</th>
                                    <th class="border-white-10 py-3 text-end">Ventas (Turno)</th>
                                    <th class="border-white-10 py-3 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($registers as $reg)
                                    <tr class="transition-all hover-row">
                                        <td class="border-white-10 py-3">
                                            <div class="small fw-bold text-white">A:
                                                {{ \Carbon\Carbon::parse($reg->fecha_apertura)->format('d/m/y H:i') }}
                                            </div>
                                            @if ($reg->fecha_cierre)
                                                <div class="small text-white-50">C:
                                                    {{ \Carbon\Carbon::parse($reg->fecha_cierre)->format('d/m/y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="border-white-10 py-3 d-none d-md-table-cell">
                                            <span
                                                class="badge bg-white-5 text-white-50 border border-white-10">{{ $reg->sucursal->nombre ?? 'S/N' }}</span>
                                        </td>
                                        <td class="border-white-10 py-3">
                                            <div class="text-primary fw-bold small text-uppercase mb-1">
                                                {{ $reg->caja->nombre ?? 'S/N' }}
                                            </div>
                                            <div class="small text-white opacity-75"><i
                                                    class="far fa-user me-1 text-white-50"></i> {{ $reg->user->name }}</div>
                                        </td>
                                        <td class="border-white-10 py-3 text-end">
                                            <div class="text-success fw-bold">Bs {{ number_format($reg->total_ventas, 2) }}
                                            </div>
                                            <div class="small text-white-50" style="font-size: 0.7rem;">Inicial: Bs
                                                {{ number_format($reg->monto_apertura, 2) }}
                                            </div>
                                        </td>
                                        <td class="border-white-10 py-3 text-center">
                                            @if ($reg->status == 'Abierta')
                                                <div class="d-flex flex-column align-items-center gap-2">
                                                    <span
                                                        class="badge rounded-pill px-3 bg-success-subtle text-success border border-success-20">Abierta</span>
                                                    @can('gestionar-cajas')
                                                        <button onclick="confirmCierre({{ $reg->id }})"
                                                            class="btn btn-sm btn-outline-danger border-white-10 py-1 px-2 rounded-pill"
                                                            style="font-size: 0.65rem;">
                                                            <i class="fas fa-power-off me-1"></i> CERRAR
                                                        </button>
                                                    @endcan
                                                </div>
                                            @else
                                                <span
                                                    class="badge rounded-pill px-3 bg-white-5 text-white-50 border border-white-10">{{ $reg->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-white-50">
                                            <i class="fas fa-receipt d-block mb-3 opacity-25" style="font-size: 3rem;"></i>
                                            No hay registros de caja disponibles
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $registers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .pulse-text {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .shadow-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3) !important;
        }
    </style>

    @section('scripts')
        <script>
            function confirmCierre(id = null) {
                Swal.fire({
                    title: '¿Cerrar Caja?',
                    text: id ? "Estás cerrando una caja de forma administrativa." : "Se registrará el monto total acumulado hasta el momento.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cerrar caja',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (id) {
                            @this.cerrarCajaEspecifica(id);
                        } else {
                            @this.cerrarCaja();
                        }
                    }
                })
            }
        </script>
    @endsection
</div>