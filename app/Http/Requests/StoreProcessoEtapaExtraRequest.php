<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessoEtapaExtraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('manage etapas processos contratacao');
    }

    public function rules(): array
    {
        return [
            'processo_contratacao_id' => 'required|exists:processos_contratacao,id',
            'processo_aditivo_id' => 'nullable|exists:processo_aditivos,id',
            'origem_tipo' => 'nullable|in:CONTRATO,ADITIVO',
            'nome_etapa' => 'required|string|max:255',
            'ordem' => 'required|integer|min:1',
            'setor_id' => 'nullable|exists:setores,id',
            'setor_responsavel' => 'nullable|string|max:255',
            'prazo_limite_dias' => 'nullable|integer|min:0',
            'status' => 'required|string|max:50',
            'observacoes' => 'nullable|string',
        ];
    }
}
