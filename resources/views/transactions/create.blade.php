@extends('layouts.app1')

@section('title', 'Registrar Transacción')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Título -->
        <h1 class="text-2xl md:text-3xl font-bold text-center text-gray-800 dark:text-gray-100 mb-8">
            Registrar Movimiento
        </h1>

        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulario -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 md:p-8 lg:p-10">
            <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Fecha y Hora -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Fecha <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               id="date"
                               name="date"
                               value="{{ date('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label for="time" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Hora <span class="text-red-500">*</span>
                        </label>
                        <input type="time"
                               id="time"
                               name="time"
                               value="{{ date('H:i') }}"
                               required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
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
                           placeholder="Ej: Pago de servicios, Venta del día..."
                           required
                           autofocus
                           autocomplete="off"
                           class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <!-- Monto y Cuenta -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Monto <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="amount"
                               name="amount"
                               step="0.01"
                               placeholder="0.00"
                               required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-lg font-bold focus:ring-2 focus:ring-blue-4 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Usa signo negativo (−) para gastos
                        </p>
                    </div>

                    <div>
                        <label for="account_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Cuenta <span class="text-red-500">*</span>
                        </label>
                        <select id="account_id"
                                name="account_id"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">-- Seleccione cuenta --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
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
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition
                                    ring-0 ring-green-500/0 
                                    data-auto:ring-4 data-auto:ring-green-500/30 data-auto:border-green-600 data-auto:shadow-lg data-auto:shadow-green-500/10">
                            <option value="">-- Seleccione categoría --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <select id="type"
                                name="type"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">-- Tipo --</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="gasto">Gasto</option>
                        </select>
                    </div>
                </div>

                <!-- Botón Guardar -->
                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                        Guardar Transacción
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const descriptionInput = document.getElementById('description');
        const categorySelect = document.getElementById('category_id');
        const typeSelect = document.getElementById('type');
        const amountInput = document.getElementById('amount');
        const autoLabel = document.getElementById('auto-label');

        const rules = {!! json_encode(
            \App\Models\CategorizationRule::all()->map(function($r) {
                return [
                    'keyword' => $r->keyword ? strtolower($r->keyword) : null,
                    'regex' => $r->regex,
                    'cat_id' => $r->category_id,
                    'type' => $r->type
                ];
            })->filter()->values()
        ) !!};

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
                resetAuto();
                return;
            }

            const desc = descriptionInput.value.toLowerCase();
            const rawAmount = parseFloat(amountInput.value) || 0;
            const inferredType = rawAmount < 0 ? 'gasto' : 'ingreso';
            let matched = false;

            for (const rule of rules) {
                if (rule.type !== inferredType && typeSelect.value !== '') continue;

                const matchKeyword = rule.keyword && desc.includes(rule.keyword);
                const matchRegex = rule.regex && new RegExp(rule.regex, 'i').test(desc);

                if (matchKeyword || matchRegex) {
                    if (categorySelect.value != rule.cat_id) {
                        categorySelect.value = rule.cat_id;
                        syncTypeFromCategory();
                    }
                    autoLabel.textContent = ' (auto)';
                    applyAutoStyle();
                    matched = true;
                    break;
                }
            }

            if (!matched) resetAuto();
        }

        function syncTypeFromCategory() {
            const selected = categorySelect.options[categorySelect.selectedIndex];
            if (selected?.value) {
                const catType = selected.getAttribute('data-type');
                if (catType === 'ingreso' || catType === 'gasto') {
                    typeSelect.value = catType;
                }
            }
        }

        function resetAuto() {
            autoLabel.textContent = '';
            removeAutoStyle();
        }

        // Eventos
        descriptionInput?.addEventListener('input', suggest);
        amountInput?.addEventListener('input', suggest);
        amountInput?.addEventListener('change', suggest);

        categorySelect?.addEventListener('change', function () {
            syncTypeFromCategory();
            if (this.value && !autoLabel.textContent.includes('auto')) {
                autoLabel.textContent = ' (seleccionada)';
                applyAutoStyle(); // opcional: también resaltar si elige manual
            }
        });

        // Ejecutar al cargar
        suggest();
    });
</script>
@endpush