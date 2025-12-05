@extends('layouts.app1')

@section('title', 'Dashboard')

@section('content')
{{-- ====== CARD TOTAL GENERAL ====== --}}
<div class="bg-gray-900 text-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
    <h2 class="text-sm tracking-widest uppercase opacity-70">Saldo Total (Todas las Cuentas)</h2>

    <div class="text-4xl font-bold mt-2">
        ${{ number_format($total_general, 2) }}
    </div>

    <button 
        onclick="printDashboard()" 
        class="mt-4 px-4 py-2 rounded bg-white/20 hover:bg-white/30 transition border border-white/30 text-sm"
    >
        🖨️ Imprimir Reporte
    </button>
</div>


{{-- ====== LISTADO DE CUENTAS ====== --}}
<h2 class="text-xl font-semibold mt-10 text-gray-800 dark:text-gray-100">Saldos por Cuenta</h2>

<div class="mt-4 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

    @foreach($accounts as $account)

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
        
        <h3 class="text-lg font-semibold mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">
            {{ $account->name }}
        </h3>

        <div class="text-3xl font-bold
            {{ $account->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
            ${{ number_format($account->current_balance, 2) }}
        </div>

    </div>

    @endforeach

</div>

{{-- ====== SCRIPT DE IMPRESIÓN (igual al tuyo, solo formateado) ====== --}}
<script>
function printDashboard() {
    const totalGeneral = {{ $total_general }};

    const accounts = [
        @foreach($accounts as $account)
        { name: "{{ $account->name }}", balance: {{ $account->current_balance }} },
        @endforeach
    ];

    const now = new Date();
    const dateStr = now.toLocaleDateString('es-PE');
    const timeStr = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });

    const formatMoney = (amount) => {
        return 'S/ ' + amount.toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    let html = `
    <html>
    <head>
        <title>Reporte de Saldos</title>
        <style>
            @page { size: 72mm auto; margin: 0; }
            body {
                width: 71mm;
                margin: auto;
                font-family: Consolas, monospace;
                font-size: 15px;
                font-weight: 700;
                text-transform: uppercase;
            }
            .header {
                text-align: center;
                margin-bottom: 15px;
                padding-bottom: 5px;
                border-bottom: 2px dashed #000;
            }
            table { width: 100%; border-collapse: collapse; }
            th, td {
                padding: 4px 0;
                border-bottom: 1px dotted #ccc;
            }
            th:nth-child(1), td:nth-child(1) { width: 60%; text-align: left; }
            th:nth-child(2), td:nth-child(2) { width: 40%; text-align: right; }
            .total-line {
                border-top: 2px solid black;
                padding-top: 5px;
                font-size: 17px;
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
            }
            .footer { text-align: center; font-size: 10px; margin-top: 10px; }
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
                <tr><th>CUENTA</th><th>SALDO</th></tr>
            </thead>
            <tbody>
                ${accounts.map(acc =>
                    `<tr><td>${acc.name}</td><td>${formatMoney(acc.balance)}</td></tr>`
                ).join('')}
            </tbody>
        </table>

        <div class="total-line">
            <span>TOTAL:</span><span>${formatMoney(totalGeneral)}</span>
        </div>

        <div class="footer">--- Fin del Reporte ---</div>

    </body>
    </html>
    `;

    const printWindow = window.open('', '', 'height=600,width=400');
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => printWindow.print(), 250);
}
</script>

@endsection