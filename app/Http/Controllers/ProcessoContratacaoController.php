<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\EtapaTemplate;
use App\Models\ProcessoContratacao;
use App\Models\ProcessoHistorico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProcessoContratacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view processos contratacao')->only(['index', 'data', 'show', 'stats']);
        $this->middleware('permission:create processos contratacao')->only(['create', 'store']);
        $this->middleware('permission:edit processos contratacao')->only(['edit', 'update']);
        $this->middleware('permission:delete processos contratacao')->only(['destroy']);
    }

    public function index()
    {
        $empresas = Empresa::orderBy('razao_social')->get(['id', 'razao_social']);
        $contratantes = Empresa::whereIn('tipo_empresa', ['CONTRATANTE', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $contratadas = Empresa::whereIn('tipo_empresa', ['CONTRATADA', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $statusOptions = [
            'EM_ANALISE' => 'Em Análise',
            'APROVADO' => 'Aprovado',
            'CONTRATADO' => 'Contratado',
            'REPROVADO' => 'Reprovado',
            'CANCELADO' => 'Cancelado',
        ];

        return view('admin.processos_contratacao.processos.index', compact('empresas', 'contratantes', 'contratadas', 'statusOptions'));
    }

    public function data(Request $request)
    {
        $query = ProcessoContratacao::with(['contratante', 'contratada', 'etapas'])
            ->select('processos_contratacao.*');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('empresa_contratada_id')) {
            $query->where('empresa_contratada_id', $request->empresa_contratada_id);
        }
        if ($request->filled('empresa_contratante_id')) {
            $query->where('empresa_contratante_id', $request->empresa_contratante_id);
        }
        if ($request->filled('tipo_contratacao')) {
            $query->where('tipo_contratacao', $request->tipo_contratacao);
        }

        return DataTables::of($query)
            ->addColumn('contratante_nome', fn ($row) => $row->contratante->razao_social ?? '-')
            ->addColumn('contratada_nome', fn ($row) => $row->contratada->razao_social ?? '-')
            ->addColumn('etapa_atual_nome', fn ($row) => $row->etapa_atual_real?->nome_etapa ?? 'Concluído')
            ->addColumn('actions', function ($processo) {
                return view('admin.processos_contratacao.processos._actions', compact('processo'))->render();
            })
            ->editColumn('valor_proposto', fn ($row) => $row->valor_proposto ? 'R$ ' . number_format($row->valor_proposto, 2, ',', '.') : '-')
            ->editColumn('valor_aprovado_final', fn ($row) => $row->valor_aprovado_final ? 'R$ ' . number_format($row->valor_aprovado_final, 2, ',', '.') : '-')
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function stats(Request $request)
    {
        $query = ProcessoContratacao::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('empresa_contratada_id')) {
            $query->where('empresa_contratada_id', $request->empresa_contratada_id);
        }
        if ($request->filled('empresa_contratante_id')) {
            $query->where('empresa_contratante_id', $request->empresa_contratante_id);
        }
        if ($request->filled('tipo_contratacao')) {
            $query->where('tipo_contratacao', $request->tipo_contratacao);
        }

        $idsFiltrados = (clone $query)->pluck('id');
        $etapasAtrasadas = \App\Models\ProcessoEtapa::query()
            ->whereIn('processo_contratacao_id', $idsFiltrados)
            ->whereIn('status', ['LIBERADA', 'EM_ANDAMENTO', 'PENDENTE', 'AGUARDANDO'])
            ->whereDate('data_limite', '<', now()->toDateString())
            ->count();

        return response()->json([
            'total_processos' => (clone $query)->count(),
            'em_analise' => (clone $query)->where('status', 'EM_ANALISE')->count(),
            'aprovados' => (clone $query)->whereIn('status', ['APROVADO', 'CONTRATADO'])->count(),
            'etapas_atrasadas' => $etapasAtrasadas,
            'valor_total_proposto' => 'R$ ' . number_format((float) (clone $query)->sum('valor_proposto'), 2, ',', '.'),
            'valor_total_aprovado' => 'R$ ' . number_format((float) (clone $query)->sum('valor_aprovado_final'), 2, ',', '.'),
        ]);
    }

    public function create()
    {
        $empresas = Empresa::where('ativo', true)->orderBy('razao_social')->get();
        $empresaContratantePadraoId = Empresa::where('cnpj', '60.437.929/0001-04')->value('id');
        $contratantes = Empresa::whereIn('tipo_empresa', ['CONTRATANTE', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $contratadas = Empresa::whereIn('tipo_empresa', ['CONTRATADA', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $statusOptions = [
            'EM_ANALISE' => 'Em Análise',
            'APROVADO' => 'Aprovado',
            'CONTRATADO' => 'Contratado',
            'REPROVADO' => 'Reprovado',
            'CANCELADO' => 'Cancelado',
        ];

        return view('admin.processos_contratacao.processos.create', compact('empresas', 'contratantes', 'contratadas', 'statusOptions', 'empresaContratantePadraoId'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data = array_map(fn ($value) => $value === '' ? null : $value, $data);
        $data['criado_por'] = Auth::id();
        $data['atualizado_por'] = Auth::id();
        $data['origem'] = $data['origem'] ?? 'MANUAL';
        $data['numero_processo_interno'] = $data['numero_processo_interno'] ?: $this->gerarNumeroInterno();

        $processo = ProcessoContratacao::create($data);
        $this->gerarEtapasPadrao($processo);

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processo->id,
            'tipo_evento' => 'CRIACAO',
            'descricao' => 'Processo criado.',
            'dados_json' => ['status' => $processo->status],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('processos-contratacao.show', $processo)->with('success', 'Processo de contratação criado com sucesso.');
    }

    public function show(ProcessoContratacao $processoContratacao)
    {
        $processoContratacao->load([
            'contratante',
            'contratada',
            'etapas.aprovadoPor',
            'etapas.reprovadoPor',
            'etapas.canceladoPor',
            'etapas.setor.usuarios',
            'etapas.template',
            'etapas.historicos.usuario',
            'etapas.anexos.usuario',
            'anexos',
            'itens.anexo',
            'aditivos',
            'historicos.usuario',
        ]);

        return view('admin.processos_contratacao.processos.show', [
            'processo' => $processoContratacao,
        ]);
    }

    public function edit(ProcessoContratacao $processoContratacao)
    {
        $empresas = Empresa::where('ativo', true)->orderBy('razao_social')->get();
        $contratantes = Empresa::whereIn('tipo_empresa', ['CONTRATANTE', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $contratadas = Empresa::whereIn('tipo_empresa', ['CONTRATADA', 'AMBAS'])->orderBy('razao_social')->get(['id', 'razao_social']);
        $statusOptions = [
            'EM_ANALISE' => 'Em Análise',
            'APROVADO' => 'Aprovado',
            'CONTRATADO' => 'Contratado',
            'REPROVADO' => 'Reprovado',
            'CANCELADO' => 'Cancelado',
        ];

        return view('admin.processos_contratacao.processos.edit', [
            'processo' => $processoContratacao,
            'empresas' => $empresas,
            'contratantes' => $contratantes,
            'contratadas' => $contratadas,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, ProcessoContratacao $processoContratacao)
    {
        $data = $this->validatedData($request);
        $data['atualizado_por'] = Auth::id();
        $processoContratacao->update($data);

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_evento' => 'ATUALIZACAO',
            'descricao' => 'Processo atualizado.',
            'dados_json' => ['status' => $processoContratacao->status],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('processos-contratacao.show', $processoContratacao)->with('success', 'Processo atualizado com sucesso.');
    }

    public function destroy(ProcessoContratacao $processoContratacao)
    {
        $processoContratacao->delete();
        return redirect()->route('processos-contratacao.index')->with('success', 'Processo excluído com sucesso.');
    }

    protected function authorizeResumoGeral(): void
    {
        abort_unless(
            auth()->user()?->can('edit processos contratacao') ||
            auth()->user()?->can('manage processos contratacao'),
            403,
            'Você não possui permissão para atualizar o resumo geral.'
        );
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'numero_processo_interno' => 'nullable|string|max:100',
            'titulo' => 'required|string|max:255',
            'empresa_contratante_id' => 'required|exists:empresas,id',
            'empresa_contratada_id' => 'required|exists:empresas,id',
            'tipo_contratacao' => 'required|in:NOVO_CONTRATO,ADITIVO',
            'categoria' => 'nullable|string|max:100',
            'origem' => 'nullable|string|max:100',
            'objeto_resumido' => 'required|string|max:255',
            'escopo_detalhado' => 'nullable|string',
            'status' => 'required|string|max:50',
            'prioridade' => 'nullable|string|max:50',
            'valor_estimado' => 'nullable|numeric|min:0',
            'valor_proposto' => 'nullable|numeric|min:0',
            'valor_aprovado_final' => 'nullable|numeric|min:0',
            'numero_contrato_assinado' => 'nullable|string|max:100',
            'data_solicitacao' => 'nullable|date',
            'data_recebimento_proposta' => 'nullable|date',
            'validade_proposta' => 'nullable|date',
            'prazo_execucao_inicio' => 'nullable|date',
            'prazo_execucao_fim' => 'nullable|date',
            'vigencia_inicio' => 'nullable|date',
            'vigencia_fim' => 'nullable|date',
            'prazo_pagamento_dias' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
        ]);
    }

    protected function gerarEtapasPadrao(ProcessoContratacao $processo): void
    {
        $dataBase = $processo->data_solicitacao ?: now()->toDateString();
        $templates = EtapaTemplate::where('ativo', true)->orderBy('ordem')->get();

        foreach ($templates as $template) {
            $inicio = $template->ordem == 1 ? $dataBase : null;
            $limite = $template->ordem == 1 ? now()->parse($dataBase)->addDays((int) $template->prazo_limite_dias)->toDateString() : null;

            $processo->etapas()->create([
                'origem_tipo' => 'CONTRATO',
                'processo_aditivo_id' => null,
                'etapa_template_id' => $template->id,
                'nome_etapa' => $template->nome,
                'ordem' => $template->ordem,
                'setor_responsavel' => $template->setor_responsavel,
                'prazo_limite_dias' => $template->prazo_limite_dias,
                'data_inicio' => $inicio,
                'data_limite' => $limite,
                'setor_id' => $template->setor_id,
                'data_prazo_original' => $limite,
                'status' => $template->ordem == 1 ? 'LIBERADA' : 'BLOQUEADA',
                'cor_badge' => $template->cor_badge,
                'permite_anexo' => $template->permite_anexo,
            ]);
        }
    }

    protected function gerarNumeroInterno(): string
    {
        return 'PC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    public function updateResumoGeral(Request $request, ProcessoContratacao $processoContratacao)
    {
        abort_unless(
            auth()->user()?->can('edit processos contratacao') ||
            auth()->user()?->can('manage processos contratacao'),
            403,
            'Você não possui permissão para atualizar o resumo geral.'
        );

        $data = $request->validate([
            'status' => 'required|string|max:50',
            'numero_contrato_assinado' => 'nullable|string|max:255',
        ]);

        $statusAnterior = $processoContratacao->status;
        $numeroAnterior = $processoContratacao->numero_contrato_assinado;

        $processoContratacao->update([
            'status' => $data['status'],
            'numero_contrato_assinado' => $data['numero_contrato_assinado'] ?? null,
            'atualizado_por' => auth()->id(),
        ]);

        $alteracoes = [];

        if ($statusAnterior !== $processoContratacao->status) {
            $alteracoes[] = "Status alterado de '{$statusAnterior}' para '{$processoContratacao->status}'";
        }

        if (($numeroAnterior ?? '') !== ($processoContratacao->numero_contrato_assinado ?? '')) {
            $numeroAntigoTexto = $numeroAnterior ?: 'vazio';
            $numeroNovoTexto = $processoContratacao->numero_contrato_assinado ?: 'vazio';
            $alteracoes[] = "Número do contrato alterado de '{$numeroAntigoTexto}' para '{$numeroNovoTexto}'";
        }

        $descricaoHistorico = count($alteracoes)
            ? implode(' | ', $alteracoes)
            : 'Resumo geral atualizado sem alteração de valores.';

        // Histórico do processo
        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_evento' => 'RESUMO_GERAL_ATUALIZADO',
            'descricao' => $descricaoHistorico,
            'dados_json' => [
                'antes' => [
                    'status' => $statusAnterior,
                    'numero_contrato_assinado' => $numeroAnterior,
                ],
                'depois' => [
                    'status' => $processoContratacao->status,
                    'numero_contrato_assinado' => $processoContratacao->numero_contrato_assinado,
                ],
            ],
            'user_id' => auth()->id(),
        ]);

        // Log de atividade do sistema
        activity('processo_contratacao')
            ->performedOn($processoContratacao)
            ->causedBy(auth()->user())
            ->withProperties([
                'antes' => [
                    'status' => $statusAnterior,
                    'numero_contrato_assinado' => $numeroAnterior,
                ],
                'depois' => [
                    'status' => $processoContratacao->status,
                    'numero_contrato_assinado' => $processoContratacao->numero_contrato_assinado,
                ],
            ])
            ->log('Resumo geral do processo atualizado');

        return redirect()
            ->route('processos-contratacao.show', $processoContratacao)
            ->with('success', 'Resumo geral atualizado com sucesso.');
    }
}
