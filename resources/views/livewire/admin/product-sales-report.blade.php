<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-white mb-2">
                        <i class="fas fa-cube me-2"></i> Reporte de Ventas por Producto
                    </h1>
                    <p class="text-muted mb-0">Análisis detallado de productos con movimiento comercial</p>
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
                        <input type="date" class="form-control form-control-glass" wire:model.live="from_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Hasta</label>
                        <input type="date" class="form-control form-control-glass" wire:model.live="to_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Sucursal</label>
                        <select class="form-select form-control-glass" wire:model.live="branch_id"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50 fw-600 small">Buscar Producto</label>
                        <input type="text" class="form-control form-control-glass" placeholder="Marca, Modelo, Tipo..."
                            wire:model.live="search"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-outline-light w-100" onclick="downloadProductsPdf()"
                            style="border: 1px solid rgba(255, 255, 255, 0.2); color: white; font-weight: 600; transition: all 0.3s ease; cursor: pointer;"
                            title="Descargar PDF de Ventas">
                            <i class="fas fa-print me-2"></i> Ventas
                        </button>
                        <button class="btn w-100" onclick="downloadReplenishmentPdf()"
                            style="background: linear-gradient(135deg, #FF6B6B 0%, #ee0979 100%); color: white; border: none; font-weight: 600; transition: all 0.3s ease; cursor: pointer;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255, 107, 107, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                            title="Descargar Reporte Unificado de Reposición">
                            <i class="fas fa-file-pdf me-2"></i> Reposición
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
                    $summary = $this->getSummary();
                @endphp

                <!-- Total de Productos -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(102, 126, 234, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Productos Vendidos</p>
                                <h3 class="text-white fw-bold mb-0">{{ $summary['total_productos'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #667eea;">
                                <i class="fas fa-cube"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Unidades Vendidas -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(76, 201, 154, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Unidades Vendidas</p>
                                <h3 class="text-white fw-bold mb-0">{{ $summary['total_vendido'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #4cc99a;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Sugerido de Reposición -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(255, 193, 7, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-refresh"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Reposición Sugerida</p>
                                <h3 class="text-white fw-bold mb-0">{{ $summary['reposicion_total'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #ffc107;">
                                <i class="fas fa-dolly"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Disponibilidad -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(76, 201, 154, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">% Reputación</p>
                                <h3 class="text-white fw-bold mb-0">
                                    @php
                                        $totalVendido = $summary['total_vendido'];
                                        $reposicion = $summary['reposicion_total'];
                                        if ($totalVendido > 0) {
                                            $percentage = (($reposicion) / ($totalVendido + abs($reposicion))) * 100;
                                            echo number_format(max(0, $percentage), 1) . '%';
                                        } else {
                                            echo '0%';
                                        }
                                    @endphp
                                </h3>
                            </div>
                            <div style="font-size: 30px; color: #4cc99a;">
                                <i class="fas fa-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-table me-2"></i> Detalle de Productos con Movimiento
                </h5>

                @if(empty($products))
                    <div class="alert alert-info text-white" role="alert"
                        style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.3);">
                        <i class="fas fa-info-circle me-2"></i> No hay productos con movimiento en el período seleccionado.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table text-white" style="border: none;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;"
                                        wire:click="setSortField('marca')">
                                        Marca
                                        @if($sortField === 'marca')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;"
                                        wire:click="setSortField('modelo')">
                                        Modelo
                                        @if($sortField === 'modelo')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600;">Categoría</th>
                                    <th style="font-weight: 600;">Tipo</th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea; text-align: center;"
                                        wire:click="setSortField('stock_actual')">
                                        Stock Actual
                                        @if($sortField === 'stock_actual')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea; text-align: center;"
                                        wire:click="setSortField('total_vendido')">
                                        Cantidad Vendida
                                        @if($sortField === 'total_vendido')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea; text-align: center;"
                                        wire:click="setSortField('reposicion')">
                                        A Reponer
                                        @if($sortField === 'reposicion')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.05)';"
                                        onmouseout="this.style.backgroundColor='transparent';">
                                        <td class="fw-bold" style="color: #667eea;">{{ $product['marca'] }}</td>
                                        <td>{{ $product['modelo'] }}</td>
                                        <td>
                                            <span class="badge"
                                                style="background: rgba(76, 201, 154, 0.3); color: #4cc99a; padding: 0.5rem 0.75rem;">
                                                {{ ucfirst($product['categoria']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0.5rem 0.75rem;">
                                                {{ $product['tipo'] }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge"
                                                style="background: rgba(102, 126, 234, 0.3); color: #667eea; padding: 0.5rem 0.75rem;">
                                                {{ $product['stock_actual'] }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; color: #4cc99a; font-weight: 600;">
                                            {{ $product['total_vendido'] }} un.
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge"
                                                style="background: rgba(255, 193, 7, 0.3); color: #ffc107; padding: 0.5rem 0.75rem;">
                                                <i class="fas fa-redo me-1"></i>{{ $product['reposicion'] }} un.
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de página tabla -->
                    <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between text-white fw-bold mb-2">
                                    <span>Total de Productos:</span>
                                    <span style="color: #667eea;">{{ $summary['total_productos'] }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between text-white fw-bold">
                                    <span>Total Unidades Vendidas:</span>
                                    <span style="color: #4cc99a;">{{ $summary['total_vendido'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between text-white fw-bold">
                                    <span>Reposición Total Sugerida:</span>
                                    <span style="color: #ffc107;">{{ $summary['reposicion_total'] }} unidades</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function downloadProductsPdf() {
            const fromDate = @this.from_date;
            const toDate = @this.to_date;
            const branchId = @this.branch_id;

            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                branch_id: branchId
            });

            window.location.href = '{{ route("admin.reportes.pdf.productos") }}?' + params.toString();
        }

        function downloadReplenishmentPdf() {
            const fromDate = @this.from_date;
            const toDate = @this.to_date;
            const branchId = @this.branch_id;

            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                branch_id: branchId
            });

            window.location.href = '{{ route("admin.reportes.pdf.reposicion") }}?' + params.toString();
        }
    </script>
</div>