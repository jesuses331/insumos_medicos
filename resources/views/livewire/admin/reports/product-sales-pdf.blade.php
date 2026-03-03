<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas por Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-left: 4px solid #667eea;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        .summary-item {
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .summary-label {
            font-weight: bold;
            color: #667eea;
            font-size: 12px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background: #667eea;
            color: white;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        table tbody tr:hover {
            background: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-success {
            background: #4cc99a;
            color: white;
        }
        .badge-warning {
            background: #ffc107;
            color: white;
        }
        .badge-danger {
            background: #FF6B6B;
            color: white;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .footer-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .footer-item {
            padding: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📦 Reporte de Ventas por Producto</h1>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</p>
        <p><strong>Generado:</strong> {{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Resumen -->
    <div class="summary">
        <h2 style="margin: 0 0 15px 0; color: #667eea;">Resumen Ejecutivo</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Productos Vendidos</div>
                <div class="summary-value">{{ $summary['total_productos'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Unidades Vendidas</div>
                <div class="summary-value">{{ $summary['total_vendido'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Reposición Total</div>
                <div class="summary-value">{{ $summary['reposicion_total'] }}</div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <h2 style="color: #667eea; margin-top: 30px;">Detalle de Productos con Movimiento</h2>
    <table>
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Categoría</th>
                <th>Tipo</th>
                <th class="text-center">Stock Actual</th>
                <th class="text-center">Cantidad Vendida</th>
                <th class="text-center">A Reponer</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr>
                    <td><strong>{{ $product['marca'] }}</strong></td>
                    <td>{{ $product['modelo'] }}</td>
                    <td>{{ ucfirst($product['categoria']) }}</td>
                    <td>{{ $product['tipo'] }}</td>
                    <td class="text-center">{{ $product['stock_actual'] }}</td>
                    <td class="text-center">{{ $product['total_vendido'] }} un.</td>
                    <td class="text-center">
                        @if($product['reposicion'] > 0)
                            <span class="badge badge-warning">↑ {{ $product['reposicion'] }}</span>
                        @elseif($product['reposicion'] == 0)
                            <span class="badge badge-success">✓ OK</span>
                        @else
                            <span class="badge badge-danger">⚠ Exceso</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">No hay productos con movimiento</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Totales -->
    <div style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-left: 4px solid #667eea;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div>
                <div style="font-weight: bold; color: #667eea; font-size: 12px;">TOTAL PRODUCTOS</div>
                <div style="font-size: 20px; font-weight: bold; color: #333; margin-top: 5px;">{{ $summary['total_productos'] }}</div>
            </div>
            <div>
                <div style="font-weight: bold; color: #667eea; font-size: 12px;">UNIDADES VENDIDAS</div>
                <div style="font-size: 20px; font-weight: bold; color: #4cc99a; margin-top: 5px;">{{ $summary['total_vendido'] }}</div>
            </div>
            <div>
                <div style="font-weight: bold; color: #667eea; font-size: 12px;">REPOSICIÓN SUGERIDA</div>
                <div style="font-size: 20px; font-weight: bold; color: #ffc107; margin-top: 5px;">{{ $summary['reposicion_total'] }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-info">
            <div class="footer-item">
                <strong>Sistema:</strong> Panel de Reportes Avanzado
            </div>
            <div class="footer-item">
                <strong>Usuario:</strong> {{ auth()->user()->name ?? 'N/A' }}
            </div>
            <div class="footer-item">
                <strong>Fecha de Descarga:</strong> {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
