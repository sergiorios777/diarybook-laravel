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
            
            <button @click="printReceipt()" style="background-color: #343a40; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; display: flex; align-items: center; gap: 5px;">
                <span>🖨️</span> Imprimir Ticket (PDF)
            </button>
        </div>

    </div>

    <script>
        function cashCounter() {
            return {
                // --- DATOS (Igual que antes) ---
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

                // --- GETTERS (Igual que antes) ---
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
                    if (Math.abs(diff) < 0.01) return '¡Cuadra perfecto!';
                    return diff > 0 ? 'Sobra dinero' : 'Falta dinero';
                },

                // --- FUNCIONES (Fetch y Format igual que antes) ---
                async fetchBalance() {
                    if (!this.selectedAccountId) { this.systemBalance = 0; return; }
                    this.isLoading = true;
                    try {
                        const select = document.querySelector(`select[x-model="selectedAccountId"]`);
                        const option = select.querySelector(`option[value="${this.selectedAccountId}"]`);
                        const response = await fetch(`/cuentas/${this.selectedAccountId}/saldo`);
                        if (response.ok) {
                            const data = await response.json();
                            this.systemBalance = parseFloat(data.balance);
                        } else {
                            if(option) this.systemBalance = parseFloat(option.getAttribute('data-initial-balance'));
                        }
                    } catch (error) { console.error(error); } finally { this.isLoading = false; }
                },
                formatNumber(value) { return value.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
                formatCurrency(value) { return 'S/ ' + this.formatNumber(value); },
                reset() {
                    this.coins.forEach(c => c.qty = '');
                    this.bills.forEach(b => b.qty = '');
                    this.selectedAccountId = '';
                    this.systemBalance = 0;
                },

                // --- FUNCIÓN FINAL: IMPRIMIR TICKET CON SUBTOTALES ---
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
                            @page { size: 80mm auto; margin: 0; }
                            
                            body { 
                                margin: 5mm 15mm; 
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

</body>
</html>