<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Movimientos</title>
    <style>
        @page { size: legal landscape; margin: 1cm; }
        body { font-family: Arial, sans-serif; font-size: 8pt; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 14pt; font-weight: bold; color: #4A90E2; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4A90E2; color: white; padding: 8px 4px; text-align: center; border: 1px solid #333; font-size: 8pt; }
        td { padding: 5px 4px; border: 1px solid #999; text-align: center; font-size: 7pt; }
        .badge-entrada { background: #10B981; color: white; padding: 2px 8px; border-radius: 3px; }
        .badge-salida { background: #EF4444; color: white; padding: 2px 8px; border-radius: 3px; }
        .text-right { text-align: right; }
        .summary-box { background: #f8f9fa; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">REPORTE DE MOVIMIENTOS DE INVENTARIO</div>
        <div style="margin-top: 5px;">
            Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    <div class="summary-box">
        <strong>Total Entradas:</strong> ${{ number_format($totalEntradas, 2) }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Salidas:</strong> ${{ number_format($totalSalidas, 2) }} &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Total Movimientos:</strong> {{ $movimientos->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">FECHA</th>
                <th style="width: 8%;">TIPO</th>
                <th style="width: 10%;">CÓDIGO</th>
                <th style="width: 30%;">MATERIAL</th>
                <th style="width: 6%;">UND</th>
                <th style="width: 10%;">CANTIDAD</th>
                <th style="width: 10%;">C. UNIT.</th>
                <th style="width: 10%;">TOTAL</th>
                <th style="width: 8%;">N° DOC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
            <tr>
                <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                <td>
                    <span class="{{ $mov->tipo_movimiento === 'entrada' ? 'badge-entrada' : 'badge-salida' }}">
                        {{ strtoupper($mov->tipo_movimiento) }}
                    </span>
                </td>
                <td><strong>{{ $mov->material->codigo }}</strong></td>
                <td style="text-align: left; padding-left: 8px;">{{ strtoupper($mov->material->nombre) }}</td>
                <td>{{ $mov->material->unidadMedida->abreviatura }}</td>
                <td class="text-right">{{ number_format($mov->cantidad, 2) }}</td>
                <td class="text-right">{{ number_format($mov->costo_unitario, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($mov->total, 2) }}</strong></td>
                <td style="font-size: 6pt;">{{ $mov->numero_factura ?? $mov->numero_ingreso ?? $mov->numero_salida ?? '-' }}</td>
            </tr>
            @if($mov->unidad_solicitante)
            <tr>
                <td colspan="9" style="text-align: left; padding-left: 10px; font-size: 6pt; font-style: italic;">
                    Solicitante: {{ $mov->unidad_solicitante }}
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 7pt; color: #666;">
        Generado el {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>