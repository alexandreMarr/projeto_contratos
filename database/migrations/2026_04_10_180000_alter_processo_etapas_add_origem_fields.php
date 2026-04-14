<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processo_etapas', function (Blueprint $table) {
            if (!Schema::hasColumn('processo_etapas', 'origem_tipo')) {
                $table->string('origem_tipo', 20)->default('CONTRATO')->after('processo_contratacao_id');
            }

            if (!Schema::hasColumn('processo_etapas', 'processo_aditivo_id')) {
                $table->foreignId('processo_aditivo_id')
                    ->nullable()
                    ->after('origem_tipo')
                    ->constrained('processo_aditivos')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('processo_etapas', function (Blueprint $table) {
            if (Schema::hasColumn('processo_etapas', 'processo_aditivo_id')) {
                $table->dropConstrainedForeignId('processo_aditivo_id');
            }
            if (Schema::hasColumn('processo_etapas', 'origem_tipo')) {
                $table->dropColumn('origem_tipo');
            }
        });
    }
};
