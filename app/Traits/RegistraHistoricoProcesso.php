<?php

namespace App\Traits;

use App\Models\ProcessoHistorico;
use Illuminate\Support\Facades\Auth;

trait RegistraHistoricoProcesso
{
    protected function registrarHistoricoProcesso(
        int $processoId,
        string $tipoEvento,
        string $descricao,
        array $dados = []
    ): void {
        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoId,
            'tipo_evento' => $tipoEvento,
            'descricao' => $descricao,
            'dados_json' => $dados,
            'user_id' => Auth::id(),
        ]);
    }
}
