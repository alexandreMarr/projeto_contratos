<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProcessoAditivo extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'processo_aditivos';

    protected $fillable = [
        'processo_contratacao_id',
        'anexo_id',
        'tipo',
        'titulo',
        'numero_documento',
        'descricao',
        'objeto',
        'escopo',
        'data_referencia',
        'boletim_medicao',
        'contrato_realizado_total',
        'valor_anterior',
        'valor_executado_medicao',
        'saldo_contrato_anterior',
        'valor_aditivo',
        'valor_novo',
        'diferenca_valor',
        'percentual_aditivo',
        'exige_aprovacao_conselho',
        'vigencia_anterior_fim',
        'vigencia_nova_fim',
        'observacoes',
    ];

    protected $casts = [
        'contrato_realizado_total' => 'boolean',
        'valor_anterior' => 'decimal:2',
        'valor_executado_medicao' => 'decimal:2',
        'saldo_contrato_anterior' => 'decimal:2',
        'valor_aditivo' => 'decimal:2',
        'valor_novo' => 'decimal:2',
        'diferenca_valor' => 'decimal:2',
        'percentual_aditivo' => 'decimal:6',
        'exige_aprovacao_conselho' => 'boolean',
        'vigencia_anterior_fim' => 'date',
        'vigencia_nova_fim' => 'date',
        'data_referencia' => 'date',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoContratacao::class, 'processo_contratacao_id');
    }

    public function anexo()
    {
        return $this->belongsTo(ProcessoAnexo::class, 'anexo_id');
    }

    public function etapas()
    {
        return $this->hasMany(ProcessoEtapa::class, 'processo_aditivo_id')
            ->where('origem_tipo', 'ADITIVO')
            ->orderBy('ordem');
    }

    public function getStatusLegalAttribute(): string
    {
        return $this->exige_aprovacao_conselho ? 'Extrapolou Limite' : 'Dentro do Limite';
    }

    public function getStatusLegalBadgeClassAttribute(): string
    {
        return $this->exige_aprovacao_conselho ? 'badge-danger' : 'badge-success';
    }

    public function getPercentualExibicaoAttribute(): string
    {
        if ($this->valor_anterior > 0 && $this->percentual_aditivo !== null) {
            return number_format((float) $this->percentual_aditivo, 4, ',', '.') . '%';
        }

        if ((float) $this->valor_aditivo > 0) {
            return '∞';
        }

        return '0,0000%';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Processo Aditivo')
            ->logOnlyDirty()
            ->logOnly([
                'tipo',
                'titulo',
                'numero_documento',
                'valor_anterior',
                'valor_executado_medicao',
                'saldo_contrato_anterior',
                'valor_aditivo',
                'valor_novo',
                'percentual_aditivo',
                'exige_aprovacao_conselho',
                'vigencia_anterior_fim',
                'vigencia_nova_fim',
                'data_referencia',
            ])
            ->dontSubmitEmptyLogs();
    }
}
