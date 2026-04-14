<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessoAditivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit processos contratacao');
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'data_referencia' => 'nullable|date',
            'boletim_medicao' => 'nullable|string|max:100',
            'objeto' => 'required|string',
            'escopo' => 'required|string',
            'contrato_realizado_total' => 'required|boolean',
            'valor_executado_medicao' => 'nullable|string',
            'valor_aditivo' => 'required|string',
            'vigencia_anterior_fim' => 'nullable|date',
            'vigencia_nova_fim' => 'nullable|date|after_or_equal:vigencia_anterior_fim',
            'observacoes' => 'nullable|string',
            'anexo_id' => 'nullable|exists:processo_anexos,id',
        ];
    }
}
