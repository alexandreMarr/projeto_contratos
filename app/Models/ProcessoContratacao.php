<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ProcessoContratacao extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'processos_contratacao';

    protected $fillable = [
        'numero_processo_interno',
        'titulo',
        'empresa_contratante_id',
        'empresa_contratada_id',
        'tipo_contratacao',
        'categoria',
        'origem',
        'objeto_resumido',
        'escopo_detalhado',
        'status',
        'prioridade',
        'valor_estimado',
        'valor_proposto',
        'valor_aprovado_final',
        'numero_contrato_assinado',
        'data_solicitacao',
        'data_recebimento_proposta',
        'validade_proposta',
        'prazo_execucao_inicio',
        'prazo_execucao_fim',
        'vigencia_inicio',
        'vigencia_fim',
        'prazo_pagamento_dias',
        'observacoes',
        'dados_extraidos_json',
        'criado_por',
        'atualizado_por',
    ];

    protected $casts = [
        'valor_estimado' => 'decimal:2',
        'valor_proposto' => 'decimal:2',
        'valor_aprovado_final' => 'decimal:2',
        'data_solicitacao' => 'date',
        'data_recebimento_proposta' => 'date',
        'validade_proposta' => 'date',
        'prazo_execucao_inicio' => 'date',
        'prazo_execucao_fim' => 'date',
        'vigencia_inicio' => 'date',
        'vigencia_fim' => 'date',
        'dados_extraidos_json' => 'array',
    ];

    public function contratante(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_contratante_id');
    }

    public function contratada(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_contratada_id');
    }

    public function etapas()
    {
        return $this->hasMany(ProcessoEtapa::class, 'processo_contratacao_id')->orderBy('ordem');
    }

    public function etapasContrato()
    {
        return $this->hasMany(ProcessoEtapa::class, 'processo_contratacao_id')
            ->where('origem_tipo', 'CONTRATO')
            ->whereNull('processo_aditivo_id')
            ->orderBy('ordem');
    }

    public function anexos()
    {
        return $this->hasMany(ProcessoAnexo::class, 'processo_contratacao_id')->latest();
    }

    public function itens()
    {
        return $this->hasMany(ProcessoItem::class, 'processo_contratacao_id')->orderBy('ordem');
    }

    public function aditivos()
    {
        return $this->hasMany(ProcessoAditivo::class, 'processo_contratacao_id')->latest();
    }

    public function historicos()
    {
        return $this->hasMany(ProcessoHistorico::class, 'processo_contratacao_id')->latest();
    }

    public function etapaAtual()
    {
        return $this->hasOne(ProcessoEtapa::class, 'processo_contratacao_id')
            ->where('origem_tipo', 'CONTRATO')
            ->whereNull('processo_aditivo_id')
            ->whereIn('status', ['PENDENTE', 'EM_ANDAMENTO', 'AGUARDANDO', 'LIBERADA'])
            ->orderBy('ordem');
    }

public function getEtapaAtualRealAttribute()
{
    $etapasContrato = $this->etapas
        ->where('origem_tipo', 'CONTRATO')
        ->whereNull('processo_aditivo_id')
        ->sortBy('ordem')
        ->values();

    $emAndamento = $etapasContrato->first(function ($etapa) {
        return $etapa->status_normalizado === 'EM_ANDAMENTO';
    });

    if ($emAndamento) {
        return $emAndamento;
    }

    $liberada = $etapasContrato->first(function ($etapa) {
        return $etapa->status_normalizado === 'LIBERADA';
    });

    if ($liberada) {
        return $liberada;
    }

    return null;
}

public function getPrevisaoConclusaoEtapaAtualAttribute()
{
    $etapa = $this->etapa_atual_real;

    if (!$etapa || !$etapa->data_limite) {
        return null;
    }

    return $etapa->data_limite;
}

    public function getPrevisaoConclusaoProcessoAttribute()
    {
        $base = now()->startOfDay();

        $etapasPendentes = $this->etapas
            ->where('origem_tipo', 'CONTRATO')
            ->whereNull('processo_aditivo_id')
            ->filter(function ($etapa) {
                return !in_array($etapa->status_normalizado, ['APROVADA', 'CANCELADA'], true);
            })
            ->sortBy('ordem');

        if ($etapasPendentes->isEmpty()) {
            return null;
        }

        foreach ($etapasPendentes as $etapa) {
            $dias = (int) ($etapa->prazo_limite_dias ?? 0);
            if ($dias > 0) {
                $base->addDays($dias);
            }
        }

        return $base;
    }

    public function getContratoEtapasConcluidasAttribute(): bool
    {
        $etapas = $this->relationLoaded('etapas')
            ? $this->etapas
                ->where('origem_tipo', 'CONTRATO')
                ->whereNull('processo_aditivo_id')
            : $this->etapasContrato()->get();

        if ($etapas->isEmpty()) {
            return false;
        }

        return $etapas->every(function ($etapa) {
            return strtoupper((string) $etapa->status) === 'APROVADA';
        });
    }

    public function getValorTotalAditivosAttribute(): float
    {
        $aditivos = $this->relationLoaded('aditivos') ? $this->aditivos : $this->aditivos()->get();
        return (float) $aditivos->sum('valor_aditivo');
    }

    public function getUltimoAditivoAttribute(): ?ProcessoAditivo
    {
        if ($this->relationLoaded('aditivos')) {
            return $this->aditivos->sortByDesc('created_at')->first();
        }

        return $this->aditivos()->latest()->first();
    }

    public function getValorContratualAtualAttribute(): float
    {
        $ultimoAditivo = $this->ultimo_aditivo;
        if ($ultimoAditivo) {
            return (float) ($ultimoAditivo->valor_novo ?? 0);
        }

        return (float) ($this->valor_aprovado_final ?: $this->valor_proposto ?: $this->valor_estimado ?: 0);
    }

    public function itensContrato()
    {
        return $this->hasMany(ProcessoItem::class, 'processo_contratacao_id')
            ->where('origem_tipo', 'CONTRATO')
            ->orderBy('ordem');
    }

    public function itensAditivo()
    {
        return $this->hasMany(ProcessoItem::class, 'processo_contratacao_id')
            ->where('origem_tipo', 'ADITIVO')
            ->orderBy('ordem');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Processo Contratação')
            ->logOnlyDirty()
            ->logOnly([
                'numero_processo_interno',
                'titulo',
                'status',
                'valor_proposto',
                'valor_aprovado_final',
                'numero_contrato_assinado',
            ])
            ->dontSubmitEmptyLogs();
    }
}
