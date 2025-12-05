{{-- resources/views/transactions/index.blade.php --}}
@extends('layouts.app1')

@section('title', 'Historial de Transacciones')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Cabecera -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
                    Historial de Movimientos
                </h1>
                <a href="{{ route('transactions.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Registrar Transacción
                </a>
            </div>
        </div>

        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tarjeta principal -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- VERSIÓN ESCRITORIO: Tabla clásica (visible solo en lg+) -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 dark:bg-gray-700 text-white text-xs font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Fecha</th>
                            <th class="px-6 py-4 text-left">Hora</th>
                            <th class="px-6 py-4 text-left">Descripción</th>
                            <th class="px-6 py-4 text-center">Cuenta</th>
                            <th class="px-6 py-4 text-left">Categoría</th>
                            <th class="px-6 py-4 text-right">Monto</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-medium">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">
                                    {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '--:--' }}
                                </td>
                                <td class="px-6 py-4">{{ $transaction->description }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $transaction->account->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $transaction->category?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-bold font-mono text-lg">
                                    <span class="{{ $transaction->type == 'ingreso' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction->type == 'ingreso' ? '+' : '-' }}${{ number_format(abs($transaction->amount), 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transactions.edit', $transaction) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline"
                                            onsubmit="return confirm('¿Borrar esta transacción?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16 text-gray-500 dark:text-gray-400">
                                    <div class="text-6xl mb-4">Mailbox</div>
                                    <p class="text-xl font-medium">Aún no hay transacciones</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- VERSIÓN MÓVIL: Tarjetas apiladas (visible solo < lg) -->
            <div class="lg:hidden divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($transactions as $transaction)
                    <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-2">
                                        {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '--:--' }}
                                    </span>
                                </div>
                                <div class="text-gray-700 dark:text-gray-300 mt-1">
                                    {{ $transaction->description }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold font-mono
                                    {{ $transaction->type == 'ingreso' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction->type == 'ingreso' ? '+' : '-' }}${{ number_format(abs($transaction->amount), 2) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 text-sm mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $transaction->account->name }}
                            </span>
                            @if($transaction->category)
                                <span class="inline-flex items-center px-3 py-1 rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ $transaction->category->name }}
                                </span>
                            @endif
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('transactions.edit', $transaction) }}"
                            class="text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/30 p-2 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </a>
                            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                                onsubmit="return confirm('¿Borrar?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 p-2 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-20 h-20 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4.479a2 2 0 00-1.414.586L12 21l-3.107-3.414A2 2 0 007.479 17H3"/></svg>
                        <p class="text-lg font-medium">No hay transacciones todavía</p>
                        <p class="text-sm mt-2">¡Registra la primera!</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación (funciona igual en ambas versiones) -->
            <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection