<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // --- Claves Foráneas (Vínculos) ---

            // Vínculo con Accounts (¿Dónde ocurrió?)
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onDelete('cascade'); // Si borras una cuenta, se borran sus transacciones.

            // Vínculo con Categories (¿En qué se gastó/ganó?)
            $table->foreignId('category_id')
                  ->nullable() // Permitimos transacciones "Sin Categoría"
                  ->constrained('categories')
                  ->onDelete('set null'); // Si borras la categoría, la transacción queda "Sin Categoría".

            // --- Campos Propios de la Transacción ---
            
            $table->string('description');
            $table->enum('type', ['ingreso', 'gasto']);
            // El monto siempre se guarda como positivo. El 'type' nos dice si suma o resta.
            $table->decimal('amount', 10, 2); 
            $table->date('date'); // La fecha de la ocurrencia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
