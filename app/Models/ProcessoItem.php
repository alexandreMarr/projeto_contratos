<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessoItem extends Model
{
    use HasFactory;

    protected $table = 'processo_itens';

    protected $fillable = [
        'processo_contratacao_id',
        'anexo_id',
        'origem_tipo',
        'aditivo_id',
        'codigo_item',
        'codigo_pai',
        'item_referencia',
        'nivel',
        'tipo_linha',
        'grupo',
        'subgrupo',
        'descricao',
        'unidade',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoContratacao::class, 'processo_contratacao_id');
    }

    public function anexo()
    {
        return $this->belongsTo(ProcessoAnexo::class, 'anexo_id');
    }

    public function aditivo()
    {
        return $this->belongsTo(ProcessoAditivo::class, 'aditivo_id');
    }
}
