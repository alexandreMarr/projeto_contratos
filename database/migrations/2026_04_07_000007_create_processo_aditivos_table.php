<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_aditivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_contratacao_id')->constrained('processos_contratacao')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('anexo_id')->nullable()->constrained('processo_anexos')->nullOnDelete();
            $table->string('tipo', 30);
            $table->string('numero_documento')->nullable();
            $table->text('descricao');
            $table->decimal('valor_anterior', 14, 2)->nullable();
            $table->decimal('valor_novo', 14, 2)->nullable();
            $table->decimal('diferenca_valor', 14, 2)->nullable();
            $table->date('vigencia_anterior_fim')->nullable();
            $table->date('vigencia_nova_fim')->nullable();
            $table->date('data_referencia')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['processo_contratacao_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_aditivos');
    }
};
