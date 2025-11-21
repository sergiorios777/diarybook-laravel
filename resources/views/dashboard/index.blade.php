<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mi Tienda</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f7f6; }
        .navbar { background-color: #fff; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: bold; margin: 0 10px; }
        .navbar a.btn { background-color: #007bff; color: white; padding: 8px 12px; border-radius: 4px; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; }
        .card-total { background-color: #333; color: white; padding: 30px; border-radius: 8px; text-align: center; margin-bottom: 25px; }
        .card-total h2 { margin: 0 0 10px 0; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
        .card-total .amount { font-size: 2.5rem; font-weight: bold; }
        .account-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .account-card { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .account-card h3 { margin: 0 0 15px 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .account-card .balance { font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; }
        .account-card .balance.positive { color: #28a745; }
        .account-card .balance.negative { color: #dc3545; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><strong>Mi Dashboard</strong></a>
        <div>
            <a href="{{ route('reports.expenses') }}">Gastos</a>
            <a href="{{ route('reports.income') }}">Ingresos</a>
            <a href="{{ route('reports.weekly') }}">Semanal</a>
            <a href="{{ route('reports.monthly') }}">Mensual</a>
            <a href="{{ route('reports.detailed') }}">Detallado</a>
            <a href="{{ route('cash_counts.index') }}">Arqueo</a>
            
            <a href="{{ route('transactions.index') }}">Historial</a>
            <a href="{{ route('categorias.index') }}">Categorías</a>
            <a href="{{ route('cuentas.index') }}">Cuentas</a>
            <a href="{{ route('transactions.create') }}" class="btn">+ Nueva Transacción</a>
        </div>
    </nav>

    <div class="container">

        <div class="card-total">
            
            <h2>Saldo Total (Todas las Cuentas)</h2>
            <div class="amount">
                ${{ number_format($total_general, 2) }}
            </div>

            <button onclick="printDashboard()" style="margin-top: 15px; background-color: rgba(255,255,255,0.2); border: 1px solid #fff; color: #fff; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
                <span>🖨️</span> Imprimir Reporte de Saldos
            </button>
        </div>

        <h2>Saldos por Cuenta</h2>
        
        <div class="account-list">
            
            @foreach($accounts as $account)
                <div class="account-card">
                    <h3>{{ $account->name }}</h3>
                    <div class="balance {{ $account->current_balance >= 0 ? 'positive' : 'negative' }}">
                        ${{ number_format($account->current_balance, 2) }}
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <script>
        function printDashboard() {
            // 1. Pasamos los datos de PHP a JS
            const totalGeneral = {{ $total_general }};
            
            // Mapeamos las cuentas para tener nombre y saldo en un array limpio
            const accounts = [
                @foreach($accounts as $account)
                {
                    name: "{{ $account->name }}",
                    balance: {{ $account->current_balance }}
                },
                @endforeach
            ];

            // 2. Datos de fecha y hora
            const now = new Date();
            const dateStr = now.toLocaleDateString('es-PE');
            const timeStr = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });

            // Helper para formato de moneda
            const formatMoney = (amount) => {
                return 'S/ ' + amount.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            // 3. Construcción del HTML (Estilo Ticket 80mm Grueso)
            let html = `
            <html>
            <head>
                <title>Reporte de Saldos</title>
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
                    
                    h2 { margin: 0; font-size: 18px; font-weight: 900; letter-spacing: 0px; }
                    p { margin: 2px 0; }

                    table { 
                        width: 100%; 
                        border-collapse: collapse; 
                        margin-bottom: 10px; 
                        table-layout: fixed; /* Alineación perfecta */
                    }
                    
                    th, td {
                        font-size: 15px; 
                        padding: 4px 0; /* Un poco más de aire entre cuentas */
                        vertical-align: bottom;
                        border-bottom: 1px dotted #ccc; /* Línea suave entre cuentas */
                    }

                    /* Columna 1: Nombre Cuenta (60%) - Izquierda */
                    th:nth-child(1), td:nth-child(1) { width: 60%; text-align: left; }

                    /* Columna 2: Saldo (40%) - Derecha */
                    th:nth-child(2), td:nth-child(2) { width: 40%; text-align: right; }

                    .total-line { 
                        border-top: 2px solid #000; 
                        font-size: 17px; 
                        margin-top: 10px; 
                        padding-top: 5px; 
                        display: flex; 
                        justify-content: space-between;
                    }

                    .footer { text-align: center; margin-top: 20px; font-size: 10px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>ESTADO DE CUENTAS</h2>
                    <p>${dateStr} ${timeStr}</p>
                    <p style="font-size: 12px;">RESUMEN GENERAL</p>
                </div>

                <table>
                    <thead>
                        <tr style="border-bottom: 1px solid #000;">
                            <th>CUENTA</th>
                            <th>SALDO</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${accounts.map(acc => `
                            <tr>
                                <td>${acc.name}</td>
                                <td>${formatMoney(acc.balance)}</td>
                            </tr>`).join('')}
                    </tbody>
                </table>

                <div class="total-line">
                    <span>TOTAL:</span>
                    <span>${formatMoney(totalGeneral)}</span>
                </div>

                <div class="footer">
                    <p>--- Fin del Reporte ---</p>
                </div>
            </body>
            </html>
            `;

            // 4. Imprimir
            const printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write(html);
            printWindow.document.close();
            printWindow.focus();
            
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }
    </script>

</body>
</html>