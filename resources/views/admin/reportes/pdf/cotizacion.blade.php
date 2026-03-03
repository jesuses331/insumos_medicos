<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cotización #{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .company-info {
            float: left;
        }

        .quotation-info {
            float: right;
            text-align: right;
        }

        .client-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #667eea;
            color: white;
            text-align: left;
            padding: 10px;
            font-size: 14px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        .total-box {
            float: right;
            width: 250px;
            background: #f4f7ff;
            padding: 15px;
            border-radius: 8px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .grand-total {
            font-size: 18px;
            font-bold: true;
            color: #2d3748;
        }

        .btn-wa {
            background: #25d366;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-info">
            <h2 style="margin: 0; color: #667eea;">POS ELITE</h2>
            <p style="margin: 5px 0; font-size: 12px;">
                {{ $quotation->sucursal->nombre }}<br>{{ $quotation->sucursal->direccion }}
            </p>
        </div>
        <div class="quotation-info">
            <h2 style="margin: 0; color: #4a5568;">COTIZACIÓN</h2>
            <p style="margin: 5px 0; font-size: 14px;"><b>Folio:</b>
                #{{ str_pad($quotation->id, 5, '0', STR_PAD_LEFT) }}<br><b>Fecha:</b>
                {{ $quotation->created_at->format('d/m/Y') }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="client-box">
        <p style="margin: 0; font-size: 12px; color: #718096; text-transform: uppercase; font-weight: bold;">Cliente</p>
        <p style="margin: 5px 0; font-size: 16px; font-weight: bold;">{{ $quotation->client_name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th style="text-align: center;">Cantidad</th>
                <th style="text-align: right;">Precio Unitario</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->details as $detail)
                <tr>
                    <td>
                        <b>{{ $detail->product->marca->nombre }}</b><br>
                        <span
                            style="color: #718096; font-size: 11px;">{{ $detail->product->modelo }}{{ $detail->product->tipoRepuesto ? ' (' . $detail->product->tipoRepuesto->nombre . ')' : '' }}</span>
                    </td>
                    <td style="text-align: center;">{{ $detail->cantidad }}</td>
                    <td style="text-align: right;">Bs {{ number_format($detail->precio_unitario, 2) }}</td>
                    <td style="text-align: right;"><b>Bs
                            {{ number_format($detail->cantidad * $detail->precio_unitario, 2) }}</b></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <div style="text-align: right;">
            <p style="margin: 5px 0; font-size: 14px; color: #718096;">Total Estimado</p>
            <p class="grand-total">Bs {{ number_format($quotation->total, 2) }}</p>
        </div>
    </div>

    <div style="margin-top: 100px; font-size: 12px; color: #718096;">
        <p><b>Notas:</b><br>
            - Esta cotización tiene una validez de 7 días naturales.<br>
            - Los precios pueden variar sin previo aviso hasta la confirmación del pedido.</p>
    </div>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} - Sistema POS Elite
    </div>
</body>

</html>