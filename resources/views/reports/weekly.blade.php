<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Semanal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; }
        
        /* Navbar (Oculta al imprimir) */
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .navbar a.btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 4px; }

        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

        /* Filtro de fecha */
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 8px; }

        /* TABLA ESTILO REPORTE */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px 5px; text-align: right; }
        
        /* Encabezados */
        thead th { background-color: #2c3e50; color: white; text-align: center; font-weight: bold; }
        
        /* Primera columna (Nombres) */
        td:first-child { text-align: left; font-weight: bold; width: 150px; background-color: #f9f9f9; }
        
        /* Totales */
        .row-total { background-color: #e9ecef; font-weight: bold; }
        .section-total { background-color: #dfe6e9; font-weight: bold; color: #2d3436; }
        
        /* Colores de texto */
        .text-ingreso { color: #27ae60; }
        .text-gasto { color: #c0392b; }
        .text-neutral { color: #7f8c8d; }

        /* ESTILOS DE IMPRESIÓN A4 HORIZONTAL */
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            body { background: white; -webkit-print-color-adjust: exact; }
            .navbar, .filter-bar, .no-print { display: none; }
            .container { box-shadow: none; margin: 0; padding: 0; max-width: 100%; }
            th { background-color: #2c3e50 !important; color: white !important; }
            .section-total { background-color: #dfe6e9 !important; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.weekly') }}">Reporte Semanal</a>
            <a href="{{ route('transactions.index') }}">Volver</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="filter-bar">
            <form action="{{ route('reports.weekly') }}" method="GET" style="display: flex; gap: 10px; align-items: center;">
                <label>Seleccionar fecha dentro de la semana:</label>
                <input type="date" name="date" value="{{ $startOfWeek->format('Y-m-d') }}" style="padding: 5px;">
                <button type="submit" style="padding: 6px 12px; cursor: pointer;">Ver Reporte</button>
            </form>
            <button onclick="window.print()" class="no-print" style="margin-left: auto; padding: 6px 12px; cursor: pointer;">🖨️ Imprimir</button>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="margin:0;">REPORTE SEMANAL Nº {{ $startOfWeek->weekOfYear }}</h2>
            <p style="margin:5px 0; color: #666;">
                Del {{ $startOfWeek->format('d/m/Y') }} al {{ $endOfWeek->format('d/m/Y') }}
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>CONCEPTO</th>
                    @foreach($days as $date => $info)
                        <th>{{ $info['label'] }}<br><small>{{ $info['day_name'] }}</small></th>
                    @endforeach
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                
                <tr class="section-total">
                    <td>Saldo Inicial</td>
                    <td>{{ number_format($balanceStartOfWeek, 2) }}</td>
                    @for($i=0; $i<6; $i++) <td></td> @endfor
                    <td>{{ number_format($balanceStartOfWeek, 2) }}</td>
                </tr>

                <tr><td colspan="9" style="background:#eafaf1; text-align:center; font-size:10px; padding:2px;">INGRESOS</td></tr>
                
                @php $weeklyTotalIngresos = 0; $dailyIngresosSum = array_fill_keys(array_keys($days), 0); @endphp
                
                @foreach($matrix['ingreso'] as $catName => $dates)
                    <tr>
                        <td>{{ $catName }}</td>
                        @php $rowTotal = 0; @endphp
                        
                        @foreach($days as $dayDate => $info)
                            @php 
                                $amount = $dates[$dayDate] ?? 0; 
                                $rowTotal += $amount;
                                $dailyIngresosSum[$dayDate] += $amount;
                            @endphp
                            <td class="{{ $amount > 0 ? 'text-ingreso' : 'text-neutral' }}">
                                {{ $amount != 0 ? number_format($amount, 2) : '0' }}
                            </td>
                        @endforeach
                        
                        <td class="row-total">{{ number_format($rowTotal, 2) }}</td>
                        @php $weeklyTotalIngresos += $rowTotal; @endphp
                    </tr>
                @endforeach

                <tr class="section-total" style="border-top: 2px solid #aaa;">
                    <td>Total Ingresos</td>
                    <td>
                        @php 
                            // Ajuste visual: En tu ejemplo, la fila "Total Ingresos" día 1 incluye el saldo inicial?
                            // En tu tabla ejemplo: SI (1337.42). Pero abajo los otros días solo suman los ingresos.
                            // Replicaré tu lógica: Dia 1 muestra (SaldoInicial + IngresoDia). Otros días solo IngresoDia.
                            $valDia1 = $balanceStartOfWeek + $dailyIngresosSum[$startOfWeek->format('Y-m-d')];
                        @endphp
                        {{ number_format($valDia1, 2) }}
                    </td>

                    @foreach($days as $dayDate => $info)
                        @if($loop->first) @continue @endif
                        <td>{{ number_format($dailyIngresosSum[$dayDate], 2) }}</td>
                    @endforeach

                    <td>{{ number_format($weeklyTotalIngresos + $balanceStartOfWeek, 2) }}</td>
                </tr>


                <tr><td colspan="9" style="background:#fdedec; text-align:center; font-size:10px; padding:2px;">GASTOS</td></tr>

                @php $weeklyTotalGastos = 0; $dailyGastosSum = array_fill_keys(array_keys($days), 0); @endphp

                @foreach($matrix['gasto'] as $catName => $dates)
                    <tr>
                        <td>{{ $catName }}</td>
                        @php $rowTotal = 0; @endphp
                        
                        @foreach($days as $dayDate => $info)
                            @php 
                                $amount = $dates[$dayDate] ?? 0; 
                                // En tu ejemplo los gastos se ven negativos.
                                // En la BD se guardan positivos. Los multiplicamos por -1 para visualización.
                                $displayAmount = $amount * -1;
                                $rowTotal += $displayAmount;
                                $dailyGastosSum[$dayDate] += $displayAmount;
                            @endphp
                            <td class="{{ $displayAmount < 0 ? 'text-gasto' : 'text-neutral' }}">
                                {{ $displayAmount != 0 ? number_format($displayAmount, 2) : '0' }}
                            </td>
                        @endforeach
                        
                        <td class="row-total text-gasto">{{ number_format($rowTotal, 2) }}</td>
                        @php $weeklyTotalGastos += $rowTotal; @endphp
                    </tr>
                @endforeach

                <tr class="section-total" style="border-top: 2px solid #aaa;">
                    <td>Total Gastos</td>
                    @foreach($days as $dayDate => $info)
                        <td class="text-gasto">{{ number_format($dailyGastosSum[$dayDate], 2) }}</td>
                    @endforeach
                    <td class="text-gasto">{{ number_format($weeklyTotalGastos, 2) }}</td>
                </tr>

                <tr><td colspan="9" style="height: 15px;"></td></tr>

                <tr style="font-weight: bold;">
                    <td>Saldo Diario</td>
                    @foreach($days as $dayDate => $info)
                        @php
                            // Saldo Diario = Ingresos Del Día - Gastos Del Día
                            // En la primera columna, tu ejemplo incluye el Saldo Inicial en la suma.
                            $ingresoBase = $dailyIngresosSum[$dayDate];
                            $gastoBase   = $dailyGastosSum[$dayDate]; // Ya es negativo
                            
                            $netoDia = $ingresoBase + $gastoBase;
                            
                            if ($loop->first) {
                                $netoDia += $balanceStartOfWeek;
                            }
                        @endphp
                        <td>{{ number_format($netoDia, 2) }}</td>
                    @endforeach
                    <td>{{ number_format(($weeklyTotalIngresos + $balanceStartOfWeek) + $weeklyTotalGastos, 2) }}</td>
                </tr>

                <tr class="section-total" style="background-color: #2c3e50; color: white;">
                    <td>ACUMULADO</td>
                    @php 
                        $runningBalance = $balanceStartOfWeek; 
                    @endphp

                    @foreach($days as $dayDate => $info)
                        @php
                            // Acumulado = Saldo Anterior + (IngresosHoy + GastosHoy)
                            $ingresoHoy = $dailyIngresosSum[$dayDate];
                            $gastoHoy   = $dailyGastosSum[$dayDate]; // Negativo
                            $runningBalance = $runningBalance + $ingresoHoy + $gastoHoy;
                        @endphp
                        <td>{{ number_format($runningBalance, 2) }}</td>
                    @endforeach
                    <td></td> </tr>

            </tbody>
        </table>

    </div>

</body>
</html>