<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EtapaTemplate;

class EtapaTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $etapas = [
            ['ordem' => 1, 'nome' => 'Validação dos serviços da proponente', 'descricao' => 'Validação dos serviços da proponente (Obra)', 'setor_responsavel' => 'Obra', 'prazo_limite_dias' => 7, 'exige_parecer' => true],
            ['ordem' => 2, 'nome' => 'Validação da documentação da proponente', 'descricao' => 'Validação da documentação da proponente (Planejamento)', 'setor_responsavel' => 'Planejamento', 'prazo_limite_dias' => 1, 'exige_parecer' => true],
            ['ordem' => 3, 'nome' => 'Envio e Validação da Proposta / Justificativa', 'descricao' => 'Envio e validação da proposta/justificativa (Diretores)', 'setor_responsavel' => 'Diretores', 'prazo_limite_dias' => 3, 'exige_parecer' => true, 'exige_aprovacao' => true],
            ['ordem' => 4, 'nome' => 'Análise da proposta orçamentária', 'descricao' => 'Análise da proposta orçamentária (Suprimentos)', 'setor_responsavel' => 'Suprimentos', 'prazo_limite_dias' => 3, 'exige_parecer' => true],
            ['ordem' => 5, 'nome' => 'Elaboração da minuta', 'descricao' => 'Elaboração da minuta (Jurídico)', 'setor_responsavel' => 'Jurídico', 'prazo_limite_dias' => 5, 'exige_parecer' => true],
            ['ordem' => 6, 'nome' => 'Validação da minuta pelo proponente', 'descricao' => 'Validação da minuta pelo proponente', 'setor_responsavel' => 'Proponente', 'prazo_limite_dias' => 1],
            ['ordem' => 7, 'nome' => 'Assinatura: Nova364/Proponente', 'descricao' => 'Assinatura da Nova364 e proponente', 'setor_responsavel' => 'Assinatura', 'prazo_limite_dias' => 1, 'exige_aprovacao' => true],
        ];

        foreach ($etapas as $etapa) {
            EtapaTemplate::updateOrCreate(
                ['ordem' => $etapa['ordem']],
                array_merge([
                    'obrigatoria' => true,
                    'permite_anexo' => true,
                    'exige_parecer' => false,
                    'exige_aprovacao' => false,
                    'cor_badge' => 'secondary',
                    'ativo' => true,
                ], $etapa)
            );
        }
    }
}
