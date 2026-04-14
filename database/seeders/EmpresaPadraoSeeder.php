<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaPadraoSeeder extends Seeder
{
    public function run(): void
    {
        Empresa::updateOrCreate(
            ['cnpj' => '60.437.929/0001-04'],
            [
                'tipo_empresa' => 'CONTRATANTE',
                'razao_social' => 'CONCESSIONÁRIA DE RODOVIA NOVA 364 S.A',
                'nome_fantasia' => 'NOVA 364',
                'endereco' => 'Avenida Pinheiro Machado',
                'numero' => '2165',
                'bairro' => 'São Cristóvão',
                'cidade' => 'Porto Velho',
                'uf' => 'RO',
                'cep' => '76801-501',
                'observacoes' => 'Empresa contratante padrão do módulo.',
                'ativo' => true,
            ]
        );
    }
}
