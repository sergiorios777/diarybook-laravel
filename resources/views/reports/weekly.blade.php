@extends('layouts.app1')
@section('title', 'Reporte Semanal')

@push('styles')
<style>
    @media print {
        @page { size: A4 landscape; margin: 1cm; }
        body { -webkit-print-color-adjust: exact; }
        .no-print { display: none !important; }
        .shadow-lg { box-shadow: none !important; }
        .bg-gradient-to-r { background: #2c3e50 !important; }
        .text-green-600 { color: #27ae60 !important; }
        .text-red-600 { color: #c0392b !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtro + Imprimir -->
    <div class="no-print bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <form action="{{ route('reports.weekly') }}" method="GET" class="flex items-center gap-4">
                <label class="font-medium text-gray-700 dark:text-gray-300">Semana de:</label>
                <input type="date" name="date" value="{{ $startOfWeek->format('Y-m-d') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    Ver Reporte
                </button>
            </form>

            <button onclick="window.print()"
                    class="px-6 py-2 bg-gray-700 hover:bg-gray-900 text-white rounded-lg font-medium transition flex items-center gap-2">
                Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Título del Reporte -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            REPORTE SEMANAL Nº {{ $startOfWeek->weekOfYear }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">
            Del {{ $startOfWeek->format('d/m/Y') }} al {{ $endOfWeek->format('d/m/Y') }}
        </p>
    </div>

    <!-- Tabla Principal -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden"> <!-- ← rounded-lg = solo 6px -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold w-48">CONCEPTO</th> <!-- ← ancho fijo perfecto -->
                        @foreach($days as $date => $info)
                            <th class="px-4 py-3 text-center min-w-28">
                                <div class="font-bold">{{ $info['label'] }}</div>
                                <div class="text-xs opacity-80">{{ $info['day_name'] }}</div>
                            </th>
                        @endforeach
                        <th class="px-8 py-4 text-center font-bold w-40 bg-gray-800">TOTAL</th> <!-- ← más ancha + fondo -->
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    <!-- Saldo Inicial -->
                    <tr class="bg-gray-50 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                        <td class="px-6 py-2 font-bold">Saldo Inicial</td>
                        <td class="text-right font-mono">${{ number_format($balanceStartOfWeek, 2) }}</td>
                        @for($i = 1; $i < 7; $i++) <td></td> @endfor
                        <td class="text-right font-bold text-base bg-gray-100 dark:bg-gray-700">
                            ${{ number_format($balanceStartOfWeek, 2) }}
                        </td>
                    </tr>

                    <!-- INGRESOS -->
                    <tr class="bg-green-50 dark:bg-green-900/20">
                        <td colspan="9" class="text-center font-bold text-green-700 dark:text-green-400 py-3 text-lg">
                            INGRESOS
                        </td>
                    </tr>

                    @php
                        $weeklyTotalIngresos = 0;
                        $dailyIngresosSum = array_fill_keys(array_keys($days), 0);
                    @endphp

                    @foreach($matrix['ingreso'] as $catName => $dates)
                        @php $rowTotal = 0; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-2 font-medium text-gray-800 dark:text-gray-200 truncate max-w-48">
                                {{ $catName }}
                            </td>
                            @foreach($days as $dayDate => $info)
                                @php
                                    $amount = $dates[$dayDate] ?? 0;
                                    $rowTotal += $amount;
                                    $dailyIngresosSum[$dayDate] += $amount;
                                @endphp
                                <td class="text-right font-mono {{ $amount > 0 ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $amount > 0 ? number_format($amount, 2) : '-' }}
                                </td>
                            @endforeach
                            <td class="text-right font-bold text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 text-base">
                                ${{ number_format($rowTotal, 2) }}
                            </td>
                            @php $weeklyTotalIngresos += $rowTotal; @endphp
                        </tr>
                    @endforeach

                    <!-- Total Ingresos -->
                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold text-base">
                        <td class="px-6 py-3">Total Ingresos</td>
                        @foreach($days as $dayDate => $info)
                            @php
                                $val = $loop->first
                                    ? $balanceStartOfWeek + $dailyIngresosSum[$dayDate]
                                    : $dailyIngresosSum[$dayDate];
                            @endphp
                            <td class="text-right text-green-700 dark:text-green-400 font-mono">
                                ${{ number_format($val, 2) }}
                            </td>
                        @endforeach
                        <td class="text-right text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 font-bold">
                            ${{ number_format($weeklyTotalIngresos + $balanceStartOfWeek, 2) }}
                        </td>
                    </tr>

                    <!-- GASTOS -->
                    <tr class="bg-red-50 dark:bg-red-900/20">
                        <td colspan="9" class="text-center font-bold text-red-700 dark:text-red-400 py-3 text-lg">
                            GASTOS
                        </td>
                    </tr>

                    @php
                        $weeklyTotalGastos = 0;
                        $dailyGastosSum = array_fill_keys(array_keys($days), 0);
                    @endphp

                    @foreach($matrix['gasto'] as $catName => $dates)
                        @php $rowTotal = 0; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-2 font-medium text-gray-800 dark:text-gray-200 truncate max-w-48">
                                {{ $catName }}
                            </td>
                            @foreach($days as $dayDate => $info)
                                @php
                                    $amount = ($dates[$dayDate] ?? 0) * -1;
                                    $rowTotal += $amount;
                                    $dailyGastosSum[$dayDate] += $amount;
                                @endphp
                                <td class="text-right font-mono {{ $amount < 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $amount < 0 ? number_format($amount, 2) : '-' }}
                                </td>
                            @endforeach
                            <td class="text-right font-bold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 text-base">
                                ${{ number_format($rowTotal, 2) }}
                            </td>
                            @php $weeklyTotalGastos += $rowTotal; @endphp
                        </tr>
                    @endforeach

                    <!-- Total Gastos -->
                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold text-base">
                        <td class="px-6 py-3">Total Gastos</td>
                        @foreach($dailyGastosSum as $sum)
                            <td class="text-right text-red-700 dark:text-red-400 font-mono">
                                ${{ number_format($sum, 2) }}
                            </td>
                        @endforeach
                        <td class="text-right text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-900/30 font-bold">
                            ${{ number_format($weeklyTotalGastos, 2) }}
                        </td>
                    </tr>

                    <!-- Saldo Diario -->
                    <tr class="bg-blue-50 dark:bg-blue-900/30 font-bold text-base">
                        <td class="px-6 py-3">Saldo Diario</td>
                        @foreach($days as $dayDate => $info)
                            @php
                                $ing = $dailyIngresosSum[$dayDate];
                                $gas = $dailyGastosSum[$dayDate];
                                $neto = $ing + $gas;
                                if ($loop->first) $neto += $balanceStartOfWeek;
                            @endphp
                            <td class="text-right font-mono {{ $neto >= 0 ? 'text-blue-700 dark:text-blue-400' : 'text-red-700 dark:text-red-400' }}">
                                ${{ number_format($neto, 2) }}
                            </td>
                        @endforeach
                        <td class="text-right font-bold text-blue-700 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30">
                            ${{ number_format(($weeklyTotalIngresos + $balanceStartOfWeek) + $weeklyTotalGastos, 2) }}
                        </td>
                    </tr>

                    <!-- ACUMULADO -->
                    <tr class="bg-gray-900 text-white font-bold text-base">
                        <td class="px-6 py-4">ACUMULADO</td>
                        @php $running = $balanceStartOfWeek; @endphp
                        @foreach($days as $dayDate => $info)
                            @php
                                $running += $dailyIngresosSum[$dayDate] + $dailyGastosSum[$dayDate];
                            @endphp
                            <td class="text-right font-mono text-base">
                                ${{ number_format($running, 2) }}
                            </td>
                        @endforeach
                        <td class="bg-gradient-to-l from-blue-600 to-purple-700 text-white font-bold text-base">
                            ${{ number_format($running, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection