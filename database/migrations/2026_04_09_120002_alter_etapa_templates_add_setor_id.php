<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etapa_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('etapa_templates', 'setor_id')) {
                $table->foreignId('setor_id')->nullable()->after('ordem')->constrained('setores')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('etapa_templates', function (Blueprint $table) {
            if (Schema::hasColumn('etapa_templates', 'setor_id')) {
                $table->dropConstrainedForeignId('setor_id');
            }
        });
    }
};
