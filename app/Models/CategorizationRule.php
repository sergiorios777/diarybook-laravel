<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategorizationRule extends Model
{
    protected $fillable = ['category_id', 'keyword', 'regex', 'type', 'priority', 'usages'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeForType($query, $type)
    {
        return $query->where('type', $type);
    }
}
