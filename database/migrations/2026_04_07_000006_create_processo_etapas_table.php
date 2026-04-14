<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_contratacao_id')->constrained('processos_contratacao')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('etapa_template_id')->nullable()->constrained('etapa_templates')->nullOnDelete();
            $table->string('nome_etapa');
            $table->unsignedInteger('ordem');
            $table->string('setor_responsavel', 150);
            $table->unsignedInteger('prazo_limite_dias')->default(1);
            $table->date('data_inicio')->nullable();
            $table->date('data_limite')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->string('status', 50)->default('PENDENTE');
            $table->longText('parecer')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('responsavel_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('aprovado_por_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_contratacao_id', 'ordem']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_etapas');
    }
};
