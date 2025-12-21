<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'total_counted',
        'system_balance',
        'difference',
        'details',
    ];

    // Esto es magia pura: Laravel convierte el JSON a Array automáticamente
    protected $casts = [
        'details' => 'array',
        'total_counted' => 'decimal:2',
        'system_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
