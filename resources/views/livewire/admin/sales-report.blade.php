<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-white mb-2">
                        <i class="fas fa-receipt me-2"></i> Reporte de Ventas
                    </h1>
                    <p class="text-muted mb-0">Análisis detallado de todas las ventas registradas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-filter me-2"></i> Filtros
                </h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Desde</label>
                        <input
                            type="date"
                            class="form-control form-control-glass"
                            wire:model.live="sales_from_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;"
                        >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Hasta</label>
                        <input
                            type="date"
                            class="form-control form-control-glass"
                            wire:model.live="sales_to_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;"
                        >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Sucursal</label>
                        <select
                            class="form-select form-control-glass"
                            wire:model.live="sales_branch"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;"
                        >
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Categoría</label>
                        <select
                            class="form-select form-control-glass"
                            wire:model.live="sales_category"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;"
                        >
                            <option value="">Todas</option>
                            <option value="pantalla">Pantallas</option>
                            <option value="accesorio">Accesorios</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Buscar</label>
                        <input
                            type="text"
                            class="form-control form-control-glass"
                            placeholder="Folio, Cliente..."
                            wire:model.live="search"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;"
                        >
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button
                            class="btn w-100"
                            onclick="downloadSalesPdf()"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; transition: all 0.3s ease; cursor: pointer;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                            title="Descargar PDF"
                        >
                            <i class="fas fa-print me-2"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Rápido (Cards) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row g-3">
                @php
                    $summary = $this->getSalesSummary();
                @endphp

                <!-- Total Vendido -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden" style="border: 1px solid rgba(102, 126, 234, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Total Vendido</p>
                                <h3 class="text-white fw-bold mb-0">Bs {{ number_format($summary['total'], 2) }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #667eea;">
                                <i class="fas fa-shopping-bag"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ganancia Estimada -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden" style="border: 1px solid rgba(76, 201, 154, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-profit"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Ganancia Estimada</p>
                                <h3 class="text-white fw-bold mb-0">Bs {{ number_format($summary['profit'], 2) }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #4cc99a;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Margen de Ganancia -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden" style="border: 1px solid rgba(102, 126, 234, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1; color: #667eea;">
                            <i class="fas fa-percent"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Margen (%)</p>
                                <h3 class="text-white fw-bold mb-0">
                                    {{ $summary['total'] > 0 ? number_format(($summary['profit'] / $summary['total']) * 100, 1) : 0 }}%
                                </h3>
                            </div>
                            <div style="font-size: 30px; color: #667eea;">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Ventas -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden" style="border: 1px solid rgba(255, 193, 7, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">N° de Ventas</p>
                                <h3 class="text-white fw-bold mb-0">{{ $summary['salesCount'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #ffc107;">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Ventas (DataTable) -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-table me-2"></i> Detalle de Ventas
                </h5>

                @if(empty($sales))
                    <div class="alert alert-info text-white" role="alert" style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.3);">
                        <i class="fas fa-info-circle me-2"></i> No hay ventas registradas en el período seleccionado.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table text-white" style="border: none;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;" wire:click="setSortField('id')">
                                        Folio
                                        @if($sortField === 'id')
                                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;" wire:click="setSortField('fecha')">
                                        Fecha
                                        @if($sortField === 'fecha')
                                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600;">Sucursal</th>
                                    <th style="font-weight: 600;">Cliente</th>
                                    <th style="font-weight: 600;">Productos</th>
                                    <th style="font-weight: 600; text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.05)';" onmouseout="this.style.backgroundColor='transparent';">
                                        <td class="fw-bold" style="color: #667eea;">{{ $sale['id'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sale['fecha'])->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sale['sucursal']['nombre'] ?? 'N/A' }}</td>
                                        <td class="text-white-50">{{ $sale['client_name'] ?? 'Venta General' }}</td>
                                        <td>{{ count($sale['details']) }} producto{{ count($sale['details']) !== 1 ? 's' : '' }}</td>
                                        <td style="text-align: right; color: #4cc99a; font-weight: 600;">Bs {{ number_format($sale['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de página tabla -->
                    <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between text-white fw-bold mb-2">
                                    <span>Total General:</span>
                                    <span style="color: #4cc99a;">Bs {{ number_format($summary['total'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-white fw-bold">
                                    <span>Ganancia Estimada:</span>
                                    <span style="color: #4cc99a;">Bs {{ number_format($summary['profit'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function downloadSalesPdf() {
            const fromDate = @this.sales_from_date;
            const toDate = @this.sales_to_date;
            const branch = @this.sales_branch;
            const category = @this.sales_category;

            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                branch_id: branch,
                category: category
            });

            window.location.href = '{{ route("admin.reportes.pdf.ventas") }}?' + params.toString();
        }
    </script>
</div>
