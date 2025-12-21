{{-- resources/views/transactions/edit.blade.php --}}
@extends('layouts.app1')

@section('title', 'Editar Transacción #' . $transaction->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Título + Volver -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">
            Editar Transacción #{{ $transaction->id }}
        </h1>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('transactions.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 font-medium"
           id="volver-historial">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al historial
        </a>
    </div>

    <!-- Formulario -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8 lg:p-10">
        <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!--Campos ocultos para filtros-->
            @foreach(request()->only(['date_from', 'date_to', 'account_id', 'category_id']) as $key => $value)
                @if($value) {{-- Solo si tiene valor --}}
                    <input type="hidden" name="keep_{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <!-- Fecha y Hora -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="date"
                           name="date"
                           value="{{ old('date', $transaction->date) }}"
                           required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="time" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Hora
                    </label>
                    <input type="time"
                           id="time"
                           name="time"
                           value="{{ old('time', $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '') }}"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="description"
                       name="description"
                       value="{{ old('description', $transaction->description) }}"
                       required
                       autocomplete="off"
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
            </div>

            <!-- Monto y Cuenta -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Monto <span class="text-red-500">*</span>
                    </label>
                    @php
                        $displayAmount = $transaction->type === 'gasto' ? -$transaction->amount : $transaction->amount;
                    @endphp
                    <input type="number"
                           id="amount"
                           name="amount"
                           step="0.01"
                           value="{{ old('amount', $displayAmount) }}"
                           required
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-lg font-bold focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Usa signo negativo (−) para gastos
                    </p>
                </div>

                <div>
                    <label for="account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Cuenta <span class="text-red-500">*</span>
                    </label>
                    <select id="account_id" name="account_id" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                        <option value="">-- Seleccione cuenta --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Categoría y Tipo -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Categoría
                        <span id="auto-label" class="text-green-600 dark:text-green-400 font-bold text-xs"></span>
                    </label>
                    <select id="category_id"
                            name="category_id"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition
                                   ring-0 ring-green-500/0
                                   data-auto:ring-4 data-auto:ring-green-500/30 data-auto:border-green-600 data-auto:shadow-lg data-auto:shadow-green-500/10">
                        <option value="">-- Seleccione categoría --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    data-type="{{ $category->type }}"
                                    {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                        <option value="ingreso" {{ old('type', $transaction->type) == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                        <option value="gasto" {{ old('type', $transaction->type) == 'gasto' ? 'selected' : '' }}>Gasto</option>
                    </select>
                </div>
            </div>

            <!-- Botón Actualizar -->
            <div class="pt-6">
                <button id="btn-actualizar" type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold py-4 px-6 rounded-lg text-lg transition duration-200 shadow-md hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Transacción
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Script del auto-resaltado (el mismo que en create y que SÍ funciona) --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const descriptionInput = document.getElementById('description');
    const categorySelect = document.getElementById('category_id');
    const typeSelect = document.getElementById('type');
    const amountInput = document.getElementById('amount');
    const autoLabel = document.getElementById('auto-label');

    const rules = {!! json_encode($rules ?? []) !!};

    function applyAutoStyle() {
        categorySelect.classList.remove('ring-0', 'ring-green-500/0');
        categorySelect.classList.add('ring-4', 'ring-green-500/30', 'border-green-600', 'shadow-lg', 'shadow-green-500/10');
    }

    function removeAutoStyle() {
        categorySelect.classList.remove('ring-4', 'ring-green-500/30', 'border-green-600', 'shadow-lg', 'shadow-green-500/10');
        categorySelect.classList.add('ring-0', 'ring-green-500/0');
    }

    function suggest() {
        if (!descriptionInput.value.trim()) {
            removeAutoStyle();
            autoLabel.textContent = '';
            return;
        }

        const desc = descriptionInput.value.toLowerCase();
        const rawAmount = parseFloat(amountInput.value) || 0;
        const inferredType = rawAmount < 0 ? 'gasto' : 'ingreso';

        for (const rule of rules) {
            if (rule.type !== inferredType) continue;

            const matchKeyword = rule.keyword && desc.includes(rule.keyword.toLowerCase());
            const matchRegex = rule.regex && new RegExp(rule.regex, 'i').test(desc);

            if (matchKeyword || matchRegex) {
                if (categorySelect.value != rule.cat_id) {
                    categorySelect.value = rule.cat_id;
                    typeSelect.value = rule.type;
                    autoLabel.textContent = ' (auto)';
                    applyAutoStyle();
                }
                return;
            }
        }
        removeAutoStyle();
        autoLabel.textContent = '';
    }

    function syncTypeFromCategory() {
        const selected = categorySelect.options[categorySelect.selectedIndex];
        if (selected?.value) {
            const catType = selected.getAttribute('data-type');
            if (catType) typeSelect.value = catType;
        }
    }

    // Eventos
    descriptionInput?.addEventListener('input', suggest);
    amountInput?.addEventListener('input', suggest);
    categorySelect?.addEventListener('change', syncTypeFromCategory);

    // Al cargar
    syncTypeFromCategory();
    suggest();
});
</script>

{{-- Script de Navegación por Teclado (Flechas + Botón) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Asignar ID al botón de submit para identificarlo en el mapa
    const submitBtn = document.querySelector('button[id="btn-actualizar"]'); //button[type="submit"]
    // const linkReturn = document.querySelectorById("volver-historial")
    if(submitBtn) submitBtn.id = 'submit-btn';

    // 2. Mapa de navegación actualizado
    const navMap = {
        'date':        { 'ArrowRight': 'time', 'ArrowDown': 'description', 'ArrowUp': 'volver-historial' },
        'time':        { 'ArrowLeft': 'date', 'ArrowDown': 'description', 'ArrowUp': 'volver-historial' },
        'description': { 'ArrowUp': 'date', 'ArrowDown': 'amount' },
        
        'amount':      { 'ArrowUp': 'description', 'ArrowRight': 'account_id', 'ArrowDown': 'category_id' },
        'account_id':  { 'ArrowUp': 'description', 'ArrowLeft': 'amount', 'ArrowDown': 'type' },
        
        'category_id': { 'ArrowUp': 'amount', 'ArrowRight': 'type', 'ArrowDown': 'submit-btn' },
        'type':        { 'ArrowUp': 'account_id', 'ArrowLeft': 'category_id', 'ArrowDown': 'submit-btn' },
        
        // Configuración del botón:
        'submit-btn':  { 'ArrowUp': 'category_id', 'ArrowLeft': 'category_id', 'ArrowRight': 'type' }, 
        'volver-historial':  { 'ArrowDown': 'time' }
    };

    document.addEventListener('keydown', function(e) {
        // Usar Ctrl + Flechas (puedes cambiar a e.altKey si prefieres)
        if (!e.ctrlKey) return; 

        if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) return;

        const currentId = document.activeElement.id;
        
        if (currentId && navMap[currentId] && navMap[currentId][e.key]) {
            e.preventDefault();
            const targetId = navMap[currentId][e.key];
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.focus();
                if (targetElement.tagName === 'INPUT') targetElement.select();
            }
        }
    });
});
</script>

@endpush