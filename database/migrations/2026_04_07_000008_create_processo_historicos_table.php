<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_contratacao_id')->constrained('processos_contratacao')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('tipo_evento', 100);
            $table->text('descricao');
            $table->json('dados_json')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_contratacao_id', 'tipo_evento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_historicos');
    }
};
