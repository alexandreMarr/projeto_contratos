<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_saved_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->json('filtros_json');
            $table->boolean('publico')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_saved_filters');
    }
};
