<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ProcessoHistorico extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'processo_historicos';

    protected $fillable = [
        'processo_contratacao_id',
        'tipo_evento',
        'descricao',
        'dados_json',
        'user_id',
    ];

    protected $casts = [
        'dados_json' => 'array',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoContratacao::class, 'processo_contratacao_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Processo Histórico')
            ->logOnlyDirty()
            ->logOnly(['tipo_evento', 'descricao'])
            ->dontSubmitEmptyLogs();
    }
}
