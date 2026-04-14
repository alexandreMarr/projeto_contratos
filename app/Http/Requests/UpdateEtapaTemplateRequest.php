<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtapaTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit etapas padrao');
    }

    public function rules(): array
    {
        $id = $this->route('etapaTemplate')->id ?? null;

        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ordem' => 'required|integer|min:1|unique:etapa_templates,ordem,' . $id,
            'setor_responsavel' => 'nullable|string|max:255',
            'setor_id' => 'nullable|exists:setores,id',
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
