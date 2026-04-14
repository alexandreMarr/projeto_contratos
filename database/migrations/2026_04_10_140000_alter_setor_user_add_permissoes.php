<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setor_user', function (Blueprint $table) {
            $table->boolean('pode_visualizar')->default(true)->after('user_id');
            $table->boolean('pode_editar')->default(false)->after('pode_visualizar');
            $table->boolean('pode_aprovar')->default(false)->after('pode_editar');
            $table->boolean('pode_reprovar')->default(false)->after('pode_aprovar');
            $table->boolean('ativo')->default(true)->after('pode_reprovar');
        });
    }

    public function down(): void
    {
        Schema::table('setor_user', function (Blueprint $table) {
            $table->dropColumn(['pode_visualizar', 'pode_editar', 'pode_aprovar', 'pode_reprovar', 'ativo']);
        });
    }
};
