<?php

namespace App\Services;

use App\Models\ProcessoEtapa;
use App\Models\ProcessoEtapaAnexo;
use App\Models\ProcessoEtapaHistorico;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessoEtapaFluxoService
{
    public function atualizar(ProcessoEtapa $etapa, array $data, ?int $userId = null): ProcessoEtapa
    {
        return DB::transaction(function () use ($etapa, $data, $userId) {
            $statusAnterior = $etapa->status_normalizado;

            $etapa->fill([
                'observacoes' => $data['observacoes'] ?? $etapa->observacoes,
                'data_limite' => $data['data_limite'] ?? $etapa->data_limite,
            ]);

            if ($etapa->status_normalizado === 'LIBERADA') {
                $etapa->status = 'EM_ANDAMENTO';
            }

            $etapa->save();

            $this->registrarHistorico(
                $etapa,
                $userId,
                'editada',
                'Observações da etapa atualizadas.',
                $statusAnterior,
                $etapa->status_normalizado,
                $data
            );

            return $this->freshEtapa($etapa);
        });
    }

    public function aprovar(ProcessoEtapa $etapa, array $data, ?int $userId = null): ProcessoEtapa
    {
        return DB::transaction(function () use ($etapa, $data, $userId) {
            $statusAnterior = $etapa->status_normalizado;

            $etapa->fill([
                'observacoes' => $data['observacoes'] ?? $etapa->observacoes,
                'data_limite' => $data['data_limite'] ?? $etapa->data_limite,
                'status' => 'APROVADA',
                'data_conclusao' => now(),
                'aprovado_por_user_id' => $userId,
            ]);

            $etapa->save();

            $this->registrarHistorico(
                $etapa,
                $userId,
                'aprovada',
                'Etapa aprovada e concluída.',
                $statusAnterior,
                'APROVADA',
                $data
            );

            $this->liberarProximaEtapa($etapa, $userId);

            return $this->freshEtapa($etapa);
        });
    }

    public function reprovar(ProcessoEtapa $etapa, array $data, ?int $userId = null): ProcessoEtapa
    {
        return DB::transaction(function () use ($etapa, $data, $userId) {
            $statusAnterior = $etapa->status_normalizado;

            $etapa->fill([
                'observacoes' => $data['observacoes'] ?? $etapa->observacoes,
                'status' => 'REPROVADA',
                'motivo_reprovacao' => $data['motivo_reprovacao'] ?? $data['observacoes'] ?? null,
                'reprovado_por_user_id' => $userId,
                'reprovado_em' => now(),
            ]);

            $etapa->save();

            $this->registrarHistorico(
                $etapa,
                $userId,
                'reprovada',
                'Etapa reprovada.',
                $statusAnterior,
                'REPROVADA',
                $data
            );

            return $this->freshEtapa($etapa);
        });
    }

    public function cancelar(ProcessoEtapa $etapa, array $data, ?int $userId = null): ProcessoEtapa
    {
        return DB::transaction(function () use ($etapa, $data, $userId) {
            $statusAnterior = $etapa->status_normalizado;

            $etapa->fill([
                'observacoes' => $data['observacoes'] ?? $etapa->observacoes,
                'status' => 'CANCELADA',
                'motivo_cancelamento' => $data['motivo_cancelamento'] ?? $data['observacoes'] ?? null,
                'cancelado_por_user_id' => $userId,
                'cancelado_em' => now(),
            ]);

            $etapa->save();

            $this->registrarHistorico(
                $etapa,
                $userId,
                'cancelada',
                'Etapa cancelada.',
                $statusAnterior,
                'CANCELADA',
                $data
            );

            return $this->freshEtapa($etapa);
        });
    }

    public function anexar(ProcessoEtapa $etapa, UploadedFile $arquivo, ?int $userId = null): ProcessoEtapaAnexo
    {
        $path = $arquivo->store('processos/etapas', 'public');

        $anexo = ProcessoEtapaAnexo::create([
            'processo_etapa_id' => $etapa->id,
            'user_id' => $userId,
            'nome_original' => $arquivo->getClientOriginalName(),
            'arquivo' => $path,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho' => $arquivo->getSize(),
        ]);

        ProcessoEtapaHistorico::create([
            'processo_etapa_id' => $etapa->id,
            'user_id' => $userId,
            'acao' => 'anexo_adicionado',
            'descricao' => 'Anexo adicionado à etapa.',
            'status_anterior' => $etapa->status_normalizado,
            'status_novo' => $etapa->status_normalizado,
            'anexo_path' => $path,
            'anexo_nome' => $arquivo->getClientOriginalName(),
        ]);

        return $anexo;
    }

    protected function liberarProximaEtapa(ProcessoEtapa $etapa, ?int $userId = null): void
    {
        if ($etapa->status_normalizado !== 'APROVADA') {
            return;
        }

        $query = ProcessoEtapa::query()->where('processo_contratacao_id', $etapa->processo_contratacao_id);

        if (!empty($etapa->origem_tipo)) {
            $query->where('origem_tipo', $etapa->origem_tipo);
        } else {
            $query->where(function ($q) {
                $q->whereNull('origem_tipo')->orWhere('origem_tipo', 'CONTRATO');
            });
        }

        if (!empty($etapa->processo_aditivo_id)) {
            $query->where('processo_aditivo_id', $etapa->processo_aditivo_id);
        } else {
            $query->whereNull('processo_aditivo_id');
        }

        $proxima = $query->where('ordem', '>', $etapa->ordem)->orderBy('ordem')->first();

        if (!$proxima || $proxima->status_normalizado !== 'BLOQUEADA') {
            return;
        }

        $inicio = $etapa->data_conclusao ? Carbon::parse($etapa->data_conclusao)->startOfDay() : now()->startOfDay();
        $prazo = (clone $inicio)->addDays((int) $proxima->prazo_limite_dias);

        $proxima->update([
            'status' => 'LIBERADA',
            'data_inicio' => $inicio->toDateString(),
            'data_prazo_original' => $prazo->toDateString(),
            'data_limite' => $prazo->toDateString(),
        ]);

        $this->registrarHistorico(
            $proxima,
            $userId,
            'liberada',
            'Etapa liberada automaticamente pela aprovação da etapa anterior.',
            'BLOQUEADA',
            'LIBERADA'
        );
    }

    protected function registrarHistorico(
        ProcessoEtapa $etapa,
        ?int $userId,
        string $acao,
        string $descricao,
        ?string $statusAnterior = null,
        ?string $statusNovo = null,
        array $dados = []
    ): void {
        ProcessoEtapaHistorico::create([
            'processo_etapa_id' => $etapa->id,
            'user_id' => $userId,
            'acao' => $acao,
            'descricao' => $descricao,
            'status_anterior' => $statusAnterior,
            'status_novo' => $statusNovo,
            'observacoes' => $dados['observacoes'] ?? null,
        ]);
    }

    protected function freshEtapa(ProcessoEtapa $etapa): ProcessoEtapa
    {
        return $etapa->fresh([
            'processo.etapas',
            'aditivo',
            'aprovadoPor',
            'reprovadoPor',
            'canceladoPor',
            'setor.usuarios',
            'template',
            'historicos.usuario',
            'anexos.usuario',
        ]);
    }
}
