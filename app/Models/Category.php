<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * Propiedad para asignación masiva.
     * Define qué campos se pueden "rellenar" masivamente al crear un registro.
     */
    protected $fillable = [
        'name',
        'type',
        'parent_id'
    ];

    /**
     * Define la relación: una Categoría (Category) pertenece a muchas Transacciones (Transaction).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Define la relación: una Categoría (Category) puede tener una Categoría padre (parent category).
     */
    public function parent(): BelongsTo
    {
        // Le indicamos explícitamente que la clave foránea es 'parent_id'
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Define la relación: esta categoría tiene muchos hijos.
     */
    public function children(): HasMany
    {
        // Le indicamos explícitamente que la clave foránea es 'parent_id'
        return $this->hasMany(Category::class, 'parent_id');
    }
}
