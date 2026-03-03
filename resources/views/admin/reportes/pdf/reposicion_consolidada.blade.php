<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Reposición Consolidado</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        .header {
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #667eea;
            font-size: 24px;
        }

        .info-section {
            margin-bottom: 20px;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #f4f7ff;
            color: #4a5568;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
            font-size: 12px;
        }

        .section-title {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .badge-qty {
            background: #edf2f7;
            color: #2d3748;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>REPORTE DE REPOSICIÓN</h1>
    </div>

    <div class="info-section">
        <p><b>Fecha Generación:</b> {{ $generatedAt->format('d/m/Y H:i') }}</p>
        <p><b>Período:</b> {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d/m/Y') : 'Inicio' }} al
            {{ $toDate ? \Carbon\Carbon::parse($toDate)->format('d/m/Y') : 'Hoy' }}</p>
        <p><b>Sucursal:</b> {{ $branch ? $branch->nombre : 'Todas las Sucursales' }}</p>
    </div>

    <!-- SECCIÓN 1: PRODUCTOS VENDIDOS PARA REPONER -->
    <div class="section-title">1. Productos Vendidos (Para Reponer)</div>
    <table>
        <thead>
            <tr>
                <th>Producto / Modelo</th>
                <th style="text-align: center; width: 100px;">Cant. Vendida</th>
                <th>Categoría</th>
            </tr>
        </thead>
        <tbody>
            @forelse($soldProducts as $product)
                <tr>
                    <td><b>{{ $product['name'] }}</b></td>
                    <td style="text-align: center;"><span class="badge-qty">{{ $product['quantity'] }}</span></td>
                    <td>{{ ucfirst($product['category']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #a0aec0; padding: 20px;">No se registraron ventas en
                        este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECCIÓN 2: PRODUCTOS DEFECTUOSOS PARA REPONER -->
    <div class="section-title">2. Productos Defectuosos (Para Reemplazar)</div>
    <table>
        <thead>
            <tr>
                <th>Producto / Modelo</th>
                <th style="text-align: center; width: 100px;">Cant. Defectos</th>
                <th>Detalles / Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($defectiveProducts as $product)
                <tr>
                    <td><b>{{ $product['name'] }}</b></td>
                    <td style="text-align: center;"><span class="badge-qty"
                            style="color: #e53e3e;">{{ $product['quantity'] }}</span></td>
                    <td style="color: #718096; font-style: italic;">{{ $product['details'] ?: 'Sin observaciones' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #a0aec0; padding: 20px;">No se registraron productos
                        defectuosos en este período.</td>
                </tr>
            @endforelse
    </table>

    <div style="margin-top: 30px; border-top: 1px dashed #cbd5e0; padding-top: 15px; font-size: 12px; color: #4a5568;">
        <p><b>Resumen Total de Reposición:</b></p>
        <ul>
            <li>Unidades por ventas: <b>{{ $soldProducts->sum('quantity') }}</b></li>
            <li>Unidades por defectos: <b>{{ $defectiveProducts->sum('quantity') }}</b></li>
            <li>Total Unidades a Reponer: <b
                    style="font-size: 16px; color: #667eea;">{{ $soldProducts->sum('quantity') + $defectiveProducts->sum('quantity') }}</b>
            </li>
        </ul>
    </div>

    <div class="footer">
        Este documento es una guía para la reposición de inventario. - Generado por Sistema POS Elite
    </div>
</body>

</html>