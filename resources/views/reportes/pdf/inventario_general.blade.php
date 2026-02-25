<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario General</title>
    <style>
        @page {
            size: legal landscape;
            margin: 15mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 11pt;
            font-weight: bold;
            color: #2C3E50;
            margin-bottom: 3px;
        }

        .report-title {
            font-size: 13pt;
            font-weight: bold;
            color: #E74C3C;
            margin: 5px 0;
        }

        .report-date {
            font-size: 9pt;
            color: #555;
            margin-top: 3px;
        }

        .info-section {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            font-size: 8pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #4A90E2;
            color: white;
        }

        th {
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #333;
            font-weight: bold;
            font-size: 7pt;
            line-height: 1.2;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #999;
            text-align: center;
            font-size: 7pt;
        }

        .text-left { text-align: left; padding-left: 6px; }
        .text-right { text-align: right; padding-right: 6px; }
        .text-bold { font-weight: bold; }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tbody tr:hover {
            background: #e8f4f8;
        }

        .col-item { width: 3%; }
        .col-codigo { width: 7%; }
        .col-detalle { width: 25%; }
        .col-unidad { width: 5%; }
        .col-cantidad { width: 7%; }
        .col-saldo { width: 8%; }
        .col-precio { width: 7%; }

        .footer-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .total-row {
            background: #fffacd !important;
            font-weight: bold;
            font-size: 8pt;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-box {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 0 10px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 8pt;
        }

        .page-number {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    {{-- ENCABEZADO --}}
    <div class="header">
        <div class="company-name">EMPRESA MUNICIPAL DE DISTRIBUCIÓN DE ENERGÍA ELÉCTRICA</div>
        <div class="company-name">"E.M.D.E.LL."</div>
        <div class="report-title">INVENTARIO DE BIENES DE CONSUMO</div>
        <div class="report-date">
            AL {{ \Carbon\Carbon::parse($fechaFin)->format('d \d\e F \d\e Y') }}
        </div>
        <div class="report-date" style="font-size: 7pt;">
            (EXPRESADO EN BOLIVIANOS)
        </div>
    </div>

    {{-- INFORMACIÓN DEL PERÍODO --}}
    <div class="info-section">
        <strong>PERÍODO:</strong> Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>FECHA DE GENERACIÓN:</strong> {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- TABLA DE INVENTARIO --}}
    <table>
        <thead>
            <tr>
                <th class="col-item" rowspan="2">ÍTEM</th>
                <th class="col-codigo" rowspan="2">CÓDIGO</th>
                <th class="col-detalle" rowspan="2">DETALLE</th>
                <th class="col-unidad" rowspan="2">UNIDAD</th>
                <th colspan="2">SALDO INICIAL<br>{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</th>
                <th class="col-precio" rowspan="2">P/U</th>
                <th colspan="2">SALDO FINAL<br>{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</th>
            </tr>
            <tr>
                <th class="col-cantidad">CANTIDAD</th>
                <th class="col-saldo">SALDO Bs.</th>
                <th class="col-cantidad">CANTIDAD</th>
                <th class="col-saldo">SALDO Bs.</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentCategoria = null;
                $itemNumber = 1;
            @endphp

            @foreach($materiales as $material)
                {{-- Separador por categoría --}}
                @if($currentCategoria !== $material['categoria'])
                    @php $currentCategoria = $material['categoria']; @endphp
                    <tr style="background: #E8F4F8; font-weight: bold;">
                        <td colspan="9" class="text-left" style="padding: 6px; font-size: 8pt;">
                            {{ strtoupper($material['categoria']) }}
                            @if(isset($material['subcategoria']))
                                / {{ strtoupper($material['subcategoria']) }}
                            @endif
                        </td>
                    </tr>
                @endif

                <tr>
                    <td>{{ $itemNumber++ }}</td>
                    <td class="text-bold">{{ $material['codigo'] }}</td>
                    <td class="text-left">{{ strtoupper($material['nombre']) }}</td>
                    <td>{{ $material['unidad'] }}</td>
                    <td class="text-right">{{ number_format($material['cantidad_inicial'], 2) }}</td>
                    <td class="text-right">{{ number_format($material['saldo_inicial'], 2) }}</td>
                    <td class="text-right">{{ number_format($material['precio_unitario'], 2) }}</td>
                    <td class="text-right">{{ number_format($material['cantidad_final'], 2) }}</td>
                    <td class="text-right text-bold">{{ number_format($material['saldo_final'], 2) }}</td>
                </tr>
            @endforeach

            {{-- FILA DE TOTALES --}}
            <tr class="total-row">
                <td colspan="5" class="text-right" style="padding-right: 10px;">TOTAL VALORIZADO:</td>
                <td class="text-right text-bold">{{ number_format($totalInicial, 2) }}</td>
                <td></td>
                <td></td>
                <td class="text-right text-bold">{{ number_format($totalFinal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- FIRMAS --}}
    <div class="footer-section">
        <div class="page-number">
            Generado el {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>
</html>