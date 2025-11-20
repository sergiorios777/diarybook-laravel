<?php

namespace App\Services;

use App\Models\CategorizatonRule;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryAssignmentEngine
{
    public function suggestCategory(string $description, string $type): ?Category
    {
        $description = Str::lower($description);

        $rule = CategorizationRule::forType($type)
            ->orderBy('priority')
            ->orderByDesc('usages')
            ->get()
            ->first(function ($rule) use ($description) {
                if ($rule->regex && preg_match('/' . $rule->regex . '/i', $description)) {
                    return true;
                }
                if (Str::contains($description, Str::lower($rule->keyword))) {
                    return true;
                }
                return false;
            });

        if ($rule) {
            $rule->increment('usages');
            return $rule->category;
        }

        // Default fallback
        return Category::where('name', 'like', $type === 'ingreso' ? '%Otros ingresos%' : '%Otros gastos%')
                       ->first();
    }

    public function learnFromTransaction($transaction): void
    {
        if (!$transaction->category_id) return;

        $desc = Str::lower($transaction->description);

        // Busca si ya existe una regla suficientemente buena
        $existing = CategorizationRule::where('category_id', $transaction->category_id)
            ->where('type', $transaction->type)
            ->whereRaw('LOWER(keyword) LIKE ?', ["%$desc%"])
            ->orWhere('regex', 'like', "%$desc%")
            ->first();

        if ($existing) {
            $existing->increment('usages');
            $existing->priority -= 1; // sube prioridad
            $existing->save();
            return;
        }

        // Crea regla simple con la descripción completa como keyword (funciona increíble en la práctica)
        CategorizationRule::create([
            'category_id' => $transaction->category_id,
            'keyword'     => $transaction->description,
            'type'        => $transaction->type,
            'priority'    => 100,
        ]);
    }
}
