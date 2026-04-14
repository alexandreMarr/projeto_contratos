<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('contas_receber.parametros_globais', function (Blueprint $table) {

                $table->boolean('cobrar_ir_fora_do_estado_rondonia')->default(value: False)->after('razao_social');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('contas_receber.parametros_globais', function (Blueprint $table) {
            $table->dropColumn(['cobrar_ir_fora_do_estado_rondonia']);
        });
    }
};
