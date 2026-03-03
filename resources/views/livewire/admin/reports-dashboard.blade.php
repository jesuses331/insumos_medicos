
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-white mb-2">
                        <i class="fas fa-chart-line me-2"></i> Panel de Reportes Avanzado
                    </h1>
                    <p class="text-muted mb-0">Análisis detallado de ventas y traslados logísticos</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acceso a Reportes Separados -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; font-weight: 600; padding: 1rem; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.4)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-receipt"></i> Reporte de Ventas
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.reportes.traslados') }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #4cc99a 0%, #2da88a 100%); color: white; border: none; font-weight: 600; padding: 1rem; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(76, 201, 154, 0.4)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-exchange-alt"></i> Reporte de Traslados
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.reportes.productos') }}" class="btn btn-lg w-100" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; border: none; font-weight: 600; padding: 1rem; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255, 107, 107, 0.4)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                        <i class="fas fa-cube"></i> Reporte de Productos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal: Panel de Acceso Rápido -->
    <div class="row">
        <div class="col-12">
            <div class="glass-card p-6 rounded-xl">
                <h5 class="text-white fw-bold mb-4">
                    <i class="fas fa-dashboard me-2"></i> Acceso a Reportes Detallados
                </h5>
                <p class="text-white-50 mb-0">
                    Elige entre tres tipos de reportes especializados: <strong>Ventas</strong> (resumen de transacciones), <strong>Traslados</strong> (movimiento entre sucursales y estado del kardex) y <strong>Productos</strong> (análisis de productos con movimiento y sugerencias de reposición).
                </p>
            </div>
        </div>
    </div>

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</div>
</div>
 