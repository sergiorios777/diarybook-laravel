<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;
    
    /**
     * Propiedad para asignación masiva.
     * Define qué campos se pueden "rellenar" masivamente al crear un registro.
     */
    protected $fillable = [
        'name',
        'initial_balance',
    ];

    /**
     * Define la relación: una Cuenta (Account) tiene muchas Transacciones (Transaction).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Define un "Accessor" para un atributo virtual 'current_balance'.
     * Esto nos permite obtener el saldo calculado de la cuenta
     * simplemente llamando a: $account->current_balance
     *
     * @return float
     */
    public function getCurrentBalanceAttribute(): float
    {
        // 1. Verificamos si los totales fueron precargados con 'withSum'.
        //    (Esto lo haremos en el controlador para que sea ultra eficiente).
        //    Si 'total_ingresos' no existe (is null), le asigna 0.
        $ingresos = $this->total_ingresos ?? 0;
        $gastos = $this->total_gastos ?? 0;

        // 2. Si los atributos NO están seteados, significa que 'withSum' no se usó.
        //    Como plan B (menos eficiente), los calculamos manualmente.
        //    Esto hace que nuestro modelo sea "robusto".
        if (!isset($this->total_ingresos)) {
            $ingresos = $this->transactions()->where('type', 'ingreso')->sum('amount');
        }
        if (!isset($this->total_gastos)) {
            $gastos = $this->transactions()->where('type', 'gasto')->sum('amount');
        }
        
        // 3. Retornamos el cálculo final
        return $this->initial_balance + $ingresos - $gastos;
    }
}
