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
        Schema::create('cash_counts', function (Blueprint $table) {
            $table->id();
            // Quién hizo el arqueo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Qué cuenta se auditó (puede ser nulo si es un conteo general)
            $table->foreignId('account_id')->nullable()->constrained()->onDelete('set null');
            
            // Los montos clave
            $table->decimal('total_counted', 15, 2);   // Lo que contaste físicamente
            $table->decimal('system_balance', 15, 2);  // Lo que decía el sistema
            $table->decimal('difference', 15, 2);      // La resta (sobrante/faltante)
            
            // Aquí guardaremos { coins: [...], bills: [...] }
            $table->json('details'); 
            
            $table->timestamps(); // created_at será la fecha del arqueo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_counts');
    }
};
