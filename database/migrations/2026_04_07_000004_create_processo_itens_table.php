<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_contratacao_id')->constrained('processos_contratacao')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('origem_anexo_id')->nullable()->constrained('processo_anexos')->nullOnDelete();
            $table->string('codigo_item', 100)->nullable();
            $table->string('codigo_pai', 100)->nullable();
            $table->unsignedInteger('nivel')->default(1);
            $table->string('grupo')->nullable();
            $table->string('subgrupo')->nullable();
            $table->text('descricao');
            $table->string('unidade', 20)->nullable();
            $table->decimal('quantidade', 14, 4)->nullable();
            $table->decimal('valor_unitario', 14, 4)->nullable();
            $table->decimal('valor_total', 14, 2)->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();

            $table->index(['processo_contratacao_id', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_itens');
    }
};
