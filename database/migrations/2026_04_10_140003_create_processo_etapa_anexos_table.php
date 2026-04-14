<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processo_etapa_anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_etapa_id')->constrained('processo_etapas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nome_original');
            $table->string('arquivo');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('tamanho')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processo_etapa_anexos');
    }
};
