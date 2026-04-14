<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessoAnexoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit processos contratacao');
    }

    public function rules(): array
    {
        return [
            'tipo_anexo' => 'required|string|max:50',
            'arquivo' => 'required|file|max:20480',
            'observacoes' => 'nullable|string',
            'executar_extracao' => 'nullable|boolean',
            'importar_itens' => 'nullable|boolean',
        ];
    }
}
