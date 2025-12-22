@extends('layouts.app1')
@section('title', 'Historial de Arqueos')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            Historial de Arqueos
        </h1>
        <a href="{{ route('cash_counts.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition">
            + Nuevo Arqueo
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200 uppercase text-xs leading-normal">
                        <th class="py-3 px-6">ID / Fecha</th>
                        <th class="py-3 px-6">Usuario</th>
                        <th class="py-3 px-6">Cuenta</th>
                        <th class="py-3 px-6 text-right">Total Físico</th>
                        <th class="py-3 px-6 text-right">Diferencia</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                    @forelse($counts as $count)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            
                            <td class="py-3 px-6 whitespace-nowrap">
                                <span class="font-bold text-gray-800 dark:text-white">#{{ $count->id }}</span><br>
                                <span class="text-xs">{{ $count->created_at->format('d/m/Y H:i') }}</span>
                            </td>

                            <td class="py-3 px-6">
                                {{ $count->user->name ?? 'Usuario Eliminado' }}
                            </td>

                            <td class="py-3 px-6">
                                @if($count->account)
                                    <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                        {{ $count->account->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Conteo General</span>
                                @endif
                            </td>

                            <td class="py-3 px-6 text-right font-bold text-gray-800 dark:text-gray-100">
                                S/ {{ number_format($count->total_counted, 2) }}
                            </td>

                            <td class="py-3 px-6 text-right font-bold">
                                @if($count->difference == 0)
                                    <span class="text-green-600 bg-green-100 py-1 px-3 rounded-full">Ok</span>
                                @elseif($count->difference > 0)
                                    <span class="text-blue-600">+{{ number_format($count->difference, 2) }}</span>
                                @else
                                    <span class="text-red-600">{{ number_format($count->difference, 2) }}</span>
                                @endif
                            </td>

                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('cash_counts.show', $count) }}" class="text-gray-500 hover:text-blue-600 transform hover:scale-110 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                No hay arqueos registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 bg-gray-50 dark:bg-gray-700">
            {{ $counts->links() }}
        </div>
    </div>
</div>
@endsection