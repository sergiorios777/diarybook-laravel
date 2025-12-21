@extends('layouts.app1')
@section('title', 'Arqueo de Caja')

@section('content')
<div class="max-w-5xl mx-auto" x-data="cashCounter()">
    <!-- TÍTULO -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 flex items-center justify-center gap-3">
            Arqueo de Caja
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Conteo físico vs sistema</p>
    </div>

    <!-- PANEL SUPERIOR: RESUMEN + COMPARADOR -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Total Contado -->
            <div class="text-center">
                <p class="text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                    Total Efectivo Contado
                </p>
                <p class="text-5xl font-bold text-gray-800 dark:text-gray-100">
                    S/ <span x-text="formatCurrency(grandTotal)"></span>
                </p>
            </div>

            <!-- Comparador con Sistema -->
            <div class="text-center lg:border-l lg:border-gray-300 dark:lg:border-gray-700 lg:pl-8">
                <p class="text-sm uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">
                    Comparar con Sistema
                </p>

                <div class="flex items-center justify-center gap-3 mb-4">
                    <select x-model="selectedAccountId" @change="fetchBalance()"
                            class="..."> <option value="">-- Seleccionar Cuenta --</option>
                        
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" 
                                    data-name="{{ $account->name }}"
                                    data-balance="{{ $account->current_balance }}">
                                {{ $account->name }} (S/ {{ number_format($account->current_balance, 2) }})
                            </option>
                        @endforeach

                    </select>
                    <button @click="fetchBalance()" x-show="selectedAccountId"
                            :disabled="isLoading" 
                            class="p-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white rounded-xl transition shadow-md flex items-center gap-2">
                        
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        
                        <span x-text="isLoading ? 'Actualizando...' : 'Refresh'"></span>
                    </button>
                </div>

                <div x-show="selectedAccountId" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Saldo en Sistema</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                        S/ <span x-text="formatCurrency(systemBalance)"></span>
                    </p>
                    <div class="mt-4 pt-4 border-t border-gray-300 dark:border-gray-600">
                        <p class="text-lg font-semibold" :class="differenceClass">
                            Diferencia: S/ <span x-text="formatCurrency(difference)"></span>
                        </p>
                        <p class="text-sm mt-1" :class="difference > 0 ? 'text-green-600' : difference < 0 ? 'text-red-600' : 'text-gray-500'"
                           x-text="differenceText"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID DE CONTEO: MONEDAS Y BILLETES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

        <!-- === MONEDAS === -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 text-center font-bold text-lg">
                Monedas
            </div>

            <!-- VERSIÓN ESCRITORIO: Tabla -->
            <div class="hidden lg:block">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Denominación</th>
                            <th class="px-6 py-4 text-center">Cantidad</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="coin in coins" :key="coin.denom">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-semibold">S/ <span x-text="coin.denom.toFixed(2)"></span></td>
                                <td class="px-6 py-4 text-center">
                                    <input type="number" min="0" x-model.number="coin.qty"
                                        class="w-24 px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-semibold">
                                    S/ <span x-text="(coin.denom * (coin.qty || 0)).toFixed(2)"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- VERSIÓN MÓVIL: Tarjetas verticales -->
            <div class="lg:hidden space-y-3 p-4">
                <template x-for="coin in coins" :key="coin.denom">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                S/ <span x-text="coin.denom.toFixed(2)"></span>
                            </div>
                            <div class="text-2xl font-mono font-bold text-blue-600 dark:text-blue-400">
                                S/ <span x-text="(coin.denom * (coin.qty || 0)).toFixed(2)"></span>
                            </div>
                        </div>
                        <input type="number" min="0" x-model.number="coin.qty" placeholder="Cantidad"
                            class="w-full px-4 py-3 text-center text-lg font-semibold border-2 border-blue-200 dark:border-blue-700 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </template>
            </div>

            <!-- Total Monedas (visible en ambas versiones) -->
            <div class="bg-green-50 dark:bg-green-900/30 px-6 py-5 border-t-4 border-green-500">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-700 dark:text-gray-300">Total Monedas</span>
                    <span class="text-2xl font-bold text-green-700 dark:text-green-400">
                        S/ <span x-text="formatNumber(totalCoins)"></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- === BILLETES === -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4 text-center font-bold text-lg">
                Billetes
            </div>

            <!-- VERSIÓN ESCRITORIO: Tabla -->
            <div class="hidden lg:block">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Denominación</th>
                            <th class="px-6 py-4 text-center">Cantidad</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="bill in bills" :key="bill.denom">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 font-semibold">S/ <span x-text="bill.denom.toFixed(2)"></span></td>
                                <td class="px-6 py-4 text-center">
                                    <input type="number" min="0" x-model.number="bill.qty"
                                        class="w-24 px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500">
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-semibold">
                                    S/ <span x-text="(bill.denom * (bill.qty || 0)).toFixed(2)"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- VERSIÓN MÓVIL: Tarjetas verticales -->
            <div class="lg:hidden space-y-3 p-4">
                <template x-for="bill in bills" :key="bill.denom">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-3">
                            <div class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                S/ <span x-text="bill.denom.toFixed(2)"></span>
                            </div>
                            <div class="text-2xl font-mono font-bold text-purple-600 dark:text-purple-400">
                                S/ <span x-text="(bill.denom * (bill.qty || 0)).toFixed(2)"></span>
                            </div>
                        </div>
                        <input type="number" min="0" x-model.number="bill.qty" placeholder="Cantidad"
                            class="w-full px-4 py-3 text-center text-lg font-semibold border-2 border-purple-200 dark:border-purple-700 rounded-xl focus:ring-4 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                </template>
            </div>

            <!-- Total Billetes -->
            <div class="bg-purple-50 dark:bg-purple-900/30 px-6 py-5 border-t-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-700 dark:text-gray-300">Total Billetes</span>
                    <span class="text-2xl font-bold text-purple-700 dark:text-purple-400">
                        S/ <span x-text="formatNumber(totalBills)"></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <button @click="saveCount()"
                :disabled="isSaving"
                class="px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 flex items-center gap-3">
            
            <svg x-show="!isSaving" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            <svg x-show="isSaving" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            
            <span x-text="isSaving ? 'Guardando...' : 'Guardar Arqueo'"></span>
        </button>
        <button @click="reset()"
                class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            Limpiar Todo
        </button>
        <button @click="printReceipt()"
                class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 flex items-center gap-3">
            Imprimir Ticket
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cashCounter() {
    return {
        coins: [
            { denom: 0.10, qty: '' }, { denom: 0.20, qty: '' }, { denom: 0.50, qty: '' },
            { denom: 1.00, qty: '' }, { denom: 2.00, qty: '' }, { denom: 5.00, qty: '' }
        ],
        bills: [
            { denom: 10.00, qty: '' }, { denom: 20.00, qty: '' }, { denom: 50.00, qty: '' },
            { denom: 100.00, qty: '' }, { denom: 200.00, qty: '' }
        ],
        selectedAccountId: '',
        systemBalance: 0,
        isLoading: false,
        isSaving: false,

        get totalCoins() { return this.coins.reduce((s, c) => s + c.denom * (c.qty || 0), 0); },
        get totalBills() { return this.bills.reduce((s, b) => s + b.denom * (b.qty || 0), 0); },
        get grandTotal() { return this.totalCoins + this.totalBills; },
        get difference() { return this.grandTotal - this.systemBalance; },

        get differenceClass() {
            if (Math.abs(this.difference) < 0.01) return 'text-gray-600';
            return this.difference > 0 ? 'text-green-600' : 'text-red-600';
        },
        get differenceText() {
            if (Math.abs(this.difference) < 0.01) return '¡Cuadra perfecto!';
            return this.difference > 0 ? 'Sobra efectivo' : 'Falta efectivo';
        },

        async fetchBalance() {
            if (!this.selectedAccountId) return;
            
            this.isLoading = true;

            try {
                const url = `/cuentas/${this.selectedAccountId}/saldo`;
                const response = await fetch(url);

                if (!response.ok) throw new Error('Error al conectar');

                const data = await response.json();

                // 1. Actualizamos el saldo principal (lo que ya tenías)
                this.systemBalance = parseFloat(data.balance) || 0;

                // --- NUEVO: Actualizar el texto visual del SELECT ---
                
                // a. Buscamos la opción seleccionada en el DOM
                //    Usamos una selección específica para encontrar el option dentro del select correcto
                const selectElement = document.querySelector('select[x-model="selectedAccountId"]');
                const selectedOption = selectElement.querySelector(`option[value="${this.selectedAccountId}"]`);

                if (selectedOption) {
                    // b. Obtenemos el nombre original que guardamos en el Paso 1
                    const accountName = selectedOption.getAttribute('data-name');
                    
                    // c. Formateamos el nuevo saldo (reutilizamos tu función formatNumber)
                    const newFormattedBalance = this.formatNumber(this.systemBalance);

                    // d. Reescribimos el texto visible de la opción
                    selectedOption.innerText = `${accountName} (S/ ${newFormattedBalance})`;

                    // e. (Opcional) Actualizamos también el data-balance para mantener consistencia
                    selectedOption.setAttribute('data-balance', this.systemBalance);
                }
                // ----------------------------------------------------

                console.log(`Saldo actualizado visualmente para: ${this.selectedAccountId}`);

            } catch (e) { 
                console.error(e);
                alert('No se pudo actualizar el saldo.');
            } finally {
                this.isLoading = false;
            }
        },

        formatNumber(v) { return v.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
        formatCurrency(v) { return this.formatNumber(v); },

        async saveCount() {
            if (this.grandTotal <= 0 && !confirm("El total es 0. ¿Deseas guardar un arqueo vacío?")) {
                return;
            }

            if (!this.selectedAccountId && !confirm("No has seleccionado una cuenta del sistema para comparar. ¿Guardar solo el conteo físico?")) {
                return;
            }

            this.isSaving = true;

            // Preparamos los datos
            const payload = {
                account_id: this.selectedAccountId || null,
                total_counted: this.grandTotal,
                system_balance: this.systemBalance,
                difference: this.difference,
                details: {
                    coins: this.coins.filter(c => c.qty > 0), // Solo enviamos lo que tiene cantidad
                    bills: this.bills.filter(b => b.qty > 0)
                }
            };

            try {
                const response = await fetch("{{ route('cash_counts.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    // Intentamos leer la respuesta para ver qué dijo el servidor
                    const errorBody = await response.text(); 
                    console.error("Error del servidor:", errorBody); // <--- Aquí verás el error de Laravel en la consola
                    throw new Error('Error en el servidor: ' + response.status);
                }

                const result = await response.json();

                if (result.success) {
                    alert('¡Arqueo guardado con éxito!');
                    this.reset(); // Limpiamos la pantalla
                } else {
                    alert('Error: ' + result.message);
                }

            } catch (error) {
                console.error(error);
                alert('Ocurrió un error al intentar guardar.');
            } finally {
                this.isSaving = false;
            }
        },

        reset() {
            this.coins.forEach(c => c.qty = '');
            this.bills.forEach(b => b.qty = '');
            this.selectedAccountId = '';
            this.systemBalance = 0;
        },

        printReceipt() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('es-PE');
            const timeStr = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });

            let accountName = "Sin comparar";
            if (this.selectedAccountId) {
                const select = document.querySelector(`select[x-model="selectedAccountId"]`);
                const option = select.options[select.selectedIndex];
                accountName = option.text.split('(')[0].trim();
            }

            const activeCoins = this.coins.filter(c => c.qty > 0);
            const activeBills = this.bills.filter(b => b.qty > 0);

            let html = `
            <html>
            <head>
                <title>Arqueo de Caja</title>
                <style>
                    @page { size: 72mm auto; margin: 0; }
                    
                    body { 
                        width: 71mm;
                        margin: auto; 
                        font-family: 'Consolas', 'Monaco', 'Lucida Console', monospace; 
                        font-size: 15px; 
                        font-weight: 700; 
                        letter-spacing: -0.5px; 
                        color: #000;
                        text-transform: uppercase; 
                    }

                    .header { 
                        text-align: center; 
                        margin-bottom: 15px; 
                        padding-bottom: 5px; 
                        border-bottom: 2px dashed #000; 
                    }
                    
                    h2 { margin: 0; font-size: 20px; font-weight: 900; letter-spacing: 0px; }
                    p { margin: 2px 0; }

                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-bottom: 2px; /* Reducido para pegar el subtotal */
                        table-layout: fixed; 
                    }
                    
                    th, td {
                        font-size: 15px; 
                        padding: 2px 0;
                        vertical-align: bottom;
                    }

                    th { border-bottom: 1px solid #000; }

                    /* Columnas: 40% - 20% - 40% */
                    th:nth-child(1), td:nth-child(1) { width: 40%; text-align: left; }
                    th:nth-child(2), td:nth-child(2) { width: 20%; text-align: center; }
                    th:nth-child(3), td:nth-child(3) { width: 40%; text-align: right; }

                    .section-title {
                        margin-top: 10px;
                        margin-bottom: 2px; 
                        text-decoration: underline;
                        font-size: 15px;
                    }

                    /* Estilo para el Subtotal de sección */
                    .subtotal-line {
                        text-align: right;
                        font-size: 15px;
                        font-style: italic;
                        border-top: 1px dashed #000;
                        padding-top: 2px;
                        margin-bottom: 5px;
                    }

                    .total-line { 
                        border-top: 2px solid #000; 
                        font-size: 16px; 
                        margin-top: 10px; 
                        padding-top: 5px; 
                        display: flex; 
                        justify-content: space-between;
                    }

                    .diff-section { 
                        margin-top: 15px; 
                        border: 1px solid #000; 
                        padding: 5px;
                        text-align: center;
                    }
                    
                    .footer { text-align: center; margin-top: 20px; font-size: 10px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>ARQUEO DE CAJA</h2>
                    <p>${dateStr} ${timeStr}</p>
                    <p style="font-size: 13px;">CUENTA: ${accountName}</p>
                </div>

                ${activeCoins.length > 0 ? `
                    <div class="section-title">MONEDAS</div>
                    <table>
                        <thead><tr><th>DENOM.</th><th>CANT.</th><th>TOTAL</th></tr></thead>
                        <tbody>
                            ${activeCoins.map(c => `
                                <tr>
                                    <td>${c.denom.toFixed(2)}</td>
                                    <td>${c.qty}</td>
                                    <td>${(c.denom * c.qty).toFixed(2)}</td>
                                </tr>`).join('')}
                        </tbody>
                    </table>
                    <div class="subtotal-line">
                        SUB. MONEDAS: S/ ${this.formatNumber(this.totalCoins)}
                    </div>
                ` : ''}

                ${activeBills.length > 0 ? `
                    <div class="section-title">BILLETES</div>
                    <table>
                        <thead><tr><th>DENOM.</th><th>CANT.</th><th>TOTAL</th></tr></thead>
                        <tbody>
                            ${activeBills.map(b => `
                                <tr>
                                    <td>${b.denom.toFixed(2)}</td>
                                    <td>${b.qty}</td>
                                    <td>${(b.denom * b.qty).toFixed(2)}</td>
                                </tr>`).join('')}
                        </tbody>
                    </table>
                    <div class="subtotal-line">
                        SUB. BILLETES: S/ ${this.formatNumber(this.totalBills)}
                    </div>
                ` : ''}

                <div class="total-line">
                    <span>TOTAL EFECTIVO:</span>
                    <span>S/ ${this.formatNumber(this.grandTotal)}</span>
                </div>

                <div class="diff-section">
                    ${this.selectedAccountId ? `
                        <p style="margin:0; font-size: 14px;">SISTEMA: S/ ${this.formatNumber(this.systemBalance)}</p>
                        <div style="font-size: 16px; margin-top: 4px;">
                            DIF: S/ ${this.formatNumber(this.difference)}
                        </div>
                        <p style="margin:0; font-size: 12px;">(${this.differenceText.toUpperCase()})</p>
                    ` : '<p style="margin:0;">SIN COMPARACIÓN</p>'}
                </div>

                <div class="footer">.</div>
            </body>
            </html>
            `;

            const printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => { printWindow.print(); }, 250);
        }
    }
}
</script>
@endpush