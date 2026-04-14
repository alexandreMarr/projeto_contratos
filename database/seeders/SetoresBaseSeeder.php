<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setor;

class SetoresBaseSeeder extends Seeder
{
    public function run(): void
    {
        $setores = [
            'OBRA',
            'PLANEJAMENTO',
            'DIRETORIA',
            'SUPRIMENTOS',
            'JURIDICO',
            'PROPONENTE',
            'ASSINATURA',
        ];

        foreach ($setores as $nome) {
            Setor::updateOrCreate(
                ['nome' => $nome],
                ['descricao' => $nome, 'ativo' => true]
            );
        }
    }
}
