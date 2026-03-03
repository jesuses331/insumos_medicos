@forelse($productos as $producto)
    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
        <td class="px-4 py-4">
            <div class="fw-bold text-white mb-1">
                @if($producto->nombre_generico)
                    <span class="text-white-50 small text-uppercase d-block"
                        style="font-size: 0.65rem;">{{ $producto->nombre_generico }}</span>
                @endif
                {{ $producto->nombre_comercial }}
            </div>
            <div class="small opacity-50 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                {{ $producto->unidad_medida }}
            </div>
        </td>
        <td class="px-4 py-4">
            <span class="badge rounded-pill px-3 py-2 fw-bold text-uppercase"
                style="font-size: 0.6rem;
                                            @if ($producto->categoria->nombre == 'pantalla') background: rgba(102, 126, 234, 0.15); color: #667eea; border: 1px solid rgba(102, 126, 234, 0.2);
                                            @else background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); @endif">
                {{ $producto->categoria->nombre }}
            </span>
        </td>
        <td class="px-4 py-4 text-center">
            @php
                $activeBranchId = session('active_sucursal_id');
                $localStock = $producto->sucursales->where('id', $activeBranchId)->first()->pivot->stock ?? 0;
                $totalStock = $producto->sucursales->sum('pivot.stock');
            @endphp
            <div
                class="h5 fw-bold mb-0 {{ $localStock <= 2 ? 'text-danger' : ($localStock <= 5 ? 'text-warning' : 'text-white') }}">
                {{ $localStock }}
            </div>
            <div class="small opacity-50" style="font-size: 0.7rem;">Total: {{ $totalStock }}</div>
        </td>
        <td class="px-4 py-4">
            <div class="fw-bold text-success">Bs {{ number_format($producto->precio_venta, 2) }}</div>
            <div class="small opacity-50" style="font-size: 0.7rem;">Costo: Bs {{ number_format($producto->costo, 2) }}
            </div>
        </td>
        <td class="px-4 py-4 text-end">
            <div class="d-flex justify-content-end gap-2">
                <button type="button"
                    onclick="openDefectiveModal('{{ $producto->id }}', '{{ $activeBranchId }}', '{{ $producto->nombre_comercial }} ({{ $producto->nombre_generico }})')"
                    class="btn btn-sm btn-outline-warning rounded-circle p-2 opacity-50 hover-opacity-100"
                    title="Reportar Defectuoso" style="width: 34px; height: 34px;">
                    <i class="fas fa-exclamation-triangle small"></i>
                </button>
                <a href="{{ route('admin.productos.editar', $producto->id) }}"
                    class="btn btn-sm btn-outline-light rounded-circle p-2 opacity-50 hover-opacity-100"
                    style="width: 34px; height: 34px;">
                    <i class="fas fa-edit small"></i>
                </a>
                <form action="{{ route('admin.productos.eliminar', $producto->id) }}" method="POST"
                    onsubmit="return confirm('¿Eliminar este producto permanentemente?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="btn btn-sm btn-outline-danger rounded-circle p-2 opacity-50 hover-opacity-100"
                        style="width: 34px; height: 34px;">
                        <i class="fas fa-trash-alt small"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <i class="fas fa-box-open text-white-10 display-4 mb-3 d-block"></i>
            <p class="mb-0">El catálogo está vacío</p>
        </td>
    </tr>
@endforelse