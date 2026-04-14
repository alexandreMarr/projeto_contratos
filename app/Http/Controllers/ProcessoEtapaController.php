<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessoEtapaExtraRequest;
use App\Http\Requests\UpdateProcessoEtapaRequest;
use App\Models\ProcessoEtapa;
use App\Models\ProcessoHistorico;
use App\Services\ProcessoEtapaFluxoService;
use Illuminate\Support\Facades\Auth;

class ProcessoEtapaController extends Controller
{
    public function __construct(protected ProcessoEtapaFluxoService $fluxoService)
    {
        $this->middleware('permission:manage etapas processos contratacao')->only(['storeExtra']);
    }

    public function update(UpdateProcessoEtapaRequest $request, ProcessoEtapa $etapa)
    {
        $user = Auth::user();
        $acao = (string) ($request->input('acao') ?: $request->input('_acao'));

        if (!$etapa->userPodeVisualizar($user) && !($user && $user->can('manage etapas processos contratacao'))) {
            return response()->json([
                'success' => false,
                'message' => 'Você não possui permissão para visualizar esta etapa.',
            ], 403);
        }

        if (!$acao) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não informada.',
            ], 422);
        }

        if (in_array($acao, ['salvar', 'aprovar', 'reprovar'], true) && $etapa->esta_bloqueada) {
            return response()->json([
                'success' => false,
                'message' => 'Esta etapa está bloqueada e depende da aprovação da etapa anterior.',
            ], 422);
        }

        if ($request->hasFile('anexo')) {
            $podeAnexar = $etapa->permite_anexo && (
                $etapa->userPodeEditar($user)
                || $etapa->userPodeAprovar($user)
                || $etapa->userPodeReprovar($user)
                || ($user && $user->can('manage etapas processos contratacao'))
            );

            if (!$podeAnexar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não possui permissão para anexar arquivos nesta etapa.',
                ], 403);
            }

            $this->fluxoService->anexar($etapa, $request->file('anexo'), $user?->id);
        }

        $payload = $request->validated();

        if ($acao === 'salvar') {
            if (!$etapa->userPodeEditar($user) && !($user && $user->can('manage etapas processos contratacao'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não possui permissão para editar esta etapa.',
                ], 403);
            }

            $etapa = $this->fluxoService->atualizar($etapa, $payload, $user?->id);
        } elseif ($acao === 'aprovar') {
            if (!$etapa->userPodeAprovar($user) && !($user && $user->can('manage etapas processos contratacao'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não possui permissão para aprovar esta etapa.',
                ], 403);
            }

            $etapa = $this->fluxoService->aprovar($etapa, $payload, $user?->id);
        } elseif ($acao === 'reprovar') {
            if (!$etapa->userPodeReprovar($user) && !($user && $user->can('manage etapas processos contratacao'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não possui permissão para reprovar esta etapa.',
                ], 403);
            }

            $etapa = $this->fluxoService->reprovar($etapa, $payload, $user?->id);
        } elseif ($acao === 'cancelar') {
            if (!($user && $user->can('manage etapas processos contratacao'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Somente administradores podem cancelar etapas.',
                ], 403);
            }

            $etapa = $this->fluxoService->cancelar($etapa, $payload, $user?->id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ação inválida.',
            ], 422);
        }

        ProcessoHistorico::create([
            'processo_contratacao_id' => $etapa->processo_contratacao_id,
            'tipo_evento' => 'ETAPA_' . strtoupper($acao),
            'descricao' => "Etapa '{$etapa->nome_etapa}' processada com ação {$acao}.",
            'dados_json' => [
                'etapa_id' => $etapa->id,
                'status' => $etapa->status,
                'acao' => $acao,
                'origem_tipo' => $etapa->origem_tipo,
                'processo_aditivo_id' => $etapa->processo_aditivo_id,
            ],
            'user_id' => $user?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Etapa processada com sucesso.',
            'etapa' => $etapa,
        ]);
    }

    public function storeExtra(StoreProcessoEtapaExtraRequest $request)
    {
        $data = $request->validated();
        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);
        $data['status'] = $data['status'] ?? 'BLOQUEADA';
        $data['data_prazo_original'] = null;
        $data['data_limite'] = null;
        $data['data_inicio'] = null;
        $data['origem_tipo'] = $data['origem_tipo'] ?? 'CONTRATO';

        $etapa = ProcessoEtapa::create($data);

        ProcessoHistorico::create([
            'processo_contratacao_id' => $etapa->processo_contratacao_id,
            'tipo_evento' => 'ETAPA_EXTRA_CRIADA',
            'descricao' => "Etapa extra '{$etapa->nome_etapa}' criada.",
            'dados_json' => $data,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Etapa extra criada com sucesso.',
            'etapa' => $etapa,
        ]);
    }
}
