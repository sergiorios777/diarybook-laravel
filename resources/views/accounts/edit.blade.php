@extends('layouts.app1')
@section('title', 'Editar Cuenta')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">Editar Cuenta</h1>
            <a href="{{ route('cuentas.index') }}"
               class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                ← Volver
            </a>
        </div>

        <form action="{{ route('cuentas.update', $account) }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Nombre de la Cuenta</label>
                <input type="text" name="name" value="{{ old('name', $account->name) }}" required
                       class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500 transition text-lg">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Saldo Inicial</label>
                <input type="number" name="initial_balance" step="0.01" min="0" value="{{ old('initial_balance', $account->initial_balance) }}" required
                       class="w-full px-5 py-4 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-blue-500 transition text-lg font-mono">
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Cambiar esto afectará el saldo actual calculado
                </p>
            </div>

            <div class="pt-6">
                <button type="submit"
                        class="w-full py-5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xl rounded-xl shadow-xl transition transform hover:-translate-y-1">
                    Actualizar Cuenta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection