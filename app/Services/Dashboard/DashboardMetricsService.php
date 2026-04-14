<?php

namespace App\Services\Dashboard;

use App\Models\DashboardSavedFilter;
use App\Models\DashboardWidget;
use App\Models\Empresa;
use App\Models\ProcessoAditivo;
use App\Models\ProcessoContratacao;
use App\Models\ProcessoEtapa;
use App\Models\ProcessoEtapaHistorico;
use App\Models\ProcessoHistorico;
use App\Models\ProcessoItem;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardMetricsService
{
    public function build(User $user, Request $request): array
    {
        $filtros = $this->sanitizeFilters($request);

        $processosQuery = $this->baseProcessosQuery($user, $filtros);
        $etapasQuery = $this->baseEtapasQuery($user, $filtros);
        $aditivosQuery = $this->baseAditivosQuery($user, $filtros);
        $itensQuery = $this->baseItensQuery($user, $filtros);
        $empresasQuery = $this->baseEmpresasQuery($user, $filtros);

        $processos = (clone $processosQuery)->with(['contratante:id,razao_social', 'contratada:id,razao_social', 'etapas', 'aditivos', 'itens', 'anexos'])->get();
        $etapas = (clone $etapasQuery)->with(['setor:id,nome', 'processo:id,titulo'])->get();
        $aditivos = (clone $aditivosQuery)->with(['processo:id,titulo'])->get();
        $itens = (clone $itensQuery)->get();
        $empresas = (clone $empresasQuery)->get();

        $cards = $this->buildCards($processos, $etapas, $aditivos, $itens, $empresas);
        $metricMap = $this->buildMetricMap($cards);

        return [
            'filtros' => $filtros,
            'cards' => $cards,
            'charts' => [
                'processos_por_mes' => $this->processosPorMes($processos),
                'processos_por_status' => $this->groupCountFromCollection($processos, fn ($item) => $this->displayStatus($item->status), 8),
                'processos_por_categoria' => $this->groupCountFromCollection($processos, fn ($item) => $this->displayValue($item->categoria), 8),
                'processos_por_tipo' => $this->groupCountFromCollection($processos, fn ($item) => $this->displayValue($item->tipo_contratacao), 8),
                'ranking_contratadas' => $this->rankingFromCollection($processos, fn ($item) => optional($item->contratada)->razao_social ?: 'Sem contratada', 10),
                'empresas_por_tipo' => $this->groupCountFromCollection($empresas, fn ($item) => $this->displayValue($item->tipo_empresa), 8),
                'empresas_por_uf' => $this->groupCountFromCollection($empresas, fn ($item) => $this->displayValue($item->uf), 10),
                'etapas_por_status' => $this->groupCountFromCollection($etapas, fn ($item) => $this->displayStatus($item->status_exibicao ?? $item->status), 8),
                'etapas_por_setor' => $this->rankingFromCollection($etapas, fn ($item) => optional($item->setor)->nome ?: $this->displayValue($item->setor_responsavel), 10),
                'etapas_por_ordem' => $this->groupCountFromCollection($etapas, fn ($item) => 'Ordem ' . ((int) $item->ordem), 12, true),
                'aditivos_por_tipo' => $this->groupCountFromCollection($aditivos, fn ($item) => $this->displayValue($item->tipo), 8),
                'aditivos_top_percentual' => $this->topAditivosPercentualFromCollection($aditivos),
                'itens_por_grupo' => $this->rankingFromCollection($itens, fn ($item) => $this->displayValue($item->grupo), 10),
                'itens_por_subgrupo' => $this->rankingFromCollection($itens, fn ($item) => $this->displayValue($item->subgrupo), 10),
                'auditoria_por_tipo' => $this->auditoriaPorTipo($user, $filtros),
                'auditoria_top_usuarios' => $this->auditoriaTopUsuarios($user, $filtros),
            ],
            'alertas' => $this->buildAlertas($processos, $etapas, $aditivos, $empresas),
            'auditoria' => $this->buildAuditoria($user, $filtros),
            'widgets_disponiveis' => $this->availableWidgetMetrics(),
            'widgets_salvos' => $this->savedWidgets($user, $metricMap),
            'filtros_salvos' => $this->savedFilters($user),
            'options' => $this->filterOptions(),
            'resumo' => $this->buildResumoExecutivo($processos, $etapas, $aditivos),
        ];
    }

    public function availableWidgetMetrics(): array
    {
        return [
            'empresas_ativas' => 'Empresas ativas',
            'total_processos' => 'Total de processos',
            'processos_em_analise' => 'Processos em análise',
            'processos_aprovados' => 'Processos aprovados',
            'etapas_atrasadas' => 'Etapas atrasadas',
            'valor_total_proposto' => 'Valor total proposto',
            'valor_contratual_atual' => 'Valor contratual atual',
            'total_aditivos' => 'Total de aditivos',
            'valor_total_aditivos' => 'Valor total de aditivos',
            'percentual_medio_aditivo' => 'Percentual médio de aditivo',
            'contratos_risco_vigencia' => 'Contratos com risco de vigência',
            'contratos_pendencia_etapa' => 'Contratos com pendência de etapa',
            'empresas_sem_contato' => 'Empresas sem contato',
            'contratos_sem_numero_assinado' => 'Contratos sem número assinado',
            'aditivos_acima_limite' => 'Aditivos acima de 30%',
            'itens_ativos' => 'Itens ativos',
            'movimentacoes_periodo' => 'Movimentações do período',
        ];
    }

    protected function sanitizeFilters(Request $request): array
    {
        return [
            'empresa_contratante_id' => $request->integer('empresa_contratante_id') ?: null,
            'empresa_contratada_id' => $request->integer('empresa_contratada_id') ?: null,
            'status' => $request->filled('status') ? trim((string) $request->input('status')) : null,
            'tipo_contratacao' => $request->filled('tipo_contratacao') ? trim((string) $request->input('tipo_contratacao')) : null,
            'categoria' => $request->filled('categoria') ? trim((string) $request->input('categoria')) : null,
            'setor_id' => $request->integer('setor_id') ?: null,
            'data_inicio' => $request->filled('data_inicio') ? Carbon::parse($request->input('data_inicio'))->startOfDay() : now()->startOfYear(),
            'data_fim' => $request->filled('data_fim') ? Carbon::parse($request->input('data_fim'))->endOfDay() : now()->endOfDay(),
        ];
    }

    protected function baseProcessosQuery(User $user, array $filtros): Builder
    {
        $query = ProcessoContratacao::query()
            ->whereBetween('processos_contratacao.created_at', [$filtros['data_inicio'], $filtros['data_fim']]);

        if ($filtros['empresa_contratante_id']) {
            $query->where('empresa_contratante_id', $filtros['empresa_contratante_id']);
        }
        if ($filtros['empresa_contratada_id']) {
            $query->where('empresa_contratada_id', $filtros['empresa_contratada_id']);
        }
        if ($filtros['status']) {
            $query->whereRaw("UPPER(COALESCE(processos_contratacao.status, '')) = ?", [mb_strtoupper($filtros['status'])]);
        }
        if ($filtros['tipo_contratacao']) {
            $query->where('tipo_contratacao', $filtros['tipo_contratacao']);
        }
        if ($filtros['categoria']) {
            $query->where('categoria', $filtros['categoria']);
        }
        if ($filtros['setor_id']) {
            $query->whereHas('etapas', fn ($q) => $q->where('setor_id', $filtros['setor_id']));
        }

        return $this->applyScopeToProcessos($query, $user, $filtros['setor_id']);
    }

    protected function baseEtapasQuery(User $user, array $filtros): Builder
    {
        $query = ProcessoEtapa::query()
            ->whereBetween('processo_etapas.created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->whereHas('processo', function ($q) use ($user, $filtros) {
                $q->whereBetween('processos_contratacao.created_at', [$filtros['data_inicio'], $filtros['data_fim']]);
                if ($filtros['empresa_contratante_id']) {
                    $q->where('empresa_contratante_id', $filtros['empresa_contratante_id']);
                }
                if ($filtros['empresa_contratada_id']) {
                    $q->where('empresa_contratada_id', $filtros['empresa_contratada_id']);
                }
                if ($filtros['status']) {
                    $q->whereRaw("UPPER(COALESCE(processos_contratacao.status, '')) = ?", [mb_strtoupper($filtros['status'])]);
                }
                if ($filtros['tipo_contratacao']) {
                    $q->where('tipo_contratacao', $filtros['tipo_contratacao']);
                }
                if ($filtros['categoria']) {
                    $q->where('categoria', $filtros['categoria']);
                }
                $this->applyScopeToProcessos($q, $user, $filtros['setor_id']);
            });

        if ($filtros['setor_id']) {
            $query->where('setor_id', $filtros['setor_id']);
        }

        return $this->applyScopeToEtapas($query, $user, $filtros['setor_id']);
    }

    protected function baseAditivosQuery(User $user, array $filtros): Builder
    {
        return ProcessoAditivo::query()
            ->whereBetween('processo_aditivos.created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->whereHas('processo', function ($q) use ($user, $filtros) {
                $q->whereBetween('processos_contratacao.created_at', [$filtros['data_inicio'], $filtros['data_fim']]);
                if ($filtros['empresa_contratante_id']) {
                    $q->where('empresa_contratante_id', $filtros['empresa_contratante_id']);
                }
                if ($filtros['empresa_contratada_id']) {
                    $q->where('empresa_contratada_id', $filtros['empresa_contratada_id']);
                }
                if ($filtros['status']) {
                    $q->whereRaw("UPPER(COALESCE(processos_contratacao.status, '')) = ?", [mb_strtoupper($filtros['status'])]);
                }
                if ($filtros['tipo_contratacao']) {
                    $q->where('tipo_contratacao', $filtros['tipo_contratacao']);
                }
                if ($filtros['categoria']) {
                    $q->where('categoria', $filtros['categoria']);
                }
                $this->applyScopeToProcessos($q, $user, $filtros['setor_id']);
            });
    }

    protected function baseItensQuery(User $user, array $filtros): Builder
    {
        return ProcessoItem::query()
            ->whereBetween('processo_itens.created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->whereHas('processo', function ($q) use ($user, $filtros) {
                $q->whereBetween('processos_contratacao.created_at', [$filtros['data_inicio'], $filtros['data_fim']]);
                if ($filtros['empresa_contratante_id']) {
                    $q->where('empresa_contratante_id', $filtros['empresa_contratante_id']);
                }
                if ($filtros['empresa_contratada_id']) {
                    $q->where('empresa_contratada_id', $filtros['empresa_contratada_id']);
                }
                if ($filtros['status']) {
                    $q->whereRaw("UPPER(COALESCE(processos_contratacao.status, '')) = ?", [mb_strtoupper($filtros['status'])]);
                }
                if ($filtros['tipo_contratacao']) {
                    $q->where('tipo_contratacao', $filtros['tipo_contratacao']);
                }
                if ($filtros['categoria']) {
                    $q->where('categoria', $filtros['categoria']);
                }
                $this->applyScopeToProcessos($q, $user, $filtros['setor_id']);
            });
    }

    protected function baseEmpresasQuery(User $user, array $filtros): Builder
    {
        $query = Empresa::query();

        if ($filtros['empresa_contratante_id'] || $filtros['empresa_contratada_id'] || $filtros['setor_id'] || $filtros['status'] || $filtros['tipo_contratacao'] || $filtros['categoria']) {
            $processosIds = $this->baseProcessosQuery($user, $filtros)->pluck('id');
            $query->where(function ($q) use ($processosIds) {
                $q->whereHas('processosComoContratante', fn ($sub) => $sub->whereIn('id', $processosIds))
                    ->orWhereHas('processosComoContratada', fn ($sub) => $sub->whereIn('id', $processosIds));
            });
        }

        return $query;
    }

    protected function applyScopeToProcessos(Builder $query, User $user, ?int $setorId = null): Builder
    {
        if ($user->can('manage dashboard')) {
            return $query;
        }

        $setoresUsuario = $user->setores()
            ->wherePivot('ativo', true)
            ->pluck('setores.id')
            ->filter()
            ->unique()
            ->values();

        if ($setorId && $setoresUsuario->contains($setorId)) {
            $setoresUsuario = collect([$setorId]);
        }

        if ($setoresUsuario->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('etapas', fn ($q) => $q->whereIn('setor_id', $setoresUsuario));
    }

    protected function applyScopeToEtapas(Builder $query, User $user, ?int $setorId = null): Builder
    {
        if ($user->can('manage dashboard')) {
            return $query;
        }

        $setoresUsuario = $user->setores()
            ->wherePivot('ativo', true)
            ->pluck('setores.id')
            ->filter()
            ->unique()
            ->values();

        if ($setorId && $setoresUsuario->contains($setorId)) {
            $setoresUsuario = collect([$setorId]);
        }

        if ($setoresUsuario->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('setor_id', $setoresUsuario);
    }

    protected function buildCards(Collection $processos, Collection $etapas, Collection $aditivos, Collection $itens, Collection $empresas): array
    {
        $today = now()->endOfDay();
        $statusProcessos = $this->countByNormalizedStatus($processos, fn ($item) => $item->status);
        $statusEtapas = $this->countByNormalizedStatus($etapas, fn ($item) => $item->status_exibicao ?? $item->status);

        $valorContratualAtual = $processos->sum(fn ($p) => (float) $p->valor_contratual_atual);
        $contratosPendenciaEtapa = $processos->filter(fn ($p) => $p->etapas->contains(fn ($etapa) => !in_array($this->normalizeStatus($etapa->status_exibicao ?? $etapa->status), ['APROVADA', 'CANCELADA'], true)))->count();
        $etapasAtrasadas = $etapas->filter(fn ($etapa) => (bool) ($etapa->esta_atrasada ?? false))->count();

        return [
            'geral' => [
                'empresas_ativas' => $this->metricCard('Empresas ativas', $empresas->where('ativo', true)->count(), 'base empresarial ativa', 'emerald', 'fas fa-building'),
                'total_processos' => $this->metricCard('Total de processos', $processos->count(), 'volume total no período', 'blue', 'fas fa-file-signature'),
                'processos_em_analise' => $this->metricCard('Em análise', $statusProcessos['EM_ANALISE'] ?? 0, 'processos aguardando avanço', 'amber', 'fas fa-hourglass-half'),
                'processos_aprovados' => $this->metricCard('Aprovados', $statusProcessos['APROVADO'] ?? 0, 'já concluídos/aprovados', 'violet', 'fas fa-check-circle'),
                'etapas_atrasadas' => $this->metricCard('Etapas atrasadas', $etapasAtrasadas, 'itens que pedem atenção', 'red', 'fas fa-triangle-exclamation'),
                'valor_total_proposto' => $this->metricCard('Valor total proposto', $this->currency($processos->sum('valor_proposto')), 'somatório do valor proposto', 'slate', 'fas fa-sack-dollar'),
                'valor_contratual_atual' => $this->metricCard('Valor contratual atual', $this->currency($valorContratualAtual), 'considerando aditivos', 'indigo', 'fas fa-file-invoice-dollar'),
                'total_aditivos' => $this->metricCard('Total de aditivos', $aditivos->count(), 'aditivos localizados no filtro', 'purple', 'fas fa-file-circle-plus'),
                'valor_total_aditivos' => $this->metricCard('Valor total de aditivos', $this->currency($aditivos->sum('valor_aditivo')), 'impacto financeiro agregado', 'pink', 'fas fa-coins'),
                'percentual_medio_aditivo' => $this->metricCard('Percentual médio de aditivo', $this->percent($aditivos->avg('percentual_aditivo')), 'média dos percentuais', 'teal', 'fas fa-percent'),
                'contratos_risco_vigencia' => $this->metricCard('Risco de vigência', $processos->filter(fn ($item) => $item->vigencia_fim && $item->vigencia_fim->between($today->copy()->startOfDay(), $today->copy()->addDays(30)))->count(), 'vencimento em até 30 dias', 'orange', 'fas fa-calendar-xmark'),
                'contratos_pendencia_etapa' => $this->metricCard('Pendência de etapa', $contratosPendenciaEtapa, 'processos ainda não finalizados', 'cyan', 'fas fa-list-check'),
            ],
            'empresas' => [
                'total_empresas' => $this->metricCard('Total de empresas', $empresas->count(), 'cadastros visíveis no filtro', 'blue', 'fas fa-building-user'),
                'total_contratantes' => $this->metricCard('Contratantes', $empresas->filter(fn ($e) => mb_strtoupper((string) $e->tipo_empresa) === 'CONTRATANTE')->count(), 'empresas contratantes', 'violet', 'fas fa-handshake'),
                'total_contratadas' => $this->metricCard('Contratadas', $empresas->filter(fn ($e) => mb_strtoupper((string) $e->tipo_empresa) === 'CONTRATADA')->count(), 'fornecedores e executoras', 'emerald', 'fas fa-briefcase'),
                'empresas_sem_contato' => $this->metricCard('Sem contato', $empresas->filter(fn ($e) => blank($e->email) && blank($e->contato_principal))->count(), 'sem e-mail e responsável', 'amber', 'fas fa-envelope-open-text'),
                'empresas_com_pix' => $this->metricCard('Com PIX/Banco', $empresas->filter(fn ($e) => filled($e->chave_pix) || filled($e->banco))->count(), 'dados bancários cadastrados', 'teal', 'fas fa-money-check-dollar'),
            ],
            'contratos' => [
                'contratos_vencidos' => $this->metricCard('Contratos vencidos', $processos->filter(fn ($p) => $p->vigencia_fim && $p->vigencia_fim->lt(now()->startOfDay()))->count(), 'vigência já encerrada', 'red', 'fas fa-calendar-times'),
                'contratos_sem_numero_assinado' => $this->metricCard('Sem nº assinado', $processos->filter(fn ($p) => blank($p->numero_contrato_assinado))->count(), 'pendentes de numeração', 'amber', 'fas fa-file-circle-question'),
                'valor_estimado_total' => $this->metricCard('Valor estimado', $this->currency($processos->sum('valor_estimado')), 'estimativa consolidada', 'blue', 'fas fa-scale-balanced'),
                'valor_aprovado_total' => $this->metricCard('Valor aprovado', $this->currency($processos->sum('valor_aprovado_final')), 'aprovado formalmente', 'emerald', 'fas fa-stamp'),
            ],
            'etapas' => [
                'total_etapas' => $this->metricCard('Total de etapas', $etapas->count(), 'etapas no período filtrado', 'blue', 'fas fa-list-ol'),
                'etapas_aprovadas' => $this->metricCard('Etapas aprovadas', $statusEtapas['APROVADA'] ?? 0, 'fechadas com aprovação', 'emerald', 'fas fa-circle-check'),
                'etapas_em_andamento' => $this->metricCard('Em andamento', $statusEtapas['EM_ANDAMENTO'] ?? 0, 'execução ativa', 'violet', 'fas fa-person-running'),
                'etapas_bloqueadas' => $this->metricCard('Bloqueadas', $etapas->filter(fn ($e) => (bool) ($e->esta_bloqueada ?? false))->count(), 'dependem de etapa anterior', 'red', 'fas fa-lock'),
                'etapas_reprovadas' => $this->metricCard('Reprovadas', $statusEtapas['REPROVADA'] ?? 0, 'com devolutiva negativa', 'orange', 'fas fa-thumbs-down'),
                'etapas_canceladas' => $this->metricCard('Canceladas', $statusEtapas['CANCELADA'] ?? 0, 'encerradas sem continuidade', 'slate', 'fas fa-ban'),
            ],
            'aditivos' => [
                'aditivos_acima_limite' => $this->metricCard('Acima de 30%', $aditivos->filter(fn ($a) => (bool) $a->exige_aprovacao_conselho || (float) $a->percentual_aditivo > 30)->count(), 'exigem atenção gerencial', 'red', 'fas fa-gavel'),
                'contratos_com_aditivo' => $this->metricCard('Contratos com aditivo', $aditivos->pluck('processo_contratacao_id')->filter()->unique()->count(), 'contratos impactados', 'blue', 'fas fa-file-contract'),
                'valor_medio_aditivo' => $this->metricCard('Valor médio de aditivo', $this->currency($aditivos->avg('valor_aditivo')), 'média por registro', 'emerald', 'fas fa-chart-line'),
                'saldo_contrato_anterior' => $this->metricCard('Saldo contrato anterior', $this->currency($aditivos->sum('saldo_contrato_anterior')), 'saldo herdado', 'slate', 'fas fa-wallet'),
            ],
            'itens' => [
                'itens_ativos' => $this->metricCard('Itens ativos', $itens->where('ativo', true)->count(), 'linhas ativas na composição', 'emerald', 'fas fa-cubes'),
                'valor_total_itens' => $this->metricCard('Valor total itens', $this->currency($itens->sum('valor_total')), 'somatório das composições', 'blue', 'fas fa-boxes-stacked'),
                'quantidade_total' => $this->metricCard('Quantidade total', number_format((float) $itens->sum('quantidade'), 2, ',', '.'), 'quantidade consolidada', 'violet', 'fas fa-hashtag'),
                'origem_aditivo' => $this->metricCard('Itens de aditivo', $itens->filter(fn ($i) => mb_strtoupper((string) $i->origem_tipo) === 'ADITIVO')->count(), 'itens vindos de aditivos', 'amber', 'fas fa-layer-group'),
            ],
            'auditoria' => [
                'movimentacoes_periodo' => $this->metricCard('Movimentações do período', $this->movimentacoesPeriodo($processos), 'histórico + etapas + activity log', 'slate', 'fas fa-clock-rotate-left'),
            ],
        ];
    }

    protected function buildResumoExecutivo(Collection $processos, Collection $etapas, Collection $aditivos): array
    {
        $total = max($processos->count(), 1);
        $aprovados = $processos->filter(fn ($p) => $this->normalizeStatus($p->status) === 'APROVADO')->count();
        $etapasAtrasadas = $etapas->filter(fn ($e) => (bool) ($e->esta_atrasada ?? false))->count();

        return [
            [
                'titulo' => 'Taxa de aprovação',
                'valor' => $this->percent(($aprovados / $total) * 100),
                'descricao' => $aprovados . ' de ' . $processos->count() . ' processos aprovados',
                'icone' => 'fas fa-chart-line',
                'tema' => 'emerald',
            ],
            [
                'titulo' => 'Etapas em alerta',
                'valor' => (string) $etapasAtrasadas,
                'descricao' => 'etapas em atraso no recorte atual',
                'icone' => 'fas fa-bell',
                'tema' => 'red',
            ],
            [
                'titulo' => 'Impacto dos aditivos',
                'valor' => $this->currency($aditivos->sum('valor_aditivo')),
                'descricao' => 'valor consolidado dos aditivos',
                'icone' => 'fas fa-badge-dollar',
                'tema' => 'violet',
            ],
        ];
    }

    protected function buildMetricMap(array $cards): array
    {
        $map = [];
        foreach ($cards as $grupo) {
            foreach ($grupo as $key => $card) {
                $map[$key] = $card;
            }
        }
        return $map;
    }

    protected function buildAlertas(Collection $processos, Collection $etapas, Collection $aditivos, Collection $empresas): array
    {
        return [
            'criticos' => [
                'Contratos vencidos' => $processos->filter(fn ($item) => $item->vigencia_fim && $item->vigencia_fim->lt(now()->startOfDay()))->take(5)->values(),
                'Etapas atrasadas' => $etapas->filter(fn ($item) => (bool) ($item->esta_atrasada ?? false))->take(5)->values(),
                'Aditivos acima de 30%' => $aditivos->filter(fn ($item) => (bool) $item->exige_aprovacao_conselho || (float) $item->percentual_aditivo > 30)->take(5)->values(),
            ],
            'atencao' => [
                'Contratos próximos do fim' => $processos->filter(fn ($item) => $item->vigencia_fim && $item->vigencia_fim->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()))->take(5)->values(),
                'Sem número de contrato assinado' => $processos->filter(fn ($item) => blank($item->numero_contrato_assinado))->take(5)->values(),
                'Empresas sem contato ou e-mail' => $empresas->filter(fn ($item) => blank($item->email) && blank($item->contato_principal))->take(5)->values(),
            ],
            'informativos' => [
                'Processos sem atualização recente' => $processos->filter(fn ($item) => $item->updated_at && $item->updated_at->lt(now()->copy()->subDays(15)))->take(5)->values(),
                'Contratos sem itens' => $processos->filter(fn ($item) => $item->itens->isEmpty())->take(5)->values(),
                'Contratos sem anexos' => $processos->filter(fn ($item) => $item->anexos->isEmpty())->take(5)->values(),
            ],
        ];
    }

    protected function buildAuditoria(User $user, array $filtros): array
    {
        $processoIds = $this->baseProcessosQuery($user, $filtros)->pluck('id');
        $etapaIds = $this->baseEtapasQuery($user, $filtros)->pluck('id');

        $historicosProcessos = ProcessoHistorico::query()
            ->with('usuario:id,name')
            ->whereIn('processo_contratacao_id', $processoIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->latest()
            ->limit(15)
            ->get()
            ->map(function ($item) {
                return [
                    'fonte' => 'Histórico do processo',
                    'data' => optional($item->created_at)->format('d/m/Y H:i'),
                    'titulo' => $this->displayValue($item->tipo_evento),
                    'descricao' => $item->descricao,
                    'usuario' => optional($item->usuario)->name,
                ];
            });

        $historicosEtapas = ProcessoEtapaHistorico::query()
            ->with('usuario:id,name')
            ->whereIn('processo_etapa_id', $etapaIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->latest()
            ->limit(15)
            ->get()
            ->map(function ($item) {
                $desc = $item->descricao;
                if (blank($desc) && ($item->status_anterior || $item->status_novo)) {
                    $desc = trim(($item->status_anterior ?: '—') . ' → ' . ($item->status_novo ?: '—'));
                }

                return [
                    'fonte' => 'Histórico da etapa',
                    'data' => optional($item->created_at)->format('d/m/Y H:i'),
                    'titulo' => $this->displayValue($item->acao),
                    'descricao' => $desc ?: 'Movimentação registrada.',
                    'usuario' => optional($item->usuario)->name,
                ];
            });

        $timeline = $historicosProcessos
            ->concat($historicosEtapas)
            ->sortByDesc(function ($item) {
                return Carbon::createFromFormat('d/m/Y H:i', $item['data']);
            })
            ->take(20)
            ->values();

        return [
            'timeline' => $timeline,
        ];
    }

    protected function processosPorMes(Collection $processos): array
    {
        $rows = $processos
            ->groupBy(fn ($item) => optional($item->created_at)->format('Y-m'))
            ->map(fn ($items, $month) => [
                'mes' => $month,
                'total' => $items->count(),
            ])
            ->filter(fn ($item) => filled($item['mes']))
            ->sortBy('mes')
            ->values();

        return [
            'labels' => $rows->map(fn ($row) => Carbon::createFromFormat('Y-m', $row['mes'])->translatedFormat('M/Y'))->values(),
            'values' => $rows->pluck('total')->map(fn ($v) => (int) $v)->values(),
        ];
    }

    protected function groupCountFromCollection(Collection $items, callable $resolver, int $limit = 10, bool $sortNaturally = false): array
    {
        $grouped = $items
            ->map(function ($item) use ($resolver) {
                return $resolver($item) ?: 'Não informado';
            })
            ->countBy();

        if ($sortNaturally) {
            $grouped = $grouped->sortKeys();
        } else {
            $grouped = $grouped->sortDesc();
        }

        $grouped = $grouped->take($limit);

        return [
            'labels' => $grouped->keys()->values(),
            'values' => $grouped->values()->map(fn ($v) => (int) $v)->values(),
        ];
    }

    protected function rankingFromCollection(Collection $items, callable $resolver, int $limit = 10): array
    {
        return $this->groupCountFromCollection($items, $resolver, $limit);
    }

    protected function topAditivosPercentualFromCollection(Collection $aditivos): array
    {
        $rows = $aditivos
            ->sortByDesc(fn ($item) => (float) $item->percentual_aditivo)
            ->take(10)
            ->map(function ($item) {
                return [
                    'nome' => $item->titulo ?: ('Aditivo #' . $item->id),
                    'total' => (float) $item->percentual_aditivo,
                ];
            })
            ->values();

        return [
            'labels' => $rows->pluck('nome')->values(),
            'values' => $rows->pluck('total')->values(),
        ];
    }

    protected function auditoriaPorTipo(User $user, array $filtros): array
    {
        $processoIds = $this->baseProcessosQuery($user, $filtros)->pluck('id');
        $etapaIds = $this->baseEtapasQuery($user, $filtros)->pluck('id');

        $historicosProcessos = ProcessoHistorico::query()
            ->whereIn('processo_contratacao_id', $processoIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->count();

        $historicosEtapas = ProcessoEtapaHistorico::query()
            ->whereIn('processo_etapa_id', $etapaIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->count();

        $activityLog = DB::table('activity_log')
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->count();

        return [
            'labels' => collect(['Histórico do processo', 'Histórico da etapa', 'Activity log']),
            'values' => collect([(int) $historicosProcessos, (int) $historicosEtapas, (int) $activityLog]),
        ];
    }

    protected function auditoriaTopUsuarios(User $user, array $filtros): array
    {
        $processoIds = $this->baseProcessosQuery($user, $filtros)->pluck('id');
        $etapaIds = $this->baseEtapasQuery($user, $filtros)->pluck('id');

        $processoUsers = ProcessoHistorico::query()
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereIn('processo_contratacao_id', $processoIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->groupBy('user_id')
            ->get();

        $etapaUsers = ProcessoEtapaHistorico::query()
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereIn('processo_etapa_id', $etapaIds)
            ->whereBetween('created_at', [$filtros['data_inicio'], $filtros['data_fim']])
            ->groupBy('user_id')
            ->get();

        $totais = collect()
            ->concat($processoUsers)
            ->concat($etapaUsers)
            ->groupBy('user_id')
            ->map(fn ($items, $userId) => [
                'user_id' => $userId,
                'total' => $items->sum('total'),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values();

        $nomes = User::query()->whereIn('id', $totais->pluck('user_id')->filter())->pluck('name', 'id');

        return [
            'labels' => $totais->map(fn ($row) => $nomes[$row['user_id']] ?? 'Sistema')->values(),
            'values' => $totais->pluck('total')->map(fn ($v) => (int) $v)->values(),
        ];
    }

    protected function filterOptions(): array
    {
        return [
            'contratantes' => Empresa::query()->orderBy('razao_social')->get(['id', 'razao_social']),
            'contratadas' => Empresa::query()->orderBy('razao_social')->get(['id', 'razao_social']),
            'setores' => Setor::query()->where('ativo', true)->orderBy('nome')->get(['id', 'nome']),
            'status_processos' => ProcessoContratacao::query()->select('status')->distinct()->orderBy('status')->pluck('status')->filter()->values(),
            'tipos_contratacao' => ProcessoContratacao::query()->select('tipo_contratacao')->distinct()->orderBy('tipo_contratacao')->pluck('tipo_contratacao')->filter()->values(),
            'categorias' => ProcessoContratacao::query()->select('categoria')->distinct()->orderBy('categoria')->pluck('categoria')->filter()->values(),
        ];
    }

    protected function savedWidgets(User $user, array $metricMap): Collection
    {
        return DashboardWidget::query()
            ->where('ativo', true)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('visivel_para_todos', true);
            })
            ->orderBy('ordem')
            ->get()
            ->map(function ($widget) use ($metricMap) {
                $metric = $metricMap[$widget->metric_key] ?? null;
                if (!$metric) {
                    return null;
                }

                $metric['id'] = $widget->id;
                $metric['titulo'] = $widget->titulo ?: $metric['titulo'];
                $metric['tema'] = $widget->cor ?: ($metric['tema'] ?? 'blue');
                $metric['icone'] = $widget->icone ?: $metric['icone'];

                return $metric;
            })
            ->filter()
            ->values();
    }

    protected function savedFilters(User $user): Collection
    {
        return DashboardSavedFilter::query()
            ->where('ativo', true)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('publico', true);
            })
            ->latest()
            ->get();
    }

    protected function metricCard(string $titulo, $valor, string $descricao, string $tema, string $icone): array
    {
        return [
            'titulo' => $titulo,
            'valor' => (string) $valor,
            'descricao' => $descricao,
            'tema' => $tema,
            'icone' => $icone,
        ];
    }

    protected function currency($value): string
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    protected function percent($value): string
    {
        return number_format((float) $value, 2, ',', '.') . '%';
    }

    protected function movimentacoesPeriodo(Collection $processos): int
    {
        $processoIds = $processos->pluck('id')->filter();
        $etapaIds = $processos->flatMap(fn ($processo) => $processo->etapas->pluck('id'))->filter();

        return ProcessoHistorico::query()->whereIn('processo_contratacao_id', $processoIds)->count()
            + ProcessoEtapaHistorico::query()->whereIn('processo_etapa_id', $etapaIds)->count()
            + DB::table('activity_log')->count();
    }

    protected function countByNormalizedStatus(Collection $items, callable $resolver): array
    {
        return $items
            ->map(fn ($item) => $this->normalizeStatus($resolver($item)))
            ->filter()
            ->countBy()
            ->toArray();
    }

    protected function normalizeStatus(?string $status): string
    {
        if ($status === null) {
            return '';
        }

        $status = Str::upper(Str::ascii(trim($status)));
        $status = preg_replace('/[^A-Z0-9]+/', '_', $status) ?: '';
        return trim($status, '_');
    }

    protected function displayStatus(?string $status): string
    {
        $normalized = $this->normalizeStatus($status);

        return match ($normalized) {
            'EM_ANALISE' => 'Em análise',
            'APROVADO' => 'Aprovado',
            'EM_ANDAMENTO' => 'Em andamento',
            'PENDENTE' => 'Pendente',
            'LIBERADA' => 'Liberada',
            'REPROVADA' => 'Reprovada',
            'CANCELADA' => 'Cancelada',
            'BLOQUEADA' => 'Bloqueada',
            default => $this->displayValue($status),
        };
    }

    protected function displayValue(?string $value): string
    {
        if (blank($value)) {
            return 'Não informado';
        }

        return Str::headline(str_replace('_', ' ', (string) $value));
    }
}
