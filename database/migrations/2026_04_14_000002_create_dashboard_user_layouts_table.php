<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_user_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->json('layout_json')->nullable();
            $table->json('filtros_json')->nullable();
            $table->boolean('padrao')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'padrao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_user_layouts');
    }
};
