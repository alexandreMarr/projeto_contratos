<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processo_aditivos', function (Blueprint $table) {
            if (!Schema::hasColumn('processo_aditivos', 'titulo')) {
                $table->string('titulo')->nullable()->after('tipo');
            }
            if (!Schema::hasColumn('processo_aditivos', 'objeto')) {
                $table->longText('objeto')->nullable()->after('descricao');
            }
            if (!Schema::hasColumn('processo_aditivos', 'escopo')) {
                $table->longText('escopo')->nullable()->after('objeto');
            }
            if (!Schema::hasColumn('processo_aditivos', 'boletim_medicao')) {
                $table->string('boletim_medicao')->nullable()->after('data_referencia');
            }
            if (!Schema::hasColumn('processo_aditivos', 'contrato_realizado_total')) {
                $table->boolean('contrato_realizado_total')->default(false)->after('boletim_medicao');
            }
            if (!Schema::hasColumn('processo_aditivos', 'valor_executado_medicao')) {
                $table->decimal('valor_executado_medicao', 14, 2)->nullable()->after('valor_anterior');
            }
            if (!Schema::hasColumn('processo_aditivos', 'saldo_contrato_anterior')) {
                $table->decimal('saldo_contrato_anterior', 14, 2)->nullable()->after('valor_executado_medicao');
            }
            if (!Schema::hasColumn('processo_aditivos', 'valor_aditivo')) {
                $table->decimal('valor_aditivo', 14, 2)->nullable()->after('saldo_contrato_anterior');
            }
            if (!Schema::hasColumn('processo_aditivos', 'percentual_aditivo')) {
                $table->decimal('percentual_aditivo', 14, 6)->nullable()->after('diferenca_valor');
            }
            if (!Schema::hasColumn('processo_aditivos', 'exige_aprovacao_conselho')) {
                $table->boolean('exige_aprovacao_conselho')->default(false)->after('percentual_aditivo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('processo_aditivos', function (Blueprint $table) {
            foreach (['titulo','objeto','escopo','boletim_medicao','contrato_realizado_total','valor_executado_medicao','saldo_contrato_anterior','valor_aditivo','percentual_aditivo','exige_aprovacao_conselho'] as $col) {
                if (Schema::hasColumn('processo_aditivos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
