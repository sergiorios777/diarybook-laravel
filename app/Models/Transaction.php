<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Propiedad para asignación masiva.
     * Define qué campos se pueden "rellenar" masivamente al crear un registro.
     */
    protected $fillable = [
        'account_id',
        'category_id',
        'description',
        'type',
        'amount',
        'date',
        'time',
    ];

    /**
     * Define la relación: una Transacción (Transaction) pertenece a una Cuenta (Account).
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
    /**
     * Define la relación: una Transacción (Transaction) pertenece a una Categoría (Category).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
