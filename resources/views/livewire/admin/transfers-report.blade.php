<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-white mb-2">
                        <i class="fas fa-exchange-alt me-2"></i> Reporte de Traslados (Kardex)
                    </h1>
                    <p class="text-muted mb-0">Análisis detallado de traslados logísticos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-filter me-2"></i> Filtros de Traslados
                </h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Desde</label>
                        <input type="date" class="form-control form-control-glass" wire:model.live="transfers_from_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Hasta</label>
                        <input type="date" class="form-control form-control-glass" wire:model.live="transfers_to_date"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">De Sucursal</label>
                        <select class="form-select form-control-glass" wire:model.live="transfers_from_branch"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">A Sucursal</label>
                        <select class="form-select form-control-glass" wire:model.live="transfers_to_branch"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-white-50 fw-600 small">Estado</label>
                        <select class="form-select form-control-glass" wire:model.live="transfers_status"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                            <option value="">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Enviado">Enviado</option>
                            <option value="Recibido">Recibido</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <input type="text" class="form-control form-control-glass" placeholder="ID, Usuario..."
                            wire:model.live="search"
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); color: white;">
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-12 d-flex gap-2">
                        <button class="btn" onclick="downloadTransfersPdf()"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; transition: all 0.3s ease; padding: 0.5rem 1.5rem; cursor: pointer;"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.4)';"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                            title="Descargar PDF">
                            <i class="fas fa-print me-2"></i> Imprimir PDF
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
                    $transfersSummary = $this->getTransfersSummary();
                @endphp

                <!-- Total de Traslados -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(102, 126, 234, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Total de Traslados</p>
                                <h3 class="text-white fw-bold mb-0">{{ $transfersSummary['totalTransfers'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #667eea;">
                                <i class="fas fa-dolly"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total de Productos -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(76, 201, 154, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Productos Trasladados</p>
                                <h3 class="text-white fw-bold mb-0">{{ $transfersSummary['totalItems'] }}</h3>
                            </div>
                            <div style="font-size: 30px; color: #4cc99a;">
                                <i class="fas fa-cube"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enviados -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(255, 193, 7, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Enviados</p>
                                <h3 class="text-white fw-bold mb-0">{{ $transfersSummary['byStatus']['Enviado'] ?? 0 }}
                                </h3>
                            </div>
                            <div style="font-size: 30px; color: #ffc107;">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recibidos -->
                <div class="col-md-3">
                    <div class="glass-card p-4 rounded-xl h-100 position-relative overflow-hidden"
                        style="border: 1px solid rgba(76, 201, 154, 0.2);">
                        <div style="position: absolute; top: -20px; right: -20px; font-size: 60px; opacity: 0.1;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-2">Recibidos</p>
                                <h3 class="text-white fw-bold mb-0">{{ $transfersSummary['byStatus']['Recibido'] ?? 0 }}
                                </h3>
                            </div>
                            <div style="font-size: 30px; color: #4cc99a;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Traslados (DataTable) -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-table me-2"></i> Kardex Logístico
                </h5>

                @if(empty($transfers))
                    <div class="alert alert-info text-white" role="alert"
                        style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.3);">
                        <i class="fas fa-info-circle me-2"></i> No hay traslados registrados en el período seleccionado.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table text-white" style="border: none;">
                            <thead>
                                <tr style="border-bottom: 2px solid rgba(255, 255, 255, 0.1);">
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;"
                                        wire:click="setSortField('id')">
                                        ID
                                        @if($sortField === 'id')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600; cursor: pointer; color: #667eea;"
                                        wire:click="setSortField('fecha')">
                                        Fecha
                                        @if($sortField === 'fecha')
                                            <i
                                                class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }} ms-2"></i>
                                        @endif
                                    </th>
                                    <th style="font-weight: 600;">De Sucursal</th>
                                    <th style="font-weight: 600;">A Sucursal</th>
                                    <th style="font-weight: 600;">Productos</th>
                                    <th style="font-weight: 600;">Usuario</th>
                                    <th style="font-weight: 600;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='rgba(255, 255, 255, 0.05)';"
                                        onmouseout="this.style.backgroundColor='transparent';">
                                        <td class="fw-bold" style="color: #667eea;">{{ $transfer['id'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transfer['fecha'])->format('d/m/Y H:i') }}</td>
                                        <td>{{ $transfer['from_sucursal']['nombre'] ?? 'N/A' }}</td>
                                        <td>{{ $transfer['to_sucursal']['nombre'] ?? 'N/A' }}</td>
                                        <td>{{ count($transfer['details']) }}
                                            producto{{ count($transfer['details']) !== 1 ? 's' : '' }}</td>
                                        <td class="text-white-50">{{ $transfer['usuario'] ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Pendiente' => '#ffc107',
                                                    'Enviado' => '#667eea',
                                                    'Recibido' => '#4cc99a',
                                                    'Cancelado' => '#FF6B6B'
                                                ];
                                                $color = $statusColors[$transfer['status']] ?? '#7f8fa4';
                                            @endphp
                                            <span class="badge" style="background-color: {{ $color }};">
                                                {{ $transfer['status'] }}
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
                                    <span>Total de Traslados:</span>
                                    <span style="color: #667eea;">{{ $transfersSummary['totalTransfers'] }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between text-white fw-bold">
                                    <span>Total de Productos:</span>
                                    <span style="color: #4cc99a;">{{ $transfersSummary['totalItems'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function downloadTransfersPdf() {
            const fromDate = @this.transfers_from_date;
            const toDate = @this.transfers_to_date;
            const fromBranch = @this.transfers_from_branch;
            const toBranch = @this.transfers_to_branch;
            const status = @this.transfers_status;

            const params = new URLSearchParams({
                from_date: fromDate,
                to_date: toDate,
                from_branch: fromBranch,
                to_branch: toBranch,
                status: status
            });

            window.location.href = '{{ route("admin.reportes.pdf.traslados") }}?' + params.toString();
        }
    </script>
</div>