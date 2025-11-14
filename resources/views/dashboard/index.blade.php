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
            <a href="{{ route('reports.expenses') }}">Informe Gastos</a>
            <a href="{{ route('reports.income') }}">Informe Ingresos</a>
            
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

</body>
</html>