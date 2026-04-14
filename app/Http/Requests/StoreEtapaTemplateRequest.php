<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEtapaTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create etapas padrao');
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ordem' => 'required|integer|min:1|unique:etapa_templates,ordem',
            'setor_id' => 'nullable|exists:setores,id',
            'setor_responsavel' => 'nullable|string|max:255',
            'prazo_limite_dias' => 'nullable|integer|min:0',
            'cor_badge' => 'nullable|string|max:50',
            'obrigatoria' => 'nullable|boolean',
            'permite_anexo' => 'nullable|boolean',
            'exige_parecer' => 'nullable|boolean',
            'exige_aprovacao' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
        ];
    }
}
