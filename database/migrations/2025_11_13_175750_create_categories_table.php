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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['ingreso', 'gasto']);
            
            // La magia para la jerarquía:
            // Esta columna (parent_id) apunta a la columna (id) de esta misma tabla.
            $table->foreignId('parent_id')
                  ->nullable() // Permite que sea null (para categorías padre)
                  ->constrained('categories') // La clave foránea apunta a la tabla 'categories'
                  ->onDelete('set null'); // Si se borra un padre, sus hijos se vuelven 'null' (categorías raíz)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
