<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #667eea;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .company-info h1 {
            color: #1a1c2d;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #666;
            font-size: 12px;
        }

        .report-title {
            text-align: right;
            color: #667eea;
            font-size: 18px;
            font-weight: bold;
        }

        /* Report Info */
        .report-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .info-item {
            flex: 1;
        }

        .info-label {
            color: #666;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .info-value {
            color: #1a1c2d;
            font-weight: 500;
        }

        /* Resumen Cards */
        .summary {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-card {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-card.success {
            background: linear-gradient(135deg, #4cc99a 0%, #2ab881 100%);
        }

        .summary-card label {
            display: block;
            font-size: 11px;
            opacity: 0.85;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .value {
            font-size: 22px;
            font-weight: bold;
        }

        /* Table */
        .table-container {
            margin-bottom: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #f0f0f0;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #1a1c2d;
            border-bottom: 2px solid #667eea;
            font-size: 12px;
            text-transform: uppercase;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
            color: #333;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tbody tr:hover {
            background: #f0f0f0;
        }

        .amount {
            text-align: right;
            font-weight: 500;
            color: #667eea;
        }

        /* Footer */
        .footer {
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            margin-top: 30px;
        }

        .footer-summary {
            display: flex;
            justify-content: flex-end;
            gap: 40px;
            margin-bottom: 20px;
        }

        .footer-item {
            text-align: right;
        }

        .footer-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .footer-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a1c2d;
        }

        .footer-info {
            text-align: center;
            font-size: 10px;
            color: #999;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="company-info">
                    <h1>Panel de Ventas</h1>
                    <p>Sucursal: <strong>{{ $branch->nombre ?? 'Todas' }}</strong></p>
                </div>
                <div class="report-title">
                    REPORTE DE VENTAS
                </div>
            </div>
        </div>

        <!-- Report Info -->
        <div class="report-info">
            <div class="info-item">
                <div class="info-label">Período:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Total de Ventas:</div>
                <div class="info-value">{{ count($sales) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha de Generación:</div>
                <div class="info-value">{{ $generatedAt->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary">
            <div class="summary-card">
                <label>Total Vendido</label>
                <div class="value">Bs {{ number_format($totalSold, 2) }}</div>
            </div>
            <div class="summary-card success">
                <label>Ganancia Estimada</label>
                <div class="value">Bs {{ number_format($estimatedProfit, 2) }}</div>
            </div>
            <div class="summary-card">
                <label>Margen de Ganancia</label>
                <div class="value">
                    {{ $totalSold > 0 ? number_format(($estimatedProfit / $totalSold) * 100, 1) : 0 }}%
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <h3 style="color: #1a1c2d; margin-bottom: 15px; font-size: 14px; text-transform: uppercase;">Detalle de
                Ventas</h3>
            @if(!empty($sales))
                <table>
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Caja</th>
                            <th>Método de Pago</th>
                            <th>Productos</th>
                            <th class="amount">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>#{{ $sale->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($sale->fecha)->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->client_name ?? 'Venta General' }}</td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                <td>{{ $sale->cashRegister->nombre ?? ($sale->cashRegister->id ?? 'N/A') }}</td>
                                <td>{{ $sale->payment_method }}</td>
                                <td>{{ count($sale->details) }}</td>
                                <td class="amount">Bs {{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No hay ventas registradas en el período
                    seleccionado.</p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-summary">
                <div class="footer-item">
                    <div class="footer-label">TOTAL GENERAL:</div>
                    <div class="footer-value">Bs {{ number_format($totalSold, 2) }}</div>
                </div>
                <div class="footer-item">
                    <div class="footer-label">GANANCIA ESTIMADA:</div>
                    <div class="footer-value">Bs {{ number_format($estimatedProfit, 2) }}</div>
                </div>
            </div>
            <div class="footer-info">
                <p>Este reporte fue generado automáticamente el {{ $generatedAt->format('d/m/Y') }} a las
                    {{ $generatedAt->format('H:i:s') }}
                </p>
                <p>© {{ date('Y') }} Panel de Ventas - Todos los derechos reservados</p>
            </div>
        </div>
    </div>
</body>

</html>