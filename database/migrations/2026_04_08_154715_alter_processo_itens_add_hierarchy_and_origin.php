<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processo_itens', function (Blueprint $table) {
            if (!Schema::hasColumn('processo_itens', 'anexo_id')) {
                $table->unsignedBigInteger('anexo_id')->nullable()->after('processo_contratacao_id');
            }

            if (!Schema::hasColumn('processo_itens', 'origem_tipo')) {
                $table->string('origem_tipo', 20)->default('CONTRATO')->after('anexo_id');
            }

            if (!Schema::hasColumn('processo_itens', 'aditivo_id')) {
                $table->unsignedBigInteger('aditivo_id')->nullable()->after('origem_tipo');
            }

            if (!Schema::hasColumn('processo_itens', 'tipo_linha')) {
                $table->string('tipo_linha', 20)->default('ITEM')->after('nivel');
            }

            if (!Schema::hasColumn('processo_itens', 'item_referencia')) {
                $table->string('item_referencia', 100)->nullable()->after('codigo_item');
            }

            if (!Schema::hasColumn('processo_itens', 'ativo')) {
                $table->boolean('ativo')->default(true)->after('valor_total');
            }
        });
    }

    public function down(): void
    {
        Schema::table('processo_itens', function (Blueprint $table) {
            foreach (['anexo_id', 'origem_tipo', 'aditivo_id', 'tipo_linha', 'item_referencia', 'ativo'] as $column) {
                if (Schema::hasColumn('processo_itens', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
