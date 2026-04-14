<?php

namespace App\Services;

use App\Models\ProcessoAnexo;
use App\Models\ProcessoContratacao;

class ProcessoItemImportService
{
    public function importarItensExtraidos(ProcessoContratacao $processo, ProcessoAnexo $anexo): int
    {
        $dados = $anexo->dados_extraidos_json['dados']['itens'] ?? [];

        $count = 0;
        $ordem = ((int) $processo->itens()->max('ordem')) + 1;

        foreach ($dados as $item) {
            $processo->itens()->create([
                'codigo_item' => null,
                'codigo_pai' => null,
                'nivel' => 1,
                'grupo' => 'Extraído automaticamente',
                'subgrupo' => null,
                'descricao' => $item['descricao'] ?? 'Item sem descrição',
                'unidade' => $item['unidade'] ?? null,
                'quantidade' => $item['quantidade'] ?? null,
                'valor_unitario' => $item['valor_unitario'] ?? null,
                'valor_total' => $item['valor_total'] ?? null,
                'origem_anexo_id' => $anexo->id,
                'ordem' => $ordem,
            ]);

            $count++;
            $ordem++;
        }

        return $count;
    }
}
