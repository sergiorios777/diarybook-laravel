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
        Schema::create('categorization_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('keyword');           // palabra o frase exacta (case insensitive)
            $table->string('regex')->nullable(); // expresión regular opcional
            $table->enum('type', ['ingreso', 'gasto']); // para forzar tipo
            $table->integer('priority')->default(100); // menor número = mayor prioridad
            $table->integer('usages')->default(0);     // cuántas veces se aplicó
            $table->timestamps();

            // Índices para rendimiento
            $table->index(['keyword']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorization_rules');
    }
};
