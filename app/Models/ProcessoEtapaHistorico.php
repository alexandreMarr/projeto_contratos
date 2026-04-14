<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoEtapaHistorico extends Model
{
    use HasFactory;

    protected $table = 'processo_etapa_historicos';

    protected $fillable = [
        'processo_etapa_id',
        'user_id',
        'acao',
        'descricao',
        'status_anterior',
        'status_novo',
        'parecer',
        'observacoes',
        'anexo_path',
        'anexo_nome',
    ];

    public function etapa()
    {
        return $this->belongsTo(ProcessoEtapa::class, 'processo_etapa_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
