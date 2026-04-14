<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoEtapaAnexo extends Model
{
    use HasFactory;

    protected $table = 'processo_etapa_anexos';

    protected $fillable = [
        'processo_etapa_id',
        'user_id',
        'nome_original',
        'arquivo',
        'mime_type',
        'tamanho',
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
