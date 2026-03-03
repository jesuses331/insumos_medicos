@section('titulo', 'Nuevo Traslado')
@section('encabezado', 'Crear Traslado de Inventario')

<div class="container-fluid p-0" style="max-width: 1200px;">
    <div class="mb-5">
        <h1 class="h2 fw-bold text-white mb-1">Nuevo Traslado</h1>
        <p class="text-white-50">Configura el envío de mercancía entre sedes</p>
    </div>

    <div class="row g-4">
        <!-- Configuracion y Buscador -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Origen</label>
                        <div class="p-3 rounded-3 bg-white-5 text-white fw-bold border border-white-10">
                            {{ session('active_sucursal_nombre') }}
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-white-50 small fw-bold text-uppercase">Destino</label>
                        <select wire:model="to_branch_id" class="form-select border-white-10">
                            <option value="">Seleccionar tienda...</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white-50 small fw-bold text-uppercase">Buscar Productos</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white-5 border-white-10 text-white-50"><i class="fas fa-search"></i></span>
                        <input wire:model.live="search" type="text" placeholder="Marca o modelo..." class="form-control border-white-10">
                    </div>
                </div>

                <div class="overflow-y-auto custom-scrollbar pe-2" style="max-height: 400px;">
                    @foreach($products as $prod)
                        <div class="p-3 rounded-4 mb-2 bg-white-5 border border-white-10 d-flex justify-content-between align-items-center transition-all hover-glow">
                            <div>
                                <div class="fw-bold text-white small">{{ $prod->marca }} {{ $prod->modelo }}</div>
                                <div class="text-white-50" style="font-size: 0.7rem;">DISPONIBLE: {{ $prod->sucursales->first()->pivot->stock }}</div>
                            </div>
                            <button wire:click="addItem({{ $prod->id }})" class="btn btn-sm btn-primary rounded-circle shadow-sm" style="width: 30px; height: 30px; padding: 0;">
                                <i class="fas fa-plus small"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lista de Items a Trasladar -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <h4 class="fw-bold text-white mb-4">Productos en Lista</h4>

                <div class="flex-grow-1 overflow-y-auto mb-4 pe-2 custom-scrollbar" style="min-height: 300px;">
                    @forelse($items as $index => $item)
                        <div class="p-3 rounded-4 mb-3 bg-white-10 border border-white-10 d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-white small">{{ $item['nombre'] }}</div>
                                <div class="text-white-50" style="font-size: 0.7rem;">CAPACIDAD MÁX: {{ $item['max'] }}</div>
                            </div>
                            <div class="mx-3" style="width: 80px;">
                                <input type="number" wire:model="items.{{ $index }}.cantidad" min="1" max="{{ $item['max'] }}"
                                    class="form-control form-control-sm text-center fw-bold">
                            </div>
                            <button wire:click="removeItem({{ $index }})" class="btn btn-link text-danger p-0 border-0 opacity-75 hover-opacity-100">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-5 my-auto">
                            <i class="fas fa-cube text-white-10 display-1 mb-4 d-block"></i>
                            <p class="text-white-50 fst-italic">No has seleccionado productos aún</p>
                        </div>
                    @endforelse
                </div>

                <div class="pt-4 border-top border-white-10">
                    <button wire:click="saveTransfer"
                        class="btn btn-glass w-100 py-3 shadow-lg"
                        {{ empty($items) ? 'disabled' : '' }}>
                        CREAR TRASLADO Y NOTIFICAR <i class="fas fa-paper-plane ms-2 small"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-white-5 { background: rgba(255, 255, 255, 0.05); }
        .bg-white-10 { background: rgba(255, 255, 255, 0.1); }
        .border-white-10 { border: 1px solid rgba(255, 255, 255, 0.1) !important; }
        .hover-glow:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(102, 126, 234, 0.3) !important;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
    </style>
</div>
