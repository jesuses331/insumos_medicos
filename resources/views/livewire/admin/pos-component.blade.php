@section('titulo', 'POS')
@section('encabezado', 'Punto de Venta')

<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Buscador y Productos -->
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                @if(!$active_register)
                    <div class="alert alert-warning border-0 bg-warning-subtle text-warning mb-3 d-flex align-items-center"
                        role="alert">
                        <i class="fas fa-exclamation-triangle me-3 h4 mb-0"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">Caja Cerrada</h6>
                            <p class="small mb-2">Debe abrir una caja para comenzar a vender.</p>
                            <a href="{{ route('admin.cash-register.index') }}" class="btn btn-warning btn-sm fw-bold">
                                IR A CAJA <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success border-0 bg-success-subtle text-success py-2 px-3 mb-3 d-flex align-items-center justify-content-between"
                        role="alert">
                        <div class="small">
                            <i class="fas fa-check-circle me-1"></i>
                            Caja Abierta: <strong>{{ $active_register->caja->nombre ?? 'S/N' }}</strong>
                        </div>
                        <span class="badge bg-success small">SESIÓN ACTIVA</span>
                    </div>
                @endif
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50"><i
                            class="fas fa-search"></i></span>
                    <input wire:model.live="search" type="text" placeholder="Buscar por nombre, genérico o lote..."
                        class="form-control ps-0 bg-transparent border-0">
                </div>
            </div>

            <div class="row g-3">
                @foreach($products as $product)
                    <div class="col-12 col-sm-6 col-xl-4">
                        <button wire:click="addToCart({{ $product->pivot_id }})"
                            class="pos-product-card w-100 text-start border-0 glass-card p-0 overflow-hidden btn-selection position-relative">
                            
                            <span
                                class="position-absolute top-0 end-0 m-2 px-2 py-1 rounded small fw-bold text-uppercase {{ $product->stock < 10 ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}"
                                style="font-size: 0.6rem; z-index:1;">STOCK: {{ $product->stock }} {{ $product->unidad_medida }}</span>

                            {{-- Mobile layout: horizontal --}}
                            <div class="d-flex d-sm-none align-items-center gap-3 px-3 py-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:44px;height:44px;background:rgba(220,53,69,0.15);">
                                    <i class="fas fa-file-medical text-danger"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="text-white-50"
                                        style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">
                                        {{ $product->nombre_generico ?: 'Genérico' }}</div>
                                    <div class="fw-bold text-white text-truncate" style="font-size:0.92rem;">
                                        {{ $product->nombre_comercial }}</div>
                                    <div class="text-info" style="font-size:0.65rem;">Lote: {{ $product->lote ?? 'N/A' }}</div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold text-success" style="font-size:0.9rem;">Bs
                                        {{ number_format($product->precio_venta, 2) }}</div>
                                </div>
                            </div>

                            {{-- Desktop layout: vertical card --}}
                            <div class="d-none d-sm-block p-4">
                                <div class="text-white-50 small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">
                                    {{ $product->nombre_generico ?: 'Genérico' }}</div>
                                <div class="h5 fw-bold text-white mb-1">{{ $product->nombre_comercial }}</div>
                                <div class="text-info small text-uppercase mb-1"
                                    style="font-size: 0.65rem; letter-spacing: 0.05em;">Lote: {{ $product->lote ?? 'N/A' }}
                                </div>
                                <div class="text-white-50 small mb-3" style="font-size: 0.65rem;">
                                    Vence: {{ $product->fecha_vencimiento ? \Carbon\Carbon::parse($product->fecha_vencimiento)->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 fw-bold text-success mb-0">Bs
                                        {{ number_format($product->precio_venta, 2) }}</span>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white-10"
                                        style="width: 32px; height: 32px;">
                                        <i class="fas fa-plus text-danger small"></i>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Carrito (Desktop Sidebar) -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="glass-card p-4 sticky-top"
                style="top: 2rem; max-height: calc(100vh - 4rem); display: flex; flex-direction: column;">
                @include('livewire.admin.pos.cart-content')
            </div>
        </div>
    </div>

    <!-- Carrito (Mobile Offcanvas) -->
    <div class="offcanvas offcanvas-end text-white" tabindex="-1" id="offcanvasCart"
        aria-labelledby="offcanvasCartLabel"
        style="width:360px;background:rgba(14,14,22,0.98);backdrop-filter:blur(20px);border-left:1px solid rgba(255,255,255,0.1);">
        <div class="offcanvas-header border-bottom border-white-10 py-3">
            <h5 class="offcanvas-title fw-bold text-white" id="offcanvasCartLabel">
                <i class="fas fa-shopping-cart me-2 opacity-50"></i> Carrito
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column h-100 p-4" style="overflow-y:auto;">
            @include('livewire.admin.pos.cart-content')
        </div>
    </div>

    <!-- Floating Action Button (Mobile) -->
    <button class="btn btn-primary rounded-circle shadow-lg fab d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasCart">
        <i class="fas fa-shopping-cart"></i>
        @if(count($cart) > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ count($cart) }}
            </span>
        @endif
    </button>

    <style>
        .fab {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            width: 58px;
            height: 58px;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.45);
        }

        .bg-white-5 {
            background: rgba(255, 255, 255, 0.05);
        }

        .bg-white-10 {
            background: rgba(255, 255, 255, 0.1);
        }

        .border-white-10 {
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .btn-selection {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-selection:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(102, 126, 234, 0.3) !important;
        }

        /* Compact mobile product card - no lift on touch */
        @media (max-width: 575px) {
            .pos-product-card {
                border-radius: 0.85rem !important;
            }

            .pos-product-card:active {
                transform: scale(0.98);
            }

            .btn-selection:hover {
                transform: none;
            }

            #offcanvasCart {
                width: 100% !important;
            }

            .fab {
                right: 1rem;
                bottom: 1rem;
                width: 52px;
                height: 52px;
                font-size: 1.2rem;
            }
        }

        /* Scrollbar styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .offcanvas {
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
        }
    </style>

    <script>
        window.addEventListener('swal', event => {
            const data = event.detail[0];
            Swal.fire({
                title: data.title,
                text: data.text,
                icon: data.icon,
                confirmButtonColor: '#667eea',
                background: '#1a1c23',
                color: '#fff'
            });
        });

        function confirmSale() {
            Swal.fire({
                title: '¿Confirmar Venta?',
                text: "Verifica que los productos y el total sean correctos.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, procesar',
                cancelButtonText: 'Cancelar',
                background: '#1a1c23',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.processSale();
                }
            })
        }

        // Preserve offcanvas open state across Livewire updates
        (function () {
            const offcanvasEl = document.getElementById('offcanvasCart');
            if (!offcanvasEl) return;
            let wasOpen = false;
            document.addEventListener('livewire:message.sent', () => {
                wasOpen = offcanvasEl.classList.contains('show');
            });
            document.addEventListener('livewire:message.processed', () => {
                if (wasOpen) {
                    try {
                        const bsOff = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                        bsOff.show();
                    } catch (e) {
                        // ignore
                    }
                    wasOpen = false;
                }
            });
        })();
    </script>
    <!-- Modal Nuevo Cliente (Moved here to avoid duplication and focus issues in Offcanvas) -->
    <div class="modal fade" id="newClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-white-10">
                <div class="modal-header border-bottom border-white-10">
                    <h5 class="modal-title">Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Nombre completo</label>
                        <input wire:model="new_client_name" type="text"
                            class="form-control bg-white-5 border-white-10 text-white" placeholder="Ej: Ana Gomez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-white-50">Teléfono</label>
                        <input wire:model="new_client_phone" type="text"
                            class="form-control bg-white-5 border-white-10 text-white" placeholder="Ej: +51 9...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click.prevent="createClient" class="btn btn-glass">Crear y
                        seleccionar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Close modal after Livewire event
        window.addEventListener('close-new-client-modal', () => {
            try {
                const modalEl = document.getElementById('newClientModal');
                const m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                if (m) m.hide();
            } catch (e) {
                console.error("Error closing modal:", e);
            }
        });
    </script>
</div>
</div>