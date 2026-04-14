<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSetorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('edit setores');
    }

    public function rules(): array
    {
        $id = $this->route('setor')->id ?? null;

        return [
            'nome' => 'required|string|max:255|unique:setores,nome,' . $id,
            'descricao' => 'nullable|string',
            'ativo' => 'nullable|boolean',
            'usuarios_permissoes' => 'nullable|array',
            'usuarios_permissoes.*.user_id' => 'required|exists:users,id',
            'usuarios_permissoes.*.pode_visualizar' => 'nullable|boolean',
            'usuarios_permissoes.*.pode_editar' => 'nullable|boolean',
            'usuarios_permissoes.*.pode_aprovar' => 'nullable|boolean',
            'usuarios_permissoes.*.pode_reprovar' => 'nullable|boolean',
            'usuarios_permissoes.*.ativo' => 'nullable|boolean',
        ];
    }
}
