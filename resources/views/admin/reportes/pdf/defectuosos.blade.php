<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Productos Defectuosos</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }

        .header {
            border-bottom: 2px solid #e53e3e;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #e53e3e;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #fff5f5;
            color: #c53030;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #feb2b2;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #fed7d7;
            font-size: 12px;
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
        <h1>REPORTE DE PRODUCTOS DEFECTUOSOS</h1>
    </div>

    <div class="info-section" style="margin-bottom: 20px; font-size: 14px;">
        <p><b>Fecha Generación:</b> {{ $generatedAt->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Sucursal</th>
                <th style="text-align: center;">Cantidad</th>
                <th>Detalle / Falla</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($defectuosos as $item)
                <tr>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td><b>{{ $item->product->marca->nombre }}
                            {{ $item->product->modelo }}{{ $item->product->tipoRepuesto ? ' (' . $item->product->tipoRepuesto->nombre . ')' : '' }}</b>
                    </td>
                    <td>{{ $item->sucursal->nombre }}</td>
                    <td style="text-align: center;"><b>{{ $item->cantidad }}</b></td>
                    <td style="color: #4a5568;">{{ $item->detalle }}</td>
                    <td>{{ $item->estado }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #a0aec0; padding: 20px;">No hay productos defectuosos
                        reportados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado por Sistema POS Elite - Reporte de Mermas y Defectos
    </div>
</body>

</html>