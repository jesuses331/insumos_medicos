@extends('layouts.plantilla')

@section('encabezado', 'Catálogo de Productos')

@section('contenido')
    <div class="container-fluid p-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="h2 fw-bold text-white mb-1">Productos</h1>
                <p class="text-white-50 mb-0">Gestión de insumos médicos y equipos</p>
            </div>
            <div class="d-flex gap-2">
                <form onsubmit="return false;" class="d-flex gap-2">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text bg-white-5 border-white-10 text-black-50">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input id="product-search" type="text" name="search" value="{{ $search }}"
                            placeholder="Nombre o categoría..."
                            class="form-control bg-white-5 border-white-10 text-white" />
                    </div>
                </form>
                <a href="{{ route('admin.productos.importar') }}" class="btn btn-glass-secondary px-4 py-2 shadow-lg">
                    <i class="fas fa-file-excel me-2"></i> Importar
                </a>
                <a href="{{ route('admin.productos.crear') }}" class="btn btn-glass px-4 py-2 shadow-lg">
                    <i class="fas fa-plus me-2"></i> Nuevo
                </a>
            </div>
        </div>

        <div class="glass-card p-0 overflow-hidden border-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-white-50 small text-uppercase fw-bold" style="background: rgba(255,255,255,0.03);">
                            <th class="px-4 py-3 border-0">Producto</th>
                            <th class="px-4 py-3 border-0">Categoría</th>
                            <th class="px-4 py-3 border-0 text-center">Stock (Local / Total)</th>
                            <th class="px-4 py-3 border-0">Lista de Precios</th>
                            <th class="px-4 py-3 border-0 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody" class="text-white-50">
                        @include('admin.productos._table_rows')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Reportar Producto Defectuoso -->
    <div class="modal fade" id="defectiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-header border-white-10">
                    <h5 class="modal-title text-white">Reportar Producto Defectuoso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="defectiveForm">
                    @csrf
                    <input type="hidden" name="id" id="defective_product_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Producto</label>
                            <input type="text" id="defective_product_name"
                                class="form-control bg-white-5 border-white-10 text-white" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white-50">Cantidad Defectuosa</label>
                            <input type="number" name="cantidad" class="form-control bg-white-5 border-white-10 text-white"
                                value="1" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white-50">Detalle del Falla/Problema</label>
                            <textarea name="detalle" class="form-control bg-white-5 border-white-10 text-white" rows="3"
                                placeholder="Describe el daño o defecto encontrado..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-white-10">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning px-4">Reportar Falla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="pagination-container" class="mt-4 d-flex justify-content-center">
        @if($productos->hasPages())
            {{ $productos->links() }}
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        function openDefectiveModal(id, branchId, name) {
            document.getElementById('defective_product_id').value = id;
            document.getElementById('defective_product_name').value = name;
            const modalElement = document.getElementById('defectiveModal');
            let modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }
            modal.show();
        }

        document.getElementById('defectiveForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

            try {
                const response = await fetch('{{ route("admin.productos.reportar.defectuoso") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    Swal.fire('¡Éxito!', result.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', result.message || 'Ocurrió un error', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Error de conexión', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Reportar Falla';
            }
        });

        (function () {
            const input = document.getElementById('product-search');
            const tbody = document.getElementById('products-tbody');
            let timer = null;

            function debounce(fn, delay) {
                return function () {
                    clearTimeout(timer);
                    const args = arguments;
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            async function fetchResults(q) {
                const params = new URLSearchParams();
                if (q) params.set('search', q);
                params.set('ajax', '1');
                const url = '{{ route("admin.productos.inicio") }}' + '?' + params.toString();
                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;
                    const html = await res.text();
                    tbody.innerHTML = html;
                    const pagContainer = document.getElementById('pagination-container');
                    if (pagContainer) pagContainer.style.display = q ? 'none' : 'flex';
                } catch (e) {
                    console.error('Search error', e);
                }
            }

            const handler = debounce(function (e) {
                fetchResults(e.target.value.trim());
            }, 300);

            if (input) {
                input.addEventListener('input', handler);
            }
        })();
    </script>
@endsection