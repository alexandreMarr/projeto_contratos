<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tipo_anexo' => 'required|string|max:50',
            'arquivo' => 'required|file|max:20480',
            'executar_extracao' => 'nullable|boolean',
        ];
    }
}
