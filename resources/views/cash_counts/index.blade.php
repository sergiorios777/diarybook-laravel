<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contador de Efectivo (Arqueo)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.9/cdn.min.js" defer></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f0f2f5; color: #333; }
        
        /* Navbar */
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .navbar a.btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 4px; }

        /* Contenedor Principal */
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 30px; }

        /* Grid de Tablas */
        .grid-money { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        @media (max-width: 768px) { .grid-money { grid-template-columns: 1fr; } }

        /* Tarjetas */
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { background: #f8f9fa; padding: 15px; border-bottom: 1px solid #eee; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; text-align: center; }
        .card-body { padding: 0; }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f1f3f5; font-size: 0.85rem; color: #666; text-align: center; padding: 10px; }
        td { padding: 8px 15px; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
        
        /* Inputs */
        input[type="number"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 1rem; transition: border 0.3s; }
        input[type="number"]:focus { border-color: #007bff; outline: none; background-color: #fbfdff; }
        
        /* Totales */
        .subtotal-display { font-family: monospace; font-size: 1.1rem; text-align: right; font-weight: bold; color: #444; }
        .section-total { padding: 15px; background-color: #f8fff9; text-align: right; font-weight: bold; font-size: 1.2rem; color: #28a745; border-top: 2px solid #e9ecef; }

        /* Resumen Final */
        .summary-panel { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
        .summary-item { flex: 1; min-width: 200px; text-align: center; }
        .summary-item label { display: block; font-size: 0.9rem; color: #666; margin-bottom: 5px; text-transform: uppercase; }
        .summary-value { font-size: 2rem; font-weight: bold; color: #2c3e50; }
        
        /* Comparador */
        .comparator { border-top: 1px solid #eee; margin-top: 20px; padding-top: 20px; width: 100%; }
        .diff-positive { color: #28a745; } /* Sobra dinero */
        .diff-negative { color: #dc3545; } /* Falta dinero */
        .diff-neutral { color: #888; }     /* Cuadra perfecto */

        select { padding: 10px; border-radius: 4px; border: 1px solid #ccc; font-size: 1rem; width: 100%; max-width: 300px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('transactions.index') }}">Volver</a>
        </div>
    </nav>

    <div class="container" x-data="cashCounter()">
        
        <h1>💰 Arqueo de Caja (Contador)</h1>

        <div class="summary-panel">
            <div class="summary-item">
                <label>Total Efectivo Contado</label>
                <div class="summary-value" x-text="formatCurrency(grandTotal)"></div>
            </div>
            
            <div class="summary-item comparator">
                <label>Comparar con Saldo del Sistema</label>
                
                <div style="margin-bottom: 10px; display: flex; justify-content: center; gap: 10px; align-items: center;">
                    <select x-model="selectedAccountId" @change="fetchBalance()">
                        <option value="">-- Seleccionar Cuenta --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" data-initial-balance="{{ $account->current_balance }}">
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>

                    <button 
                        x-show="selectedAccountId" 
                        @click="fetchBalance()" 
                        title="Actualizar saldo del sistema"
                        style="padding: 8px 12px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        <span x-show="!isLoading">🔄</span>
                        <span x-show="isLoading">...</span>
                    </button>
                </div>
                
                <div x-show="selectedAccountId">
                    <div style="font-size: 1.1em;">
                        Saldo Sistema: <strong x-text="formatCurrency(systemBalance)"></strong>
                    </div>
                    <div style="margin-top: 5px;">
                        Diferencia: 
                        <span :class="differenceClass" x-text="formatCurrency(difference)"></span>
                    </div>
                    <small x-text="differenceText" style="color: #666;"></small>
                </div>
            </div>
        </div>

        <br>

        <div class="grid-money">
            
            <div class="card">
                <div class="card-header">Monedas</div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Denominación</th>
                                <th style="width: 100px;">Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(coin, index) in coins" :key="index">
                                <tr>
                                    <td style="text-align: center; font-weight: bold;">
                                        S/ <span x-text="coin.denom.toFixed(2)"></span>
                                    </td>
                                    <td>
                                        <input type="number" min="0" x-model.number="coin.qty" placeholder="0" onfocus="this.select()">
                                    </td>
                                    <td class="subtotal-display">
                                        S/ <span x-text="(coin.denom * coin.qty).toFixed(2)"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="section-total">
                    Total Monedas: S/ <span x-text="formatNumber(totalCoins)"></span>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Billetes</div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Denominación</th>
                                <th style="width: 100px;">Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(bill, index) in bills" :key="index">
                                <tr>
                                    <td style="text-align: center; font-weight: bold;">
                                        S/ <span x-text="bill.denom.toFixed(2)"></span>
                                    </td>
                                    <td>
                                        <input type="number" min="0" x-model.number="bill.qty" placeholder="0" onfocus="this.select()">
                                    </td>
                                    <td class="subtotal-display">
                                        S/ <span x-text="(bill.denom * bill.qty).toFixed(2)"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="section-total">
                    Total Billetes: S/ <span x-text="formatNumber(totalBills)"></span>
                </div>
            </div>

        </div>

        <div style="text-align: center; margin-bottom: 40px;">
            <button @click="reset()" style="background-color: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                Limpiar / Reiniciar
            </button>
        </div>

    </div>

    <script>
        function cashCounter() {
            return {
                // Datos iniciales
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
                isLoading: false, // Estado de carga

                // Getters (Sin cambios)
                get totalCoins() { return this.coins.reduce((sum, item) => sum + (item.denom * (item.qty || 0)), 0); },
                get totalBills() { return this.bills.reduce((sum, item) => sum + (item.denom * (item.qty || 0)), 0); },
                get grandTotal() { return this.totalCoins + this.totalBills; },
                
                get difference() { return this.grandTotal - this.systemBalance; },
                get differenceClass() {
                    const diff = this.difference;
                    if (Math.abs(diff) < 0.01) return 'diff-neutral';
                    return diff > 0 ? 'diff-positive' : 'diff-negative';
                },
                get differenceText() {
                    const diff = this.difference;
                    if (Math.abs(diff) < 0.01) return '¡La caja cuadra perfectamente!';
                    return diff > 0 ? 'Sobra dinero (Ingreso extra?)' : 'Falta dinero (Gasto no registrado?)';
                },

                // --- NUEVA FUNCIÓN: Obtener saldo fresco del servidor ---
                async fetchBalance() {
                    if (!this.selectedAccountId) {
                        this.systemBalance = 0;
                        return;
                    }

                    this.isLoading = true;

                    try {
                        // 1. Intentamos leer el valor inicial del HTML primero (para respuesta inmediata)
                        const select = document.querySelector(`select[x-model="selectedAccountId"]`);
                        const option = select.querySelector(`option[value="${this.selectedAccountId}"]`);
                        
                        // 2. Llamada AJAX al servidor para obtener el dato REAL y ACTUALIZADO
                        const response = await fetch(`/cuentas/${this.selectedAccountId}/saldo`);
                        
                        if (response.ok) {
                            const data = await response.json();
                            // Actualizamos el saldo con el dato fresco de la base de datos
                            this.systemBalance = parseFloat(data.balance);
                        } else {
                            console.error("Error al obtener saldo");
                            // Fallback al dato estático si falla la red
                            if(option) this.systemBalance = parseFloat(option.getAttribute('data-initial-balance'));
                        }

                    } catch (error) {
                        console.error(error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                formatNumber(value) {
                    return value.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                formatCurrency(value) {
                    return 'S/ ' + this.formatNumber(value);
                },
                reset() {
                    this.coins.forEach(c => c.qty = '');
                    this.bills.forEach(b => b.qty = '');
                    this.selectedAccountId = '';
                    this.systemBalance = 0;
                }
            }
        }
    </script>

</body>
</html>