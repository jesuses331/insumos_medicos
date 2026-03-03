@extends('layouts.plantilla')

@section('titulo', 'Tablero')
@section('encabezado', 'Resumen Global')

@section('contenido')
    <div class="row g-4">
        <!-- Card Ventas Hoy -->
        <div class="col-md-4">
            <div class="glass-card stat-card text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-cash-register fa-3x text-primary opacity-50"></i>
                </div>
                <h5 class="text-white-50 mb-2">Ventas de Hoy</h5>
                <h2 class="fw-bold m-0 text-white">Bs {{ number_format($ventasHoy, 2) }}</h2>
            </div>
        </div>

        <!-- Card Ventas Mes -->
        <div class="col-md-4">
            <div class="glass-card stat-card text-center p-4" style="border-left: 4px solid #764ba2;">
                <div class="mb-3">
                    <i class="fas fa-calendar-check fa-3x text-purple opacity-50"></i>
                </div>
                <h5 class="text-white-50 mb-2">Ventas del Mes</h5>
                <h2 class="fw-bold m-0 text-white">Bs {{ number_format($ventasMes, 2) }}</h2>
            </div>
        </div>

        <!-- Card Productos -->
        <div class="col-md-4">
            <div class="glass-card stat-card text-center p-4" style="border-left: 4px solid #ff9a9e;">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-3x text-danger opacity-50"></i>
                </div>
                <h5 class="text-white-50 mb-2">Total Productos</h5>
                <h2 class="fw-bold m-0 text-white">{{ $totalProductos }}</h2>
            </div>
        </div>
    </div>

    <!-- Sección de Gráficos -->
    <div class="row g-4 mt-4">
        <!-- Gráfica de Líneas: Ventas Mensuales del Año -->
        <div class="col-lg-8">
            <div class="glass-card p-4">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-chart-line me-2"></i> Ventas Mensuales - {{ now()->year }}
                </h5>
                @if(count($monthlyRevenue['data']) > 0)
                    <div style="position: relative; height: 350px;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                @else
                    <div class="alert alert-info text-white" role="alert"
                        style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.3);">
                        <i class="fas fa-info-circle me-2"></i> No hay datos de ventas disponibles.
                    </div>
                @endif
            </div>
        </div>

        <!-- Gráfica de Pastel: Pantallas Más Vendidas -->
        <div class="col-lg-4">
            <div class="glass-card p-4">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-chart-pie me-2"></i> Top Pantallas - {{ now()->year }}
                </h5>
                @if(count($topScreens['data']) > 0)
                    <div style="position: relative; height: 350px;">
                        <canvas id="topScreensChart"></canvas>
                    </div>
                @else
                    <div class="alert alert-info text-white" role="alert"
                        style="background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.3);">
                        <i class="fas fa-info-circle me-2"></i> No hay pantallas vendidas en este año.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alertas de Stock Bajo -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="glass-card p-4 h-100" style="border-left: 4px solid #ef4444;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-white fw-bold m-0">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i> Alertas de Stock Bajo
                    </h5>
                    <a href="{{ route('admin.productos.inicio') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        Ver todo el inventario
                    </a>
                </div>

                @if($lowStockProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead class="small text-uppercase text-white-50">
                                <tr>
                                    <th class="border-0">Producto</th>
                                    <th class="border-0 text-center">Categoría</th>
                                    <th class="border-0 text-center">Stock Actual</th>
                                    <th class="border-0 text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="text-white-50">
                                @foreach($lowStockProducts as $lp)
                                    @php
                                        $lpStock = $lp->sucursales->first()->pivot->stock ?? 0;
                                    @endphp
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="py-3">
                                            <div class="fw-bold text-white">{{ $lp->marca->nombre }} {{ $lp->modelo }}</div>
                                            <div class="small opacity-50">{{ $lp->tipoRepuesto->nombre ?? 'Estándar' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill px-2 py-1"
                                                style="font-size: 0.6rem; background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.1);">
                                                {{ $lp->categoria->nombre }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold {{ $lpStock <= 2 ? 'text-danger' : 'text-warning' }}">
                                                {{ $lpStock }} unidades
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.productos.editar', $lp->id) }}"
                                                class="btn btn-sm btn-premium p-2 rounded-circle"
                                                style="width:30px; height:30px; display:inline-flex; align-items:center; justify-content:center;">
                                                <i class="fas fa-edit small"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success display-4 mb-3 opacity-25"></i>
                        <p class="text-white-50">Todo el stock está en niveles óptimos.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-5">
        <div class="glass-card">
            <h5 class="fw-bold mb-4"><i class="fas fa-rocket me-2"></i> Acciones Rápidas</h5>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('admin.usuarios.inicio') }}" class="btn btn-premium">
                    <i class="fas fa-plus me-2"></i> Nuevo Usuario
                </a>
                <a href="{{ route('admin.roles.inicio') }}" class="btn btn-outline-dark rounded-pill px-4">
                    <i class="fas fa-shield-alt me-2"></i> Gestionar Seguridad
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Configuración global de Chart.js
        Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

        // Gráfica de Líneas: Ventas Mensuales del Año
        const monthlyCtx = document.getElementById('monthlyRevenueChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyRevenue['labels']) !!},
                    datasets: [{
                        label: 'Ingresos Mensuales ($)',
                        data: {!! json_encode($monthlyRevenue['data']) !!},
                        borderColor: 'rgba(102, 126, 234, 1)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 7,
                        hoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 28, 45, 0.9)',
                            borderColor: 'rgba(102, 126, 234, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            displayColors: true,
                            callbacks: {
                                label: function (context) {
                                    return 'Ingresos: Bs ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)',
                                drawBorder: true
                            },
                            ticks: {
                                callback: function (value) {
                                    return 'Bs ' + value.toFixed(0);
                                }
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            }
                        }
                    }
                }
            });
        }

        // Gráfica de Pastel: Pantallas Más Vendidas
        const topScreensCtx = document.getElementById('topScreensChart');
        if (topScreensCtx) {
            const colors = [
                'rgba(102, 126, 234, 0.8)',
                'rgba(76, 201, 154, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(244, 67, 54, 0.8)',
                'rgba(156, 39, 176, 0.8)',
                'rgba(0, 188, 212, 0.8)',
                'rgba(233, 30, 99, 0.8)',
                'rgba(63, 81, 181, 0.8)',
                'rgba(76, 175, 80, 0.8)',
                'rgba(255, 87, 34, 0.8)',
            ];

            new Chart(topScreensCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($topScreens['labels']) !!},
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: {!! json_encode($topScreens['data']) !!},
                        backgroundColor: colors.slice(0, {!! json_encode(count($topScreens['labels'])) !!}),
                        borderColor: 'rgba(26, 28, 45, 0.9)',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 28, 45, 0.9)',
                            borderColor: 'rgba(102, 126, 234, 0.5)',
                            borderWidth: 1,
                            padding: 12,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            displayColors: true,
                            callbacks: {
                                label: function (context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' unidades (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection