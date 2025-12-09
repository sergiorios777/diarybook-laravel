<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Semanal</title>
    <style>
        /* Estilos compatibles con DOMPDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        p { margin: 2px 0; }
        
        /* Tablas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px;
            text-align: right; /* Por defecto números a la derecha */
        }
        th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
        /* Primera columna (Concepto) alineada a la izquierda */
        td:first-child, th:first-child {
            text-align: left;
            width: 180px; 
        }

        /* Colores para textos (sin usar clases de tailwind) */
        .text-green { color: #27ae60; }
        .text-red { color: #c0392b; }
        .text-blue { color: #2980b9; }
        .bg-gray { background-color: #f0f0f0; font-weight: bold; }
        .font-bold { font-weight: bold; }

        /* Totales */
        .row-total { background-color: #f9f9f9; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte Semanal Nº {{ $startOfWeek->weekOfYear }}</h1>
        <p>Del {{ $startOfWeek->format('d/m/Y') }} al {{ $endOfWeek->format('d/m/Y') }}</p>
        <p><small>Generado el: {{ now()->format('d/m/Y H:i') }}</small></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>CONCEPTO</th>
                @foreach($days as $date => $info)
                    <th>
                        {{ $info['label'] }}<br>
                        <small>{{ substr($info['day_name'], 0, 3) }}</small>
                    </th>
                @endforeach
                <th style="background-color: #333; color: white;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            
            <tr style="background-color: #eef;">
                <td><strong>Saldo Inicial</strong></td>
                <td>{{ number_format($balanceStartOfWeek, 2) }}</td>
                @for($i = 1; $i < 7; $i++) <td></td> @endfor
                <td class="bg-gray">{{ number_format($balanceStartOfWeek, 2) }}</td>
            </tr>

            <tr>
                <td colspan="9" style="text-align: center; background-color: #e8f5e9; color: #27ae60; font-weight: bold;">
                    INGRESOS
                </td>
            </tr>

            @php
                $weeklyTotalIngresos = 0;
                $dailyIngresosSum = array_fill_keys(array_keys($days), 0);
            @endphp

            @foreach($matrix['ingreso'] as $catName => $dates)
                @php $rowTotal = 0; @endphp
                <tr>
                    <td>{{ $catName }}</td>
                    @foreach($days as $dayDate => $info)
                        @php
                            $amount = $dates[$dayDate] ?? 0;
                            $rowTotal += $amount;
                            $dailyIngresosSum[$dayDate] += $amount;
                        @endphp
                        <td class="{{ $amount > 0 ? 'text-green' : '' }}">
                            {{ $amount > 0 ? number_format($amount, 2) : '-' }}
                        </td>
                    @endforeach
                    <td class="row-total text-green">
                        {{ number_format($rowTotal, 2) }}
                    </td>
                    @php $weeklyTotalIngresos += $rowTotal; @endphp
                </tr>
            @endforeach

            <tr class="bg-gray">
                <td>TOTAL INGRESOS</td>
                @foreach($days as $dayDate => $info)
                    @php
                        $val = $loop->first ? $balanceStartOfWeek + $dailyIngresosSum[$dayDate] : $dailyIngresosSum[$dayDate];
                    @endphp
                    <td class="text-green">{{ number_format($val, 2) }}</td>
                @endforeach
                <td class="text-green">{{ number_format($weeklyTotalIngresos + $balanceStartOfWeek, 2) }}</td>
            </tr>

            <tr>
                <td colspan="9" style="text-align: center; background-color: #fdeaea; color: #c0392b; font-weight: bold;">
                    GASTOS
                </td>
            </tr>

            @php
                $weeklyTotalGastos = 0;
                $dailyGastosSum = array_fill_keys(array_keys($days), 0);
            @endphp

            @foreach($matrix['gasto'] as $catName => $dates)
                @php $rowTotal = 0; @endphp
                <tr>
                    <td>{{ $catName }}</td>
                    @foreach($days as $dayDate => $info)
                        @php
                            $amount = ($dates[$dayDate] ?? 0) * -1; // Convertir a positivo visualmente si deseas, o dejar negativo
                            $rowTotal += $amount;
                            $dailyGastosSum[$dayDate] += $amount;
                        @endphp
                        <td class="{{ $amount < 0 ? 'text-red' : '' }}">
                            {{ $amount < 0 ? number_format($amount, 2) : '-' }}
                        </td>
                    @endforeach
                    <td class="row-total text-red">
                        {{ number_format($rowTotal, 2) }}
                    </td>
                    @php $weeklyTotalGastos += $rowTotal; @endphp
                </tr>
            @endforeach

            <tr class="bg-gray">
                <td>TOTAL GASTOS</td>
                @foreach($dailyGastosSum as $sum)
                    <td class="text-red">{{ number_format($sum, 2) }}</td>
                @endforeach
                <td class="text-red">{{ number_format($weeklyTotalGastos, 2) }}</td>
            </tr>

            <tr style="background-color: #333; color: white;">
                <td style="background-color: #333; color: white;">SALDO FINAL</td>
                @foreach($days as $dayDate => $info)
                    @php
                        $ing = $dailyIngresosSum[$dayDate];
                        $gas = $dailyGastosSum[$dayDate];
                        $neto = $ing + $gas;
                        if ($loop->first) $neto += $balanceStartOfWeek;
                    @endphp
                    <td style="font-weight: bold;">{{ number_format($neto, 2) }}</td>
                @endforeach
                <td style="font-weight: bold;">
                    {{ number_format(($weeklyTotalIngresos + $balanceStartOfWeek) + $weeklyTotalGastos, 2) }}
                </td>
            </tr>

             <tr>
                <td class="font-bold">ACUMULADO</td>
                @php $running = $balanceStartOfWeek; @endphp
                @foreach($days as $dayDate => $info)
                    @php
                        $running += $dailyIngresosSum[$dayDate] + $dailyGastosSum[$dayDate];
                    @endphp
                    <td>{{ number_format($running, 2) }}</td>
                @endforeach
                <td class="bg-gray">{{ number_format($running, 2) }}</td>
            </tr>

        </tbody>
    </table>

</body>
</html>