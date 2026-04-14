<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('create empresas');
    }

    public function rules(): array
    {
        return [
            'tipo_empresa' => 'required|string|max:20',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:empresas,cnpj',
            'inscricao_estadual' => 'nullable|string|max:50',
            'inscricao_municipal' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'contato_principal' => 'nullable|string|max:255',
            'cargo_contato' => 'nullable|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:30',
            'conta' => 'nullable|string|max:30',
            'chave_pix' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'nullable|boolean',
        ];
    }
}
