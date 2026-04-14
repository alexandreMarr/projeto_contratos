<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_empresa', 20)->default('CONTRATADA');
            $table->string('razao_social');
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj', 18)->unique();
            $table->string('inscricao_estadual', 30)->nullable();
            $table->string('inscricao_municipal', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('telefone', 30)->nullable();
            $table->string('celular', 30)->nullable();
            $table->string('contato_principal')->nullable();
            $table->string('cargo_contato')->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 30)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('banco')->nullable();
            $table->string('agencia')->nullable();
            $table->string('conta')->nullable();
            $table->string('chave_pix')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('tipo_empresa');
            $table->index('ativo');
            $table->index('razao_social');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
