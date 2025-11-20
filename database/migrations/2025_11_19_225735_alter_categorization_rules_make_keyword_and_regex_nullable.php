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
        Schema::table('categorization_rules', function (Blueprint $table) {
            $table->string('keyword')->nullable()->change();
            $table->string('regex')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('categorization_rules', function (Blueprint $table) {
            $table->string('keyword')->nullable(false)->change();
            $table->string('regex')->nullable(false)->change();
        });
    }
};
