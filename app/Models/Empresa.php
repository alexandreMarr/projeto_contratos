<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Empresa extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'empresas';

    protected $fillable = [
        'tipo_empresa',
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'inscricao_municipal',
        'email',
        'telefone',
        'celular',
        'contato_principal',
        'cargo_contato',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'cep',
        'banco',
        'agencia',
        'conta',
        'chave_pix',
        'observacoes',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function processosComoContratante()
    {
        return $this->hasMany(ProcessoContratacao::class, 'empresa_contratante_id');
    }

    public function processosComoContratada()
    {
        return $this->hasMany(ProcessoContratacao::class, 'empresa_contratada_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Empresa')
            ->logOnlyDirty()
            ->logOnly(['razao_social', 'nome_fantasia', 'cnpj', 'email', 'telefone', 'ativo'])
            ->dontSubmitEmptyLogs();
    }
}
