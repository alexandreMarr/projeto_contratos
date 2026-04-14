<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->string('tipo', 30)->default('card');
            $table->string('metric_key', 120);
            $table->json('configuracao')->nullable();
            $table->string('cor', 30)->nullable();
            $table->string('icone', 80)->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('visivel_para_todos')->default(false);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'ativo']);
            $table->index(['metric_key', 'ativo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
