<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_contratacao_id')->constrained('processos_contratacao')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('tipo_anexo', 50);
            $table->string('nome_original');
            $table->string('caminho_arquivo');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->nullable();
            $table->string('hash_arquivo', 64)->nullable();
            $table->unsignedInteger('versao')->default(1);
            $table->boolean('extraido_com_sucesso')->default(false);
            $table->json('dados_extraidos_json')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['processo_contratacao_id', 'tipo_anexo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_anexos');
    }
};
