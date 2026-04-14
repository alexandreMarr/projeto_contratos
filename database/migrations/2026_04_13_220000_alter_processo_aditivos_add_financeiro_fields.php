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
                $table->string('titulo')->nullable()->after('numero_documento');
            }
            if (!Schema::hasColumn('processo_aditivos', 'objeto')) {
                $table->text('objeto')->nullable()->after('titulo');
            }
            if (!Schema::hasColumn('processo_aditivos', 'escopo')) {
                $table->longText('escopo')->nullable()->after('objeto');
            }
            if (!Schema::hasColumn('processo_aditivos', 'contrato_realizado_integral')) {
                $table->boolean('contrato_realizado_integral')->default(true)->after('escopo');
            }
            if (!Schema::hasColumn('processo_aditivos', 'valor_executado_medicao')) {
                $table->decimal('valor_executado_medicao', 14, 2)->nullable()->after('valor_anterior');
            }
            if (!Schema::hasColumn('processo_aditivos', 'saldo_contrato_anterior')) {
                $table->decimal('saldo_contrato_anterior', 14, 2)->nullable()->after('valor_executado_medicao');
            }
            if (!Schema::hasColumn('processo_aditivos', 'percentual_aditivo')) {
                $table->decimal('percentual_aditivo', 8, 2)->nullable()->after('diferenca_valor');
            }
            if (!Schema::hasColumn('processo_aditivos', 'exige_aprovacao_conselho')) {
                $table->boolean('exige_aprovacao_conselho')->default(false)->after('percentual_aditivo');
            }
            if (!Schema::hasColumn('processo_aditivos', 'boletim_medicao_referencia')) {
                $table->string('boletim_medicao_referencia')->nullable()->after('data_referencia');
            }
        });
    }

    public function down(): void
    {
        Schema::table('processo_aditivos', function (Blueprint $table) {
            foreach ([
                'titulo',
                'objeto',
                'escopo',
                'contrato_realizado_integral',
                'valor_executado_medicao',
                'saldo_contrato_anterior',
                'percentual_aditivo',
                'exige_aprovacao_conselho',
                'boletim_medicao_referencia',
            ] as $column) {
                if (Schema::hasColumn('processo_aditivos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
