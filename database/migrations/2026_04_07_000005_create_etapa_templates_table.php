<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapa_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->unsignedInteger('ordem');
            $table->string('setor_responsavel', 150);
            $table->unsignedInteger('prazo_limite_dias')->default(1);
            $table->boolean('obrigatoria')->default(true);
            $table->boolean('permite_anexo')->default(true);
            $table->boolean('exige_parecer')->default(false);
            $table->boolean('exige_aprovacao')->default(false);
            $table->string('cor_badge', 20)->default('secondary');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique('ordem');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapa_templates');
    }
};
