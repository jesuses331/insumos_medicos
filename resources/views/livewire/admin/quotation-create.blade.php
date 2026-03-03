@section('titulo', 'Nueva Cotización')
@section('encabezado', 'Crear Cotización')

<div class="container-fluid p-0">
    <div class="row g-4">
        <!-- Buscador y Productos -->
        <div class="col-lg-8">
            <div class="glass-card p-4 mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50"><i
                            class="fas fa-search"></i></span>
                    <input wire:model.live="search" type="text" placeholder="Buscar producto para cotizar..."
                        class="form-control ps-0 bg-transparent border-0">
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($products as $product)
                    @php $stock = $product->sucursales->first()->pivot->stock ?? 0; @endphp
                    <div class="col">
                        <button wire:click="addToCart({{ $product->id }})"
                            class="glass-card p-4 w-100 h-100 text-start transition-all border-0 position-relative overflow-hidden btn-selection">
                            <span
                                class="position-absolute top-0 end-0 m-3 px-2 py-1 rounded small fw-bold text-uppercase bg-info-subtle text-info"
                                style="font-size: 0.65rem;">
                                STOCK: {{ $stock }}
                            </span>
                            <div class="text-white-50 small fw-bold text-uppercase mb-1" style="font-size: 0.7rem;">
                                {{ $product->marca }}
                            </div>
                            <div class="h5 fw-bold text-white mb-1">{{ $product->modelo }}</div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="h5 fw-bold text-success mb-0">Bs
                                    {{ number_format($product->precio_venta, 2) }}</span>
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-white-10"
                                    style="width: 32px; height: 32px;">
                                    <i class="fas fa-plus text-info small"></i>
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Carrito de Cotización -->
        <div class="col-lg-4">
            <div class="glass-card p-4 sticky-top"
                style="top: 2rem; max-height: calc(100vh - 4rem); display: flex; flex-direction: column;">
                <h4 class="fw-bold text-white mb-4 d-flex align-items-center">
                    <i class="fas fa-file-invoice-dollar me-3 opacity-50"></i> Detalle
                </h4>

                <div class="flex-grow-1 overflow-y-auto mb-4 custom-scrollbar pe-2" style="min-height: 150px;">
                    @forelse($cart as $id => $item)
                        <div wire:key="cart-item-{{ $id }}"
                            class="p-3 rounded-4 mb-3 border border-white-10 bg-white-5 d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-white small">{{ $item['nombre'] }}</div>
                                <div class="text-white-50 small mt-1 d-flex align-items-center gap-2">
                                    <button wire:click.prevent="decreaseQuantity({{ $id }})"
                                        class="btn btn-sm btn-outline-light rounded-circle p-0"
                                        style="width:24px;height:24px;line-height:22px;">-</button>
                                    <input type="number" min="1" wire:key="qty-input-{{ $id }}"
                                        value="{{ $item['cantidad'] }}"
                                        class="form-control form-control-sm text-center bg-dark border-white-10 text-white fw-bold shadow-sm"
                                        style="width:70px;height:28px;font-size:1rem; color: #ffffff !important; background-color: rgba(0,0,0,0.6) !important; padding: 0;"
                                        wire:change="setQuantity({{ $id }}, $event.target.value)">
                                    <button wire:click.prevent="increaseQuantity({{ $id }})"
                                        class="btn btn-sm btn-outline-light rounded-circle p-0"
                                        style="width:24px;height:24px;line-height:22px;">+</button>
                                    <span class="ms-1">x Bs {{ number_format($item['precio'], 2) }}</span>
                                </div>
                            </div>
                            <div class="text-end ms-3">
                                <div class="fw-bold text-white mb-1">
                                    Bs {{ number_format($item['precio'] * $item['cantidad'], 2) }}</div>
                                <button wire:click="removeFromCart({{ $id }})"
                                    class="btn btn-link py-0 px-1 text-danger text-decoration-none opacity-50 hover-opacity-100">
                                    <i class="fas fa-times small"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list text-white-10 h1 mb-3 d-block"></i>
                            <p class="text-white-50">Agrega productos a la cotización</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-4 border-top border-white-10">
                    <div class="mb-3 position-relative">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Cliente</label>

                        @if($selected_client_id)
                            {{-- Cliente ya seleccionado --}}
                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 border border-white-10 bg-white-5">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width:32px;height:32px;background:rgba(13,202,240,0.15);">
                                    <i class="fas fa-user-check text-info small"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="text-white fw-semibold small text-truncate">{{ $client_name }}</div>
                                    <div class="text-white-50" style="font-size:0.7rem;">Cliente seleccionado</div>
                                </div>
                                <button wire:click="clearClient" class="btn btn-sm p-0 text-white-50"
                                    title="Cambiar cliente" style="line-height:1;">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        @else
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white-5 border-white-10 text-white-50">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input wire:model.live.debounce.300ms="client_search" type="text"
                                    id="quotation-client-search" placeholder="Buscar por nombre o teléfono..."
                                    class="form-control bg-white-5 border-white-10 text-white" autocomplete="off">
                            </div>

                            @if(!empty($clientResults))
                                <div class="position-absolute w-100 mt-1 rounded-3 overflow-hidden shadow-lg"
                                    style="z-index:9999; left:0; border:1px solid rgba(255,255,255,0.12); background:rgba(18,18,35,0.97); backdrop-filter:blur(16px);">
                                    @foreach($clientResults as $c)
                                        <div wire:click="selectClient({{ $c['id'] }})"
                                            class="autocomplete-item d-flex align-items-center gap-3 px-3 py-2 cursor-pointer"
                                            style="transition:background 0.15s;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width:30px;height:30px;background:rgba(255,255,255,0.07);">
                                                <i class="fas fa-user text-white-50" style="font-size:0.7rem;"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="text-white fw-semibold small text-truncate">{{ $c['name'] }}</div>
                                                @if($c['phone'])
                                                    <div class="text-white-50" style="font-size:0.72rem;">{{ $c['phone'] }}</div>
                                                @endif
                                            </div>
                                            <i class="fas fa-chevron-right text-white-30" style="font-size:0.65rem;"></i>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(strlen($client_search) >= 2)
                                <div class="position-absolute w-100 mt-1 rounded-3 px-3 py-2 text-white-50 small"
                                    style="z-index:9999; left:0; border:1px solid rgba(255,255,255,0.1); background:rgba(18,18,35,0.95);">
                                    <i class="fas fa-search me-1"></i> Sin resultados para "{{ $client_search }}"
                                </div>
                            @endif

                            <div class="mt-2 small text-warning" style="font-style:italic;">Se guardará como &ldquo;Cliente
                                General&rdquo; si no selecciona uno</div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="text-white-50">Subtotal:</span>
                        <span class="h2 fw-bold text-info mb-0">Bs {{ number_format($total, 2) }}</span>
                    </div>


                    @if(isset($quotationId))
                        <button wire:click="updateQuotation" {{ empty($cart) ? 'disabled' : '' }}
                            class="btn btn-warning w-100 py-3 shadow-lg fw-bold text-dark">
                            <i class="fas fa-edit me-2"></i> ACTUALIZAR COTIZACIÓN
                        </button>
                    @else
                        <button wire:click="saveQuotation" {{ empty($cart) ? 'disabled' : '' }}
                            class="btn btn-info w-100 py-3 shadow-lg fw-bold text-white">
                            <i class="fas fa-save me-2"></i> GUARDAR COTIZACIÓN
                        </button>
                    @endif
                    <a href="{{ route('admin.quotations.index') }}"
                        class="btn btn-outline-light w-100 py-2 mt-2 opacity-50 border-0">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .hover-bg-white-5:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .cursor-pointer {
            cursor: pointer;
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

        .bg-white-20 {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .border-white-20 {
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .btn-selection:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(0, 183, 255, 0.3) !important;
        }

        .autocomplete-item:hover {
            background: rgba(255, 255, 255, 0.07);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .autocomplete-item.is-active {
            background: rgba(13, 202, 240, 0.18) !important;
            outline: 1px solid rgba(13, 202, 240, 0.35);
        }
    </style>

    <script>
    (function () {
        let acIndex = -1;

        function getItems() {
            return document.querySelectorAll('#quotation-client-search').length
                ? document.querySelectorAll('.autocomplete-item')
                : [];
        }

        function highlight(items, idx) {
            items.forEach((el, i) => el.classList.toggle('is-active', i === idx));
            if (items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
        }

        document.addEventListener('keydown', function (e) {
            const input = document.getElementById('quotation-client-search');
            if (!input || document.activeElement !== input) return;

            const items = getItems();
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                acIndex = (acIndex + 1) % items.length;
                highlight(items, acIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                acIndex = (acIndex - 1 + items.length) % items.length;
                highlight(items, acIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[acIndex]) items[acIndex].click();
                acIndex = -1;
            } else if (e.key === 'Escape') {
                acIndex = -1;
                highlight(items, -1);
            }
        });

        document.addEventListener('livewire:navigated', () => { acIndex = -1; });
        document.addEventListener('livewire:update', () => { acIndex = -1; });
    })();
    </script>
</div>
