<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProcessoAnexo extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'processo_anexos';

    protected $fillable = [
        'processo_contratacao_id',
        'tipo_anexo',
        'nome_original',
        'caminho_arquivo',
        'mime_type',
        'tamanho_bytes',
        'hash_arquivo',
        'versao',
        'extraido_com_sucesso',
        'observacoes',
        'dados_extraidos_json',
    ];

    protected $casts = [
        'extraido_com_sucesso' => 'boolean',
        'dados_extraidos_json' => 'array',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoContratacao::class, 'processo_contratacao_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Processo Anexo')
            ->logOnlyDirty()
            ->logOnly(['tipo_anexo', 'nome_original', 'extraido_com_sucesso'])
            ->dontSubmitEmptyLogs();
    }
}
