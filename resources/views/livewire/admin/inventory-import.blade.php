<div>
    <div class="container-fluid p-0">
        <div class="mb-5">
            <h1 class="h2 fw-bold text-white mb-1">Importar Inventario</h1>
            <p class="text-white-50 mb-0">Carga masiva de productos y existencias mediante archivo CSV o Excel (.xlsx)
            </p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="glass-card p-4 border-0">
                    <h5 class="text-white mb-4"><i class="fas fa-file-import me-2"></i>Configuración de Carga</h5>

                    <form wire:submit.prevent="import">
                        <div class="mb-4">
                            <label class="form-label text-white-50">Seleccionar Sucursal de Destino</label>
                            <select wire:model="branch_id" class="form-select">
                                <option value="">Seleccione una sucursal...</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                @endforeach
                            </select>
                            @error('branch_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white-50">Archivo</label>
                            <div class="form-group">
                                <input type="file" wire:model="file"
                                    class="form-control bg-white-5 border-white-10 text-white"
                                    id="upload{{ $iteration ?? '' }}">
                                <div wire:loading wire:target="file" class="text-info small mt-1">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Cargando archivo...
                                </div>
                            </div>
                            @error('file') <span class="text-danger small">{{ $message }}</span> @enderror
                            <div class="mt-2 d-flex gap-3">
                                <a href="{{ asset('templates/inventario_plantilla.csv') }}"
                                    class="text-info small text-decoration-none">
                                    <i class="fas fa-file-csv me-1"></i> Plantilla CSV
                                </a>
                                <a href="{{ asset('templates/inventario_plantilla.xlsx') }}"
                                    class="text-success small text-decoration-none">
                                    <i class="fas fa-file-excel me-1"></i> Plantilla Excel
                                </a>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-glass py-3" wire:loading.attr="disabled">
                                <i class="fas fa-upload me-2"></i> Procesar Importación
                            </button>
                        </div>
                    </form>

                    @if($importMessage)
                        <div
                            class="mt-4 p-3 rounded {{ $successCount > 0 ? 'bg-success-10 text-success' : 'bg-danger-10 text-danger' }} border border-white-10">
                            {{ $importMessage }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-card p-4 border-0 h-100">
                    <h5 class="text-white mb-4"><i class="fas fa-info-circle me-2"></i>Instrucciones</h5>
                    <div class="text-white-50 small">
                        <p>Para asegurar una importación exitosa, siga estas reglas:</p>
                        <ul class="ps-3">
                            <li class="mb-2">El archivo debe ser formato <strong>CSV</strong> o <strong>Excel
                                    (.xlsx)</strong>.</li>
                            <li class="mb-2">No altere los encabezados de la primera fila.</li>
                            <li class="mb-2"><strong>Genérico, Comercial, Unidad</strong>: Se usan para identificar el
                                producto y su presentación.</li>
                            <li class="mb-2"><strong>costo, precio_venta</strong>: Use números decimales (ej. 15.50).
                            </li>
                            <li class="mb-2"><strong>Unidad de medida</strong>: Debe ser <code>Caja</code>,
                                <code>Unidad</code>, <code>Blister</code> o <code>Frasco</code>.
                            </li>
                            <li class="mb-2"><strong>stock</strong>: Cantidad entera que se asignará a la sucursal
                                seleccionada.</li>
                        </ul>
                        <div class="alert bg-white-10 border-0 text-white mt-4">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            <strong>Nota:</strong> Esta acción actualizará los precios de productos existentes y
                            REEMPLAZARÁ el stock en la sucursal seleccionada con el valor del archivo.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('swal', (data) => {
                    const eventData = Array.isArray(data) ? data[0] : data;
                    Swal.fire({
                        title: eventData.title,
                        text: eventData.text,
                        icon: eventData.icon,
                        background: '#1a1d21',
                        color: '#ffffff',
                        confirmButtonColor: '#3085d6'
                    });
                });
            });
        </script>
    @endpush
</div>