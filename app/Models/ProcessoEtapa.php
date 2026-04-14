<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProcessoEtapa extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'processo_etapas';

    protected $fillable = [
        'processo_contratacao_id',
        'processo_aditivo_id',
        'origem_tipo',
        'etapa_template_id',
        'nome_etapa',
        'ordem',
        'setor_id',
        'setor_responsavel',
        'prazo_limite_dias',
        'data_inicio',
        'data_limite',
        'data_prazo_original',
        'data_conclusao',
        'status',
        'parecer',
        'observacoes',
        'responsavel_user_id',
        'aprovado_por_user_id',
        'reprovado_por_user_id',
        'reprovado_em',
        'motivo_reprovacao',
        'cancelado_por_user_id',
        'cancelado_em',
        'motivo_cancelamento',
        'cor_badge',
        'permite_anexo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_limite' => 'date',
        'data_prazo_original' => 'date',
        'data_conclusao' => 'datetime',
        'reprovado_em' => 'datetime',
        'cancelado_em' => 'datetime',
        'permite_anexo' => 'boolean',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoContratacao::class, 'processo_contratacao_id');
    }

    public function aditivo()
    {
        return $this->belongsTo(ProcessoAditivo::class, 'processo_aditivo_id');
    }

    public function template()
    {
        return $this->belongsTo(EtapaTemplate::class, 'etapa_template_id');
    }

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_user_id');
    }

    public function aprovadoPor()
    {
        return $this->belongsTo(User::class, 'aprovado_por_user_id');
    }

    public function reprovadoPor()
    {
        return $this->belongsTo(User::class, 'reprovado_por_user_id');
    }

    public function canceladoPor()
    {
        return $this->belongsTo(User::class, 'cancelado_por_user_id');
    }

    public function historicos()
    {
        return $this->hasMany(ProcessoEtapaHistorico::class, 'processo_etapa_id')->latest();
    }

    public function anexos()
    {
        return $this->hasMany(ProcessoEtapaAnexo::class, 'processo_etapa_id')->latest();
    }

    public function getStatusNormalizadoAttribute(): string
    {
        return strtoupper(trim((string) $this->status));
    }

    public function getCorCardAttribute(): string
    {
        $cor = $this->cor_badge;

        if (empty($cor) && $this->relationLoaded('template') && $this->template) {
            $cor = $this->template->cor_badge;
        }

        if (empty($cor) && $this->template) {
            $cor = $this->template->cor_badge;
        }

        if (empty($cor) || !$this->isHexColor($cor)) {
            return '#6c757d';
        }

        return $cor;
    }

    public function getEtapaAnteriorAttribute(): ?self
    {
        $query = self::query()
            ->where('processo_contratacao_id', $this->processo_contratacao_id)
            ->where(function ($q) {
                $q->where('origem_tipo', $this->origem_tipo);
                if ($this->origem_tipo === null) {
                    $q->orWhereNull('origem_tipo');
                }
            });

        if (!empty($this->processo_aditivo_id)) {
            $query->where('processo_aditivo_id', $this->processo_aditivo_id);
        } else {
            $query->whereNull('processo_aditivo_id');
        }

        return $query->where('ordem', '<', $this->ordem)->orderByDesc('ordem')->first();
    }

    public function getSequenciaLiberadaAttribute(): bool
    {
        $anterior = $this->etapa_anterior;

        if (!$anterior) {
            return true;
        }

        return strtoupper((string) $anterior->status) === 'APROVADA';
    }

    public function getEstaBloqueadaAttribute(): bool
    {
        $status = $this->status_normalizado;

        if (in_array($status, ['APROVADA', 'REPROVADA', 'CANCELADA'], true)) {
            return false;
        }

        if (!$this->sequencia_liberada) {
            return true;
        }

        return $status === 'BLOQUEADA';
    }

    public function getPodeSerTrabalhadaAttribute(): bool
    {
        if ($this->esta_bloqueada) {
            return false;
        }

        return in_array($this->status_normalizado, ['LIBERADA', 'EM_ANDAMENTO'], true);
    }

    public function getStatusExibicaoAttribute(): string
    {
        if ($this->esta_bloqueada) {
            return 'BLOQUEADA';
        }

        return $this->status_normalizado;
    }

    public function getEstaAtrasadaAttribute(): bool
    {
        if (!$this->data_limite || $this->esta_bloqueada) {
            return false;
        }

        return in_array($this->status_normalizado, ['LIBERADA', 'EM_ANDAMENTO', 'PENDENTE'], true)
            && $this->data_limite->isPast();
    }

    public function permissaoUsuario(?User $user): ?array
    {
        if (!$user || !$this->setor_id) {
            return null;
        }

        $setor = $this->relationLoaded('setor') ? $this->setor : $this->setor()->with('usuarios')->first();

        if (!$setor) {
            return null;
        }

        $usuario = $setor->usuarios->firstWhere('id', $user->id);

        if (!$usuario || !(bool) ($usuario->pivot->ativo ?? false)) {
            return null;
        }

        return [
            'visualizar' => (bool) ($usuario->pivot->pode_visualizar ?? false),
            'editar' => (bool) ($usuario->pivot->pode_editar ?? false),
            'aprovar' => (bool) ($usuario->pivot->pode_aprovar ?? false),
            'reprovar' => (bool) ($usuario->pivot->pode_reprovar ?? false),
        ];
    }

    public function userPodeVisualizar(?User $user): bool
    {
        if ($user && $user->can('manage etapas processos contratacao')) {
            return true;
        }

        return (bool) ($this->permissaoUsuario($user)['visualizar'] ?? false);
    }

    public function userPodeEditar(?User $user): bool
    {
        if (!$this->pode_ser_trabalhada) {
            return false;
        }

        if ($user && $user->can('manage etapas processos contratacao')) {
            return true;
        }

        return (bool) ($this->permissaoUsuario($user)['editar'] ?? false);
    }

    public function userPodeAprovar(?User $user): bool
    {
        if (!$this->pode_ser_trabalhada) {
            return false;
        }

        if ($user && $user->can('manage etapas processos contratacao')) {
            return true;
        }

        return (bool) ($this->permissaoUsuario($user)['aprovar'] ?? false);
    }

    public function userPodeReprovar(?User $user): bool
    {
        if (!$this->pode_ser_trabalhada) {
            return false;
        }

        if ($user && $user->can('manage etapas processos contratacao')) {
            return true;
        }

        return (bool) ($this->permissaoUsuario($user)['reprovar'] ?? false);
    }

    protected function isHexColor(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return (bool) preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $value);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Processo Etapa')
            ->logOnlyDirty()
            ->logOnly([
                'nome_etapa',
                'status',
                'data_limite',
                'data_conclusao',
                'setor_id',
                'setor_responsavel',
                'origem_tipo',
                'processo_aditivo_id',
            ])
            ->dontSubmitEmptyLogs();
    }
}
