@extends('layouts.app1')

@section('title', 'Arqueo de Caja')

@push('styles')
    <style>
        /* (Tus estilos de listado) */
        .content { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .balance { font-weight: bold; }
        .balance.positive { color: #28a745; }
        .balance.negative { color: #dc3545; }
        .btn-edit { color: #007bff; text-decoration: none; }
        .btn-delete { color: #dc3545; background: none; border: none; cursor: pointer; padding: 0; }
    </style>
@endpush

@section('content')
    <div class="content">
        <h1>Gestión de Cuentas</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <a href="{{ route('cuentas.create') }}" class="btn" style="margin-bottom: 20px;">
            + Crear Nueva Cuenta
        </a>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Saldo Inicial</th>
                    <th>Saldo Actual (Calculado)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>${{ number_format($account->initial_balance, 2) }}</td>
                        <td class="balance {{ $account->current_balance >= 0 ? 'positive' : 'negative' }}">
                            ${{ number_format($account->current_balance, 2) }}
                        </td>
                        <td>
                            <a href="{{ route('cuentas.edit', $account) }}" class="btn-edit">Editar</a>
                            
                            <form action="{{ route('cuentas.destroy', $account) }}" method="POST" style="display: inline-block; margin-left: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro? Solo puedes eliminar cuentas sin transacciones.')">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay cuentas creadas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection