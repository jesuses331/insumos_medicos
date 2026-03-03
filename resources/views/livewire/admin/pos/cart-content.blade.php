<h4 class="fw-bold text-white mb-4 d-flex align-items-center">
    <i class="fas fa-shopping-cart me-3 opacity-50"></i> Carrito
</h4>

<div class="flex-grow-1 overflow-y-auto mb-4 custom-scrollbar" style="min-height: 150px;">
    @forelse($cart as $id => $item)
        <div class="rounded-3 mb-2 border border-white-10 bg-white-5 p-3">
            {{-- Top row: name + subtotal --}}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="flex-grow-1 overflow-hidden me-2">
                    <div class="fw-semibold text-white text-truncate" style="font-size:0.88rem;">{{ $item['nombre'] }}</div>
                    <div class="text-white-50 small" style="font-size:0.75rem;">
                        Lote: <span class="text-info">{{ $item['lote'] ?? 'N/A' }}</span> |
                        Vence: <span
                            class="text-{{ \Carbon\Carbon::parse($item['fecha_vencimiento'])->isPast() ? 'danger' : 'warning' }}">{{ $item['fecha_vencimiento'] ? \Carbon\Carbon::parse($item['fecha_vencimiento'])->format('d/m/Y') : 'N/A' }}</span>
                    </div>
                </div>
                <div class="fw-bold text-white text-nowrap" style="font-size:0.92rem;">
                    Bs {{ number_format($item['precio'] * $item['cantidad'], 2) }}
                </div>
            </div>
            {{-- Bottom row: qty controls + trash --}}
            <div class="d-flex align-items-center gap-2">
                <button wire:click.prevent="decreaseQuantity({{ $id }})"
                    class="btn btn-sm btn-outline-light rounded-circle p-0 flex-shrink-0"
                    style="width:30px;height:30px;">&#8722;</button>

                <input type="number" min="1" value="{{ $item['cantidad'] }}" max="{{ $item['stock'] ?? 9999 }}"
                    oninput="this.value = Math.max(1, Math.min(this.value, this.max));"
                    class="form-control form-control-sm text-center text-white fw-bold"
                    style="width:58px;height:30px;padding:0;background:rgba(0,0,0,0.4);border-color:rgba(255,255,255,0.15);"
                    wire:change="setQuantity({{ $id }}, $event.target.value)">

                <button wire:click.prevent="increaseQuantity({{ $id }})" @if(($item['cantidad'] ?? 0) >= ($item['stock'] ?? 0)) disabled @endif class="btn btn-sm btn-outline-light rounded-circle p-0 flex-shrink-0"
                    style="width:30px;height:30px;">+</button>

                <span class="text-white-50 flex-grow-1" style="font-size:0.72rem;">
                    Max: {{ $item['stock'] ?? '&#8212;' }}
                </span>

                <button wire:click="removeFromCart({{ $id }})" class="btn btn-link p-0 text-danger flex-shrink-0"
                    style="opacity:0.65;">
                    <i class="fas fa-trash-alt" style="font-size:0.8rem;"></i>
                </button>
            </div>
            @if(isset($qtyErrors[$id]))
                <div class="text-danger mt-1" style="font-size:0.75rem;">{{ $qtyErrors[$id] }}</div>
            @endif
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-shopping-basket text-white-10 h1 mb-3 d-block"></i>
            <p class="text-white-50 small">Selecciona productos</p>
        </div>
    @endforelse
</div>


<div class="mt-auto pt-4 border-top border-white-10">
    <!-- Client Search / Selection -->
    <div class="mb-3 pos-client-wrapper position-relative">
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
                <button wire:click="clearClient" class="btn btn-sm p-0 text-white-50 hover-text-white"
                    title="Cambiar cliente" style="line-height:1;">
                    <i class="fas fa-times-circle"></i>
                </button>
            </div>
        @else
            {{-- Buscador --}}
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white-5 border-white-10 text-white-50">
                    <i class="fas fa-search"></i>
                </span>
                <input wire:model.live.debounce.300ms="client_search" type="text" id="pos-client-search"
                    placeholder="Buscar por nombre o teléfono..." class="form-control bg-white-5 border-white-10 text-white"
                    autocomplete="off">
                <button class="btn btn-glass btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#newClientModal">
                    <i class="fas fa-user-plus me-1"></i>Nuevo
                </button>
            </div>

            @if(!empty($clientResults))
                <div class="autocomplete-dropdown position-absolute w-100 mt-1 rounded-3 overflow-hidden shadow-lg"
                    style="z-index:9999; top:100%; left:0; border:1px solid rgba(255,255,255,0.12); background:rgba(18,18,35,0.97); backdrop-filter:blur(16px);">
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
                    style="z-index:9999; top:100%; left:0; border:1px solid rgba(255,255,255,0.1); background:rgba(18,18,35,0.95);">
                    <i class="fas fa-search me-1"></i> Sin resultados — <a href="#" class="text-info text-decoration-none"
                        data-bs-toggle="modal" data-bs-target="#newClientModal">Crear nuevo</a>
                </div>
            @endif
        @endif
    </div>

    <!-- Payment Method -->
    <div class="mb-4">
        <label class="form-label text-white-50 small fw-bold text-uppercase">Método de Pago</label>
        <select wire:model="payment_method" class="form-select form-select-sm">
            <option value="Efectivo">Efectivo</option>
            <option value="Transferencia">Transferencia</option>
            <option value="Yape/Plin">Yape / Plin</option>
            <option value="Tarjeta">Tarjeta Crédito/Débito</option>
        </select>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="text-white-50">Total a pagar:</span>
        <span class="h2 fw-bold text-success mb-0">Bs {{ number_format($total, 2) }}</span>
    </div>
    <button type="button" onclick="confirmSale()" {{ empty($cart) ? 'disabled' : '' }}
        class="btn btn-glass w-100 py-3 shadow-lg fw-bold">
        <i class="fas fa-check-circle me-2"></i> FINALIZAR VENTA
    </button>
</div>

<style>
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
            return document.querySelectorAll('#pos-client-search')
                .length
                ? document.querySelectorAll('.autocomplete-dropdown .autocomplete-item')
                : [];
        }

        function highlight(items, idx) {
            items.forEach((el, i) => el.classList.toggle('is-active', i === idx));
            if (items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
        }

        document.addEventListener('keydown', function (e) {
            const input = document.getElementById('pos-client-search');
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

        // Reset index on each Livewire re-render
        document.addEventListener('livewire:navigated', () => { acIndex = -1; });
        document.addEventListener('livewire:update', () => { acIndex = -1; });
    })();
</script>