<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProcessoResumoGeralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit processos contratacao');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|max:50',
            'numero_contrato_assinado' => 'nullable|string|max:100',
        ];
    }
}
