@extends('layouts.app')

@section('title', 'Historial de Transacciones')

@push('styles')
    <style>
        /* --- CONTENEDOR TIPO TARJETA --- */
        .container { 
            max-width: 100%; /* Ocupa todo el ancho disponible en el área de contenido */
            background-color: #fff; 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
            padding: 25px;
        }
        
        /* Cabecera con título y botón */
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        h1 { margin: 0; font-size: 1.5rem; color: #343a40; }

        /* --- ESTILOS DE LA TABLA --- */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        thead th { 
            background-color: #343a40; 
            color: white; 
            padding: 12px 15px; 
            text-align: left; 
            font-size: 0.85rem; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        /* Bordes redondeados para la cabecera */
        thead th:first-child { border-top-left-radius: 6px; }
        thead th:last-child { border-top-right-radius: 6px; }

        tbody td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #f1f1f1; 
            color: #444;
            vertical-align: middle;
            font-size: 14px; 
        }

        tbody tr:hover { background-color: #f8f9fa; }

        /* --- COLUMNAS ESPECÍFICAS --- */
        .time-col { 
            color: #6c757d; 
            font-size: 13px; 
            font-family: 'Consolas', monospace; 
        }
        
        .amount-col {
            font-family: 'Consolas', monospace;
            font-size: 14px;
            font-weight: 700;
            text-align: right;
        }
        .monto-ingreso { color: #28a745; }
        .monto-gasto { color: #dc3545; }

        /* Etiquetas para cuentas */
        .badge-account {
            background: #eef2f7; 
            padding: 3px 8px; 
            border-radius: 4px; 
            font-size: 12px; 
            color: #555;
            font-weight: 500;
        }

        /* --- ACCIONES (BOTONES) --- */
        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
            white-space: nowrap;
        }

        .btn-action {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-edit { background-color: #e7f1ff; color: #0d6efd; border-color: #cff4fc; }
        .btn-edit:hover { background-color: #cfe2ff; }

        .btn-delete { background-color: #ffebe9; color: #dc3545; border-color: #fecaca; }
        .btn-delete:hover { background-color: #f8d7da; }

        /* Botón Principal (+ Registrar) */
        .btn-primary-action { 
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            text-decoration: none; 
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,123,255,0.2);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-primary-action:hover { background-color: #0056b3; }

        /* --- PAGINACIÓN --- */
        .pagination-wrapper { margin-top: 20px; display: flex; justify-content: center; }
        .pagination-wrapper nav div:first-child { display: none; } 
        .pagination-wrapper nav div:last-child { display: flex; gap: 5px; box-shadow: none !important; }
        .pagination-wrapper a, .pagination-wrapper span {
            padding: 8px 12px; border: 1px solid #ddd; color: #007bff; text-decoration: none; border-radius: 4px; font-size: 14px; background: white;
        }
        .pagination-wrapper span[aria-current="page"] { background-color: #007bff; color: white; border-color: #007bff; }
        .pagination-wrapper a:hover { background-color: #e9ecef; }
        .pagination-wrapper svg { width: 16px; height: 16px; vertical-align: middle; }
    </style>
@endpush

@section('content')

    <div class="container">
        
        <div class="header-flex">
            <h1>Historial de Movimientos</h1>
            <a href="{{ route('transactions.create') }}" class="btn-primary-action">
                <span>➕</span> Registrar Transacción
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th style="width: 30%;">Descripción</th>
                    <th>Cuenta</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th style="width: 140px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if($transactions->isEmpty())
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                            <div style="font-size: 3rem; margin-bottom: 10px;">📭</div>
                            No hay transacciones registradas todavía.
                        </td>
                    </tr>
                @else
                    @foreach($transactions as $transaction)
                        <tr>
                            <td style="font-weight: 500;">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                            </td>
                            
                            <td class="time-col">
                                {{ $transaction->time ? \Carbon\Carbon::parse($transaction->time)->format('H:i') : '--:--' }}
                            </td>

                            <td>{{ $transaction->description }}</td>
                            
                            <td>
                                <span class="badge-account">
                                    {{ $transaction->account->name }}
                                </span>
                            </td>
                            
                            <td>
                                {{ $transaction->category ? $transaction->category->name : '-' }}
                            </td>

                            <td class="amount-col {{ $transaction->type == 'ingreso' ? 'monto-ingreso' : 'monto-gasto' }}">
                                {{ $transaction->type == 'ingreso' ? '+' : '-' }}
                                ${{ number_format($transaction->amount, 2) }}
                            </td>
                            
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="btn-action btn-edit">
                                        Editar
                                    </a>

                                    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE') 
                                        <button type="submit" 
                                                class="btn-action btn-delete"
                                                onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">
                                                Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="pagination-wrapper">
            {{ $transactions->links() }}
        </div>

    </div>

@endsection