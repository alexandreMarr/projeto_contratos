<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessoEtapaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'acao' => 'nullable|in:salvar,aprovar,reprovar,cancelar',
            '_acao' => 'nullable|in:salvar,aprovar,reprovar,cancelar',
            'data_limite' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'motivo_reprovacao' => 'nullable|string',
            'motivo_cancelamento' => 'nullable|string',
            'anexo' => 'nullable|file|max:15360',
        ];
    }
}
