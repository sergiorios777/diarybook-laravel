<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorizationRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rules = [
            // Alquiler
            ['keyword' => 'alquiler', 'category' => 'Alquiler tienda', 'type' => 'gasto'],

            // Préstamos
            ['keyword' => 'ptmo lk', 'category' => 'Pago préstamos', 'type' => 'gasto'],
            ['keyword' => 'préstamo lk', 'category' => 'PRÉSTAMOS', 'type' => 'ingreso'],

            // Ventas
            ['keyword' => 'venta if', 'category' => 'Venta IF', 'type' => 'ingreso'],
            ['keyword' => 'venta caja', 'category' => 'Venta Cajas y botellas', 'type' => 'ingreso'],
            ['keyword' => 'venta botellas', 'category' => 'Venta Cajas y botellas', 'type' => 'ingreso'],
            ['keyword' => 'ventas del día', 'category' => 'Venta IF', 'type' => 'ingreso'],

            // Servicios públicos y web
            ['keyword' => 'luz', 'category' => 'Servicio de Luz', 'type' => 'gasto'],
            ['keyword' => 'agua', 'category' => 'Servicios de Agua', 'type' => 'gasto'],
            ['keyword' => 'internet', 'category' => 'Servicio de Internet', 'type' => 'gasto'],
            ['keyword' => 'kyte', 'category' => 'Servicio de tienda web', 'type' => 'gasto'],
            ['keyword' => 'microsoft 365', 'category' => 'Licencia M365', 'type' => 'gasto'],
            ['keyword' => 'm365', 'category' => 'Licencia M365', 'type' => 'gasto'],

            // Personal
            ['keyword' => 'ayudante', 'category' => 'Pago de Ayudante', 'type' => 'gasto'],
            ['keyword' => 'guardián', 'category' => 'Pago de guardián', 'type' => 'gasto'],

            // Transferencias internas (con regex y keyword opcional)
            ['keyword' => 't.int', 'category' => 'Transferencias internas enviadas [T.INT]', 'type' => 'gasto'],
            ['keyword' => 't.int', 'category' => 'Transferencias internas recibidas [T.INT]', 'type' => 'ingreso'],
            ['regex' => 'enviado.*t\.int', 'category' => 'Transferencias internas enviadas [T.INT]', 'type' => 'gasto'],
            ['regex' => 'recibido.*t\.int', 'category' => 'Transferencias internas recibidas [T.INT]', 'type' => 'ingreso'],

            // Compras
            ['keyword' => 'compra de', 'category' => 'Compra productos', 'type' => 'gasto'],
            ['keyword' => 'coca-cola', 'category' => 'Compra productos', 'type' => 'gasto'],
            ['keyword' => 'kola real', 'category' => 'Compra productos', 'type' => 'gasto'],
        ];

        foreach ($rules as $r) {
            $category = \App\Models\Category::where('name', 'like', '%' . $r['category'] . '%')->first();

            if ($category) {
                \App\Models\CategorizationRule::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'keyword'     => $r['keyword'] ?? null,
                        'regex'       => $r['regex'] ?? null,
                        'type'        => $r['type'],
                    ],
                    [
                        'priority' => 50,
                    ]
                );
            }
        }
    }
}
