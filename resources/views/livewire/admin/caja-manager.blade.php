@section('titulo', 'Administrar Cajas')
@section('encabezado', 'Administración de Estaciones de Caja')

<div class="row g-4">
    <div class="col-md-4">
        <div class="glass-card p-4">
            <h5 class="text-white mb-4">
                <i class="fas fa-plus-circle me-2 text-primary"></i>
                {{ $updateMode ? 'Editar Caja' : 'Nueva Caja' }}
            </h5>

            @if(session()->has('success'))
                <div class="alert alert-success bg-success-subtle text-success border-0 small py-2 mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label class="form-label text-white-50 small fw-bold text-uppercase">Sucursal</label>
                    @if(auth()->user()->can('ver-reportes-globales'))
                        <select wire:model="sucursal_id" class="form-select">
                            <option value="">Seleccione Sucursal</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" class="form-control" value="{{ session('active_sucursal_nombre') }}" readonly>
                        <input type="hidden" wire:model="sucursal_id">
                    @endif
                    @error('sucursal_id') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label text-white-50 small fw-bold text-uppercase">Nombre de la Estación</label>
                    <input wire:model="nombre" type="text" class="form-control"
                        placeholder="Ej: Caja Principal, Mostrador 1">
                    @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label text-white-50 small fw-bold text-uppercase">Estado</label>
                    <select wire:model="estado" class="form-select">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-glass flex-grow-1">
                        <i class="fas fa-save me-2"></i> {{ $updateMode ? 'ACTUALIZAR' : 'GUARDAR' }}
                    </button>
                    @if($updateMode)
                        <button type="button" wire:click="resetInputFields" class="btn btn-outline-light border-white-10">
                            Cancelar
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="glass-card p-4">
            <h5 class="text-white mb-4">Estaciones Registradas</h5>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-white-50 small text-uppercase">
                        <tr>
                            <th class="border-white-10">Nombre</th>
                            <th class="border-white-10">Sucursal</th>
                            <th class="border-white-10">Estado</th>
                            <th class="border-white-10 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($cajas as $caja)
                            <tr>
                                <td class="border-white-10 fw-bold">{{ $caja->nombre }}</td>
                                <td class="border-white-10 small text-white-50">{{ $caja->sucursal->nombre }}</td>
                                <td class="border-white-10">
                                    <span
                                        class="badge rounded-pill {{ $caja->estado == 'Activo' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                        {{ $caja->estado }}
                                    </span>
                                </td>
                                <td class="border-white-10 text-center">
                                    <div class="btn-group">
                                        <button wire:click="edit({{ $caja->id }})"
                                            class="btn btn-sm btn-glass text-primary mx-1">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $caja->id }})"
                                            class="btn btn-sm btn-glass text-danger mx-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-white-50">No hay cajas registradas</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $cajas->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar Caja?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.delete(id);
            }
        })
    }
</script>