<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_etapa_historicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_etapa_id')->constrained('processo_etapas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('acao', 80);
            $table->text('descricao')->nullable();
            $table->string('status_anterior', 50)->nullable();
            $table->string('status_novo', 50)->nullable();
            $table->longText('parecer')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('anexo_path')->nullable();
            $table->string('anexo_nome')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_etapa_historicos');
    }
};
