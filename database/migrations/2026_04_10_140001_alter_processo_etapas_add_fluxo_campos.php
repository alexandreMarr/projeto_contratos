<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processo_etapas', function (Blueprint $table) {
            $table->date('data_prazo_original')->nullable()->after('data_limite');
            $table->string('cor_badge', 20)->nullable()->after('observacoes');
            $table->boolean('permite_anexo')->default(false)->after('cor_badge');
            $table->foreignId('reprovado_por_user_id')->nullable()->after('aprovado_por_user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('reprovado_em')->nullable()->after('reprovado_por_user_id');
            $table->text('motivo_reprovacao')->nullable()->after('reprovado_em');
            $table->foreignId('cancelado_por_user_id')->nullable()->after('motivo_reprovacao')->constrained('users')->nullOnDelete();
            $table->timestamp('cancelado_em')->nullable()->after('cancelado_por_user_id');
            $table->text('motivo_cancelamento')->nullable()->after('cancelado_em');
        });
    }

    public function down(): void
    {
        Schema::table('processo_etapas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reprovado_por_user_id');
            $table->dropConstrainedForeignId('cancelado_por_user_id');
            $table->dropColumn(['data_prazo_original', 'cor_badge', 'permite_anexo', 'reprovado_em', 'motivo_reprovacao', 'cancelado_em', 'motivo_cancelamento']);
        });
    }
};
