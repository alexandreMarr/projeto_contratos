<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessoContratacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create processos contratacao');
    }

    public function rules(): array
    {
        return [
            'numero_processo_interno' => 'nullable|string|max:100',
            'titulo' => 'required|string|max:255',
            'empresa_contratante_id' => 'required|exists:empresas,id',
            'empresa_contratada_id' => 'required|exists:empresas,id',
            'tipo_contratacao' => 'nullable|string|max:100',
            'categoria' => 'nullable|string|max:100',
            'origem' => 'nullable|string|max:100',
            'objeto_resumido' => 'required|string|max:255',
            'escopo_detalhado' => 'nullable|string',
            'status' => 'required|string|max:50',
            'prioridade' => 'nullable|string|max:50',
            'valor_estimado' => 'nullable|numeric|min:0',
            'valor_proposto' => 'nullable|numeric|min:0',
            'valor_aprovado_final' => 'nullable|numeric|min:0',
            'numero_contrato_assinado' => 'nullable|string|max:100',
            'data_solicitacao' => 'nullable|date',
            'data_recebimento_proposta' => 'nullable|date',
            'validade_proposta' => 'nullable|date',
            'prazo_execucao_inicio' => 'nullable|date',
            'prazo_execucao_fim' => 'nullable|date',
            'vigencia_inicio' => 'nullable|date',
            'vigencia_fim' => 'nullable|date',
            'prazo_pagamento_dias' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
        ];
    }
}
