<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex - {{ $material->nombre }}</title>
    <style>
        @page {
            size: legal landscape;
            margin: 0.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header {
            text-align: center;
            background: #4A90E2;
            color: white;
            padding: 8px;
            border: 2px solid #333;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
        }

        .header h2 {
            font-size: 11pt;
            margin: 3px 0 0 0;
            font-weight: normal;
        }

        .info-section {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #333;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        .info-section table {
            width: 100%;
        }

        .info-section td {
            padding: 2px 5px;
        }

        .info-section strong {
            font-weight: bold;
        }

        .kardex-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        .kardex-table th {
            background: #4A90E2;
            color: white;
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #333;
            font-weight: bold;
            vertical-align: middle;
        }

        .kardex-table .group-header {
            background: #2C5F8D;
            font-weight: bold;
            font-size: 8pt;
        }

        .kardex-table td {
            padding: 3px 2px;
            border: 1px solid #666;
            text-align: center;
            font-size: 7pt;
            vertical-align: middle;
        }

        .row-entrada {
            background: rgba(16, 185, 129, 0.15);
        }

        .row-salida {
            background: rgba(239, 68, 68, 0.15);
        }

        .row-saldo-inicial {
            background: #fff3cd;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
            padding-left: 8px;
        }

        .text-bold {
            font-weight: bold;
        }

        .empty-cell {
            background: #f5f5f5;
        }

        .footer {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            padding: 0 15px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 8pt;
        }

        .generated-date {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- ENCABEZADO --}}
        <div class="header">
            <h1>TARJETA KARDEX - CONTROL DE INVENTARIO {{ date('Y') }}</h1>
            <h2>{{ strtoupper($material->nombre) }}</h2>
        </div>

        {{-- INFORMACIÓN DEL MATERIAL --}}
        <div class="info-section">
            <table>
                <tr>
                    <td width="25%"><strong>Código:</strong> {{ $material->codigo }}</td>
                    <td width="25%"><strong>Categoría:</strong> {{ $material->subcategoria->categoria->nombre }}</td>
                    <td width="25%"><strong>Subcategoría:</strong> {{ $material->subcategoria->nombre }}</td>
                    <td width="25%"><strong>U. Medida:</strong> {{ $material->unidadMedida->nombre }} ({{ $material->unidadMedida->abreviatura }})</td>
                </tr>
                @if($material->descripcion)
                <tr>
                    <td colspan="4"><strong>Descripción:</strong> {{ $material->descripcion }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- TABLA KARDEX --}}
        <table class="kardex-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 6%;">FECHA</th>
                    <th rowspan="2" style="width: 7%;">N° DOC</th>
                    <th colspan="4" class="group-header">ENTRADA</th>
                    <th colspan="4" class="group-header">SALIDA</th>
                    <th colspan="3" class="group-header">SALDOS</th>
                </tr>
                <tr>
                    {{-- ENTRADA --}}
                    <th style="width: 4%;">UND</th>
                    <th style="width: 6%;">CANT.</th>
                    <th style="width: 5%;">C.U.</th>
                    <th style="width: 6%;">TOTAL</th>
                    
                    {{-- SALIDA --}}
                    <th style="width: 4%;">UND</th>
                    <th style="width: 6%;">CANT.</th>
                    <th style="width: 5%;">C.U.</th>
                    <th style="width: 6%;">TOTAL</th>
                    
                    {{-- SALDOS --}}
                    <th style="width: 6%;">CANT.</th>
                    <th style="width: 5%;">C.U.</th>
                    <th style="width: 6%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                {{-- SALDO INICIAL --}}
                @php
                    $primerMovimiento = $movimientos->first();
                    
                    if ($primerMovimiento) {
                        $cantidadInicial = $primerMovimiento->tipo_movimiento === 'entrada' 
                            ? $primerMovimiento->saldo_cantidad - $primerMovimiento->cantidad
                            : $primerMovimiento->saldo_cantidad + $primerMovimiento->cantidad;
                        $costoInicial = $primerMovimiento->tipo_movimiento === 'entrada'
                            ? $primerMovimiento->saldo_costo_total - $primerMovimiento->total
                            : $primerMovimiento->saldo_costo_total + $primerMovimiento->total;
                    } else {
                        $cantidadInicial = $material->detalleMaterial->cantidad_actual ?? 0;
                        $costoInicial = $material->detalleMaterial->costo_total ?? 0;
                    }
                    $precioUnitInicial = $cantidadInicial > 0 ? $costoInicial / $cantidadInicial : 0;
                @endphp
                <tr class="row-saldo-inicial">
                    <td>{{ now()->startOfYear()->format('d/m/Y') }}</td>
                    <td colspan="5" class="text-bold">SALDO INICIAL</td>
                    <td colspan="4" class="empty-cell"></td>
                    <td class="text-bold">{{ number_format($cantidadInicial, 2) }}</td>
                    <td>{{ number_format($precioUnitInicial, 2) }}</td>
                    <td class="text-bold">{{ number_format($costoInicial, 2) }}</td>
                </tr>

                {{-- MOVIMIENTOS --}}
                @foreach($movimientos as $mov)
                <tr class="{{ $mov->tipo_movimiento === 'entrada' ? 'row-entrada' : 'row-salida' }}">
                    <td>{{ $mov->fecha->format('d/m/Y') }}</td>
                    <td style="font-size: 6pt;">
                        @if($mov->tipo_movimiento === 'entrada')
                            {{ $mov->numero_factura ? 'F:' . $mov->numero_factura : ($mov->numero_ingreso ? 'I:' . $mov->numero_ingreso : '-') }}
                        @else
                            {{ 'S:' . ($mov->numero_salida ?? '-') }}
                        @endif
                    </td>
                    
                    @if($mov->tipo_movimiento === 'entrada')
                        {{-- ENTRADA --}}
                        <td>{{ $material->unidadMedida->abreviatura }}</td>
                        <td class="text-bold">{{ number_format($mov->cantidad, 2) }}</td>
                        <td>{{ number_format($mov->costo_unitario, 2) }}</td>
                        <td class="text-bold">{{ number_format($mov->total, 2) }}</td>
                        {{-- SALIDA vacía --}}
                        <td colspan="4" class="empty-cell"></td>
                    @else
                        {{-- ENTRADA vacía --}}
                        <td colspan="4" class="empty-cell"></td>
                        {{-- SALIDA --}}
                        <td>{{ $material->unidadMedida->abreviatura }}</td>
                        <td class="text-bold">{{ number_format($mov->cantidad, 2) }}</td>
                        <td>{{ number_format($mov->costo_unitario, 2) }}</td>
                        <td class="text-bold">{{ number_format($mov->total, 2) }}</td>
                    @endif
                    
                    {{-- SALDOS --}}
                    <td class="text-bold">{{ number_format($mov->saldo_cantidad, 2) }}</td>
                    <td>{{ number_format($mov->saldo_cantidad > 0 ? $mov->saldo_costo_total / $mov->saldo_cantidad : 0, 2) }}</td>
                    <td class="text-bold">{{ number_format($mov->saldo_costo_total, 2) }}</td>
                </tr>
                
                @if($mov->unidad_solicitante && $mov->tipo_movimiento === 'salida')
                <tr class="row-salida">
                    <td colspan="13" class="text-left" style="font-size: 6pt; font-style: italic;">
                        <strong>Solicitante:</strong> {{ $mov->unidad_solicitante }}
                        @if($mov->observaciones)
                            | <strong>Obs:</strong> {{ Str::limit($mov->observaciones, 60) }}
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach

                @if($movimientos->isEmpty())
                <tr>
                    <td colspan="13" style="padding: 15px; color: #999;">
                        No hay movimientos registrados para este material
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- FIRMAS --}}
        <div class="footer">
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>Responsable de Almacén</strong>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>Jefe de Área</strong>
                    </div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">
                        <strong>Gerencia General</strong>
                    </div>
                </div>
            </div>
            <div class="generated-date">
                Generado el {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>