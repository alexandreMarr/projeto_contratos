<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class EtapaTemplate extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'etapa_templates';

    protected $fillable = [
        'nome',
        'descricao',
        'ordem',
        'setor_id',
        'setor_responsavel',
        'prazo_limite_dias',
        'obrigatoria',
        'permite_anexo',
        'exige_parecer',
        'exige_aprovacao',
        'cor_badge',
        'ativo',
    ];

    protected $casts = [
        'obrigatoria' => 'boolean',
        'permite_anexo' => 'boolean',
        'exige_parecer' => 'boolean',
        'exige_aprovacao' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function setor()
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function etapas()
    {
        return $this->hasMany(ProcessoEtapa::class, 'etapa_template_id');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->setor && empty($model->setor_responsavel)) {
                $model->setor_responsavel = $model->setor->nome;
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Etapa Template')
            ->logOnlyDirty()
            ->logOnly(['nome', 'ordem', 'setor_id', 'setor_responsavel', 'prazo_limite_dias', 'ativo'])
            ->dontSubmitEmptyLogs();
    }
}
