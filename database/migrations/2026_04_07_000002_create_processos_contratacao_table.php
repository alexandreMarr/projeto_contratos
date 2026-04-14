<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos_contratacao', function (Blueprint $table) {
            $table->id();
            $table->string('numero_processo_interno')->unique();
            $table->string('titulo');
            $table->foreignId('empresa_contratante_id')->constrained('empresas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('empresa_contratada_id')->constrained('empresas')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('tipo_contratacao', 100)->nullable();
            $table->string('categoria', 100)->nullable();
            $table->string('origem', 100)->nullable();
            $table->text('objeto_resumido');
            $table->longText('escopo_detalhado')->nullable();
            $table->string('status', 50)->default('RASCUNHO');
            $table->string('prioridade', 20)->default('NORMAL');
            $table->decimal('valor_estimado', 14, 2)->nullable();
            $table->decimal('valor_proposto', 14, 2)->nullable();
            $table->decimal('valor_aprovado_final', 14, 2)->nullable();
            $table->string('numero_contrato_assinado')->nullable();
            $table->date('data_solicitacao')->nullable();
            $table->date('data_recebimento_proposta')->nullable();
            $table->date('validade_proposta')->nullable();
            $table->date('prazo_execucao_inicio')->nullable();
            $table->date('prazo_execucao_fim')->nullable();
            $table->date('vigencia_inicio')->nullable();
            $table->date('vigencia_fim')->nullable();
            $table->unsignedInteger('prazo_pagamento_dias')->nullable();
            $table->json('dados_extraidos_json')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('atualizado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('tipo_contratacao');
            $table->index('categoria');
            $table->index('data_solicitacao');
            $table->index('vigencia_fim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos_contratacao');
    }
};
