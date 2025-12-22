@extends('layouts.app1')
@section('title', 'Detalle de Arqueo #' . $cashCount->id)

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('cash_counts.history') }}" class="flex items-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver al Historial
        </a>
        <div class="text-right">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Arqueo #{{ $cashCount->id }}</h1>
            <p class="text-sm text-gray-500">{{ $cashCount->created_at->format('d/m/Y - h:i A') }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">
            
            <div class="py-2">
                <p class="text-sm uppercase tracking-wider text-gray-500 mb-1">Total Contado</p>
                <p class="text-4xl font-bold text-gray-800 dark:text-gray-100">
                    S/ {{ number_format($cashCount->total_counted, 2) }}
                </p>
            </div>

            <div class="py-2">
                <p class="text-sm uppercase tracking-wider text-gray-500 mb-1">Saldo Sistema</p>
                <p class="text-2xl font-semibold text-gray-600 dark:text-gray-300">
                    S/ {{ number_format($cashCount->system_balance, 2) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ $cashCount->account ? $cashCount->account->name : 'Sin cuenta asociada' }}
                </p>
            </div>

            <div class="py-2">
                <p class="text-sm uppercase tracking-wider text-gray-500 mb-1">Diferencia</p>
                <p class="text-3xl font-bold {{ $cashCount->difference < 0 ? 'text-red-600' : ($cashCount->difference > 0 ? 'text-blue-600' : 'text-green-600') }}">
                    S/ {{ number_format($cashCount->difference, 2) }}
                </p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold {{ $cashCount->difference < 0 ? 'bg-red-100 text-red-800' : ($cashCount->difference > 0 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                    @if($cashCount->difference == 0) Cuadre Perfecto
                    @elseif($cashCount->difference > 0) Sobrante
                    @else Faltante
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border-t-4 border-blue-600">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 border-b border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Detalle Monedas</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-3 text-left">Denominación</th>
                        <th class="px-6 py-3 text-center">Cant.</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $totalCoins = 0; @endphp
                    {{-- Accedemos al JSON guardado usando notación de array --}}
                    @foreach($cashCount->details['coins'] ?? [] as $coin)
                        @if(!empty($coin['qty']) && $coin['qty'] > 0)
                            @php $subtotal = $coin['denom'] * $coin['qty']; $totalCoins += $subtotal; @endphp
                            <tr>
                                <td class="px-6 py-3 font-medium text-gray-700 dark:text-gray-300">S/ {{ number_format($coin['denom'], 2) }}</td>
                                <td class="px-6 py-3 text-center">{{ $coin['qty'] }}</td>
                                <td class="px-6 py-3 text-right text-gray-800 dark:text-gray-200">S/ {{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="bg-blue-50 dark:bg-blue-900/20 font-bold">
                        <td colspan="2" class="px-6 py-3 text-right">Total Monedas:</td>
                        <td class="px-6 py-3 text-right">S/ {{ number_format($totalCoins, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border-t-4 border-purple-600">
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 border-b border-gray-200 dark:border-gray-600">
                <h3 class="font-bold text-gray-700 dark:text-gray-200">Detalle Billetes</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-3 text-left">Denominación</th>
                        <th class="px-6 py-3 text-center">Cant.</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $totalBills = 0; @endphp
                    @foreach($cashCount->details['bills'] ?? [] as $bill)
                        @if(!empty($bill['qty']) && $bill['qty'] > 0)
                            @php $subtotal = $bill['denom'] * $bill['qty']; $totalBills += $subtotal; @endphp
                            <tr>
                                <td class="px-6 py-3 font-medium text-gray-700 dark:text-gray-300">S/ {{ number_format($bill['denom'], 2) }}</td>
                                <td class="px-6 py-3 text-center">{{ $bill['qty'] }}</td>
                                <td class="px-6 py-3 text-right text-gray-800 dark:text-gray-200">S/ {{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="bg-purple-50 dark:bg-purple-900/20 font-bold">
                        <td colspan="2" class="px-6 py-3 text-right">Total Billetes:</td>
                        <td class="px-6 py-3 text-right">S/ {{ number_format($totalBills, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection