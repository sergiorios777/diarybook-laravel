<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Transacciones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f9f9f9; }
        .btn { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .monto-ingreso { color: #28a745; font-weight: bold; }
        .monto-gasto { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Historial de Movimientos</h1>

        <a href="{{ route('transactions.create') }}" class="btn">
            + Registrar Nueva Transacción
        </a>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Cuenta</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @if($transactions->isEmpty())
                    <tr>
                        <td colspan="5">No hay transacciones registradas todavía.</td>
                    </tr>
                @else
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->description }}</td>
                            
                            <td>{{ $transaction->account->name }}</td>
                            <td>{{ $transaction->category ? $transaction->category->name : 'Sin Categoría' }}</td>

                            <td class="{{ $transaction->type == 'ingreso' ? 'monto-ingreso' : 'monto-gasto' }}">
                                {{ $transaction->type == 'ingreso' ? '+' : '-' }}
                                ${{ number_format($transaction->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

    </div>

</body>
</html>