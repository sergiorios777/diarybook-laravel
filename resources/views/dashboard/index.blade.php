@extends('layouts.app1')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    {{-- ====== SALDO TOTAL GENERAL ====== --}}
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 dark:from-blue-500 dark:to-purple-600 rounded-2xl p-8 text-white shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm uppercase tracking-wider">Saldo Total General</p>
                <p class="text-5xl font-bold mt-2">
                    ${{ number_format($total_general, 2) }}
                </p>
                <p class="text-blue-100 mt-2">
                    @php
                        $cambio = $accounts->sum(fn($a) => $a->current_balance) - $accounts->sum('initial_balance');
                    @endphp
                    @if($cambio > 0)
                        <span class="text-green-300">+${{ number_format($cambio, 2) }}</span> este mes
                    @elseif($cambio < 0)
                        <span class="text-red-300">-${{ number_format(abs($cambio), 2) }}</span> este mes
                    @else
                        Sin movimientos
                    @endif
                </p>
            </div>
            <div class="text-8xl opacity-20">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5a2 2 0 002-2v-4a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 002 2zm-10-8h.01M9 16h.01"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ====== SALDOS POR CUENTA ====== --}}
    <div>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Saldos por Cuenta</h3>
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($accounts as $account)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 hover:shadow-xl transition transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                            {{ $account->name }}
                        </h4>
                        <span class="text-4xl opacity-40">
                            @if(strtolower($account->name) == 'efectivo')
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            @elseif(str_contains(strtolower($account->name), 'banco'))
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-4m-6 0H5a2 2 0 002-2v-4a2 2 0 012-2h6a2 2 0 012 2v4a2 2 0 002 2zm-10-8h.01M9 16h.01"/>
                                </svg>
                            @else
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            @endif
                        </span>
                    </div>

                    <div class="text-3xl font-bold {{ $account->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        ${{ number_format($account->current_balance, 2) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ====== GRÁFICO DE EVOLUCIÓN MENSUAL (Últimos 6 meses) ====== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-4">Evolución Mensual</h3>
            <div class="h-80"> <!-- Contenedor con altura fija -->
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- ====== RESUMEN RÁPIDO ====== --}}
        <div class="space-y-4">
            @php
                $ingresosMes = \App\Models\Transaction::where('type', 'ingreso')
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->excludeInternalTransfers()  // ← ¡MÁGICO!
                    ->sum('amount');

                $gastosMes = \App\Models\Transaction::where('type', 'gasto')
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year)
                    ->excludeInternalTransfers()  // ← ¡MÁGICO!
                    ->sum('amount');

                $balanceMes = $ingresosMes - $gastosMes;
            @endphp

            <!-- Ingresos del Mes -->
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 dark:text-green-400 font-semibold">Ingresos del Mes</p>
                        <p class="text-3xl font-bold text-green-700 dark:text-green-300">
                            ${{ number_format($ingresosMes, 2) }}
                        </p>
                    </div>
                    <div class="text-5xl text-green-500">
                        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Gastos del Mes -->
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 dark:text-red-400 font-semibold">Gastos del Mes</p>
                        <p class="text-3xl font-bold text-red-700 dark:text-red-300">
                            ${{ number_format($gastosMes, 2) }}
                        </p>
                    </div>
                    <div class="text-5xl text-red-500">
                        <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-5 text-white">
                <p class="font-semibold">Balance del Mes</p>
                <p class="text-4xl font-bold">
                    ${{ number_format($balanceMes, 2) }}
                </p>
                <p class="text-sm mt-1 opacity-90">
                    {{ $balanceMes >= 0 ? 'Estás en positivo' : 'Cuidado con los gastos' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ====== NUEVO: GRÁFICO DE EVOLUCIÓN DIARIA (12 DÍAS) ====== --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">
                Flujo de Caja (Últimos 12 Días)
            </h3>
            <span class="text-xs font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded-lg dark:bg-blue-900 dark:text-blue-200">
                Diario
            </span>
        </div>
        <div class="h-72"> <canvas id="dailyChart"></canvas>
        </div>
    </div>

    {{-- ====== ÚLTIMAS TRANSACCIONES ====== --}}
    <div>
        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-4">Últimos Movimientos</h3>

        <!-- VERSIÓN ESCRITORIO: Tabla (lg+) -->
        <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descripción</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Cuenta</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Transaction::with(['account', 'category'])->orderBy('date', 'desc')->orderBy('time', 'desc')->latest()->take(8)->get() as $t)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-2 text-sm text-gray-600 dark:text-gray-400">
                                    <div>{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</div>
                                    <div>{{ \Carbon\Carbon::parse($t->time)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-2 font-medium">
                                    {{ Str::limit($t->description, 40) }}
                                </td>
                                <td class="px-6 py-2 text-sm">
                                    <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $t->account->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-2 text-right font-bold text-lg">
                                    <span class="{{ $t->type == 'ingreso' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $t->type == 'ingreso' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    No hay movimientos recientes
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VERSIÓN MÓVIL: Tarjetas apiladas (< lg) -->
        <div class="lg:hidden space-y-4">
            @forelse(\App\Models\Transaction::with(['account', 'category'])->orderBy('date', 'desc')->orderBy('time', 'desc')->latest()->take(8)->get() as $t)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}
                                @if($t->time)
                                    <span class="ml-2">{{ \Carbon\Carbon::parse($t->time)->format('H:i') }}</span>
                                @endif
                            </div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100 mt-1">
                                {{ $t->description }}
                            </div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $t->type == 'ingreso' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                    {{ $t->type == 'ingreso' ? 'Ingreso' : 'Gasto' }}
                                </span>
                                <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $t->account->name }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold
                                {{ $t->type == 'ingreso' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $t->type == 'ingreso' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-8 text-center">
                    <div class="text-6xl mb-4 opacity-30">Empty</div>
                    <p class="text-gray-500 dark:text-gray-400">No hay movimientos recientes</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

{{-- ====== CHART.JS + GRÁFICO ====== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ====== CONFIGURACIÓN GRÁFICO MENSUAL ======
    const ctx = document.getElementById('monthlyChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Ingresos',
                    data: @json($chartIngresos),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 5
                },
                {
                    label: 'Gastos',
                    data: @json($chartGastos),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ef4444',
                    pointRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 14 } }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            label += '$' + context.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 });
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    // ==========================================

    // ====== CONFIGURACIÓN GRÁFICO DIARIO ======
    const ctxDaily = document.getElementById('dailyChart').getContext('2d');

    new Chart(ctxDaily, {
        type: 'bar', // 'bar' es mejor para ver comparativas diarias, pero puedes usar 'line'
        data: {
            labels: @json($dailyLabels),
            datasets: [
                {
                    label: 'Ingresos',
                    data: @json($dailyIngresos),
                    backgroundColor: '#10b981', // Verde sólido para barras
                    borderRadius: 4, // Bordes redondeados en las barras
                    barPercentage: 0.6,
                },
                {
                    label: 'Gastos',
                    data: @json($dailyGastos),
                    backgroundColor: '#ef4444', // Rojo sólido para barras
                    borderRadius: 4,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { size: 12 } }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            label += '$' + context.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 });
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString(); // Formato moneda eje Y
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)' // Líneas guías sutiles
                    }
                },
                x: {
                    grid: {
                        display: false // Ocultar líneas verticales para limpieza visual
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
    // ==========================================
});
</script>
@endpush