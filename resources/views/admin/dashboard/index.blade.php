@extends('adminlte::page')

@section('title', 'Dashboard Executivo')

@section('content_header')
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between dashboard-header mb-3">
    <div>
        <h1 class="mb-1">Dashboard executivo</h1>
        <p class="text-muted mb-0">Visão consolidada de empresas, contratos, etapas, aditivos, itens e auditoria.</p>
    </div>
    <div class="mt-3 mt-lg-0 d-flex flex-wrap gap-2">
        @can('create dashboard personalizado')
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalNovoWidget">
                <i class="fas fa-plus mr-1"></i> Novo widget
            </button>
        @endcan
        @can('edit dashboard personalizado')
            <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalSalvarFiltro">
                <i class="fas fa-filter mr-1"></i> Salvar filtro
            </button>
        @endcan
    </div>
</div>
@stop

@section('content')
@include('layouts.notificacoes')

<div class="container-fluid">
    <div class="card dashboard-panel border-0 shadow-sm mb-4">
        <div class="card-body pb-2">
            <form method="GET" action="{{ route('dashboard.index') }}" class="row align-items-end">
                <div class="col-xl col-md-6 form-group">
                    <label>Período inicial</label>
                    <input type="date" name="data_inicio" class="form-control" value="{{ optional($filtros['data_inicio'])->format('Y-m-d') }}">
                </div>
                <div class="col-xl col-md-6 form-group">
                    <label>Período final</label>
                    <input type="date" name="data_fim" class="form-control" value="{{ optional($filtros['data_fim'])->format('Y-m-d') }}">
                </div>
                <div class="col-xl col-md-6 form-group">
                    <label>Contratante</label>
                    <select name="empresa_contratante_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($options['contratantes'] as $empresa)
                            <option value="{{ $empresa->id }}" @selected($filtros['empresa_contratante_id'] == $empresa->id)>{{ $empresa->razao_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl col-md-6 form-group">
                    <label>Contratada</label>
                    <select name="empresa_contratada_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($options['contratadas'] as $empresa)
                            <option value="{{ $empresa->id }}" @selected($filtros['empresa_contratada_id'] == $empresa->id)>{{ $empresa->razao_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl col-md-4 form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        @foreach($options['status_processos'] as $status)
                            <option value="{{ $status }}" @selected($filtros['status'] === $status)>{{ Illuminate\Support\Str::headline(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl col-md-4 form-group">
                    <label>Tipo</label>
                    <select name="tipo_contratacao" class="form-control">
                        <option value="">Todos</option>
                        @foreach($options['tipos_contratacao'] as $tipo)
                            <option value="{{ $tipo }}" @selected($filtros['tipo_contratacao'] === $tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl col-md-4 form-group">
                    <label>Categoria</label>
                    <select name="categoria" class="form-control">
                        <option value="">Todas</option>
                        @foreach($options['categorias'] as $categoria)
                            <option value="{{ $categoria }}" @selected($filtros['categoria'] === $categoria)>{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl col-md-6 form-group">
                    <label>Setor</label>
                    <select name="setor_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach($options['setores'] as $setor)
                            <option value="{{ $setor->id }}" @selected($filtros['setor_id'] == $setor->id)>{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-auto col-md-6 form-group d-flex flex-wrap gap-2">
                    <button class="btn btn-primary px-4">
                        <i class="fas fa-search mr-1"></i> Aplicar
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-rotate-left mr-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        @foreach($resumo as $item)
            <div class="col-lg-4 mb-3">
                <div class="dashboard-summary dashboard-theme-{{ $item['tema'] }}">
                    <div class="dashboard-summary__icon"><i class="{{ $item['icone'] }}"></i></div>
                    <div>
                        <div class="dashboard-summary__label">{{ $item['titulo'] }}</div>
                        <div class="dashboard-summary__value">{{ $item['valor'] }}</div>
                        <div class="dashboard-summary__meta">{{ $item['descricao'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @php($tabs = ['geral' => 'Geral', 'empresas' => 'Empresas', 'contratos' => 'Contratos', 'etapas' => 'Etapas', 'aditivos' => 'Aditivos', 'itens' => 'Itens', 'alertas' => 'Alertas', 'auditoria' => 'Auditoria', 'widgets' => 'Personalizado'])
    <ul class="nav nav-pills dashboard-tabs mb-4" role="tablist">
        @foreach($tabs as $tabKey => $tabLabel)
            <li class="nav-item mr-2 mb-2">
                <a class="nav-link @if($loop->first) active @endif" id="{{ $tabKey }}-tab" data-toggle="pill" href="#{{ $tabKey }}" role="tab">{{ $tabLabel }}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="geral" role="tabpanel">
            <div class="row">
                @foreach($cards['geral'] as $card)
                    <div class="col-xl-3 col-md-6 mb-3">@include('admin.dashboard.partials.card', ['card' => $card])</div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-xl-8 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosMes', 'title' => 'Evolução mensal de processos', 'subtitle' => 'Com base na data de criação dos processos'])</div>
                <div class="col-xl-4 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosStatus', 'title' => 'Processos por status'])</div>
                <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosCategoria', 'title' => 'Processos por categoria'])</div>
                <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartRankingContratadas', 'title' => 'Top empresas contratadas'])</div>
                <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosTipo', 'title' => 'Processos por tipo de contratação'])</div>
                <div class="col-xl-6 mb-4">
                    <div class="card dashboard-panel shadow-sm border-0 h-100">
                        <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bold mb-0">Últimos eventos</h3>
                            <span class="badge badge-light border">Timeline</span>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($auditoria['timeline']->take(8) as $evento)
                                    <li class="list-group-item px-4 py-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <strong>{{ $evento['titulo'] }}</strong>
                                            <small class="text-muted">{{ $evento['data'] }}</small>
                                        </div>
                                        <div class="text-muted small mb-1">{{ $evento['fonte'] }} · {{ $evento['usuario'] ?: 'Sistema' }}</div>
                                        <div>{{ $evento['descricao'] }}</div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted px-4 py-4">Nenhum evento encontrado.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach(['empresas','contratos','etapas','aditivos','itens','auditoria'] as $aba)
            <div class="tab-pane fade" id="{{ $aba }}" role="tabpanel">
                <div class="row">
                    @foreach($cards[$aba] as $card)
                        <div class="col-xl-3 col-md-6 mb-3">@include('admin.dashboard.partials.card', ['card' => $card])</div>
                    @endforeach
                </div>

                @if($aba === 'empresas')
                    <div class="row">
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartEmpresasTipo', 'title' => 'Empresas por tipo'])</div>
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartEmpresasUf', 'title' => 'Empresas por UF'])</div>
                    </div>
                @elseif($aba === 'contratos')
                    <div class="row">
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosStatus2', 'title' => 'Funil por status'])</div>
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartProcessosMes2', 'title' => 'Criação mensal de contratos'])</div>
                    </div>
                @elseif($aba === 'etapas')
                    <div class="row">
                        <div class="col-xl-4 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartEtapasStatus', 'title' => 'Etapas por status'])</div>
                        <div class="col-xl-4 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartEtapasSetor', 'title' => 'Etapas por setor'])</div>
                        <div class="col-xl-4 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartEtapasOrdem', 'title' => 'Etapas por ordem'])</div>
                        <div class="col-12 mb-4">
                            <div class="card dashboard-panel shadow-sm border-0">
                                <div class="card-header border-0 bg-white">
                                    <h3 class="card-title font-weight-bold mb-0">Etapas atrasadas</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Etapa</th>
                                                <th>Processo</th>
                                                <th>Data limite</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($alertas['criticos']['Etapas atrasadas'] as $etapa)
                                                <tr>
                                                    <td>{{ $etapa->nome_etapa }}</td>
                                                    <td>{{ optional($etapa->processo)->titulo }}</td>
                                                    <td>{{ optional($etapa->data_limite)->format('d/m/Y') }}</td>
                                                    <td><span class="badge badge-danger">Atrasada</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center text-muted py-4">Nenhuma etapa atrasada no filtro atual.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($aba === 'aditivos')
                    <div class="row">
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartAditivosTipo', 'title' => 'Aditivos por tipo'])</div>
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartAditivosPercentual', 'title' => 'Top aditivos por percentual'])</div>
                    </div>
                @elseif($aba === 'itens')
                    <div class="row">
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartItensGrupo', 'title' => 'Itens por grupo'])</div>
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartItensSubgrupo', 'title' => 'Itens por subgrupo'])</div>
                    </div>
                @elseif($aba === 'auditoria')
                    <div class="row">
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartAuditoriaTipo', 'title' => 'Movimentações por origem'])</div>
                        <div class="col-xl-6 mb-4">@include('admin.dashboard.partials.chart-card', ['id' => 'chartAuditoriaUsuarios', 'title' => 'Usuários com mais registros'])</div>
                        <div class="col-12 mb-4">
                            <div class="card dashboard-panel shadow-sm border-0">
                                <div class="card-header border-0 bg-white">
                                    <h3 class="card-title font-weight-bold mb-0">Timeline global</h3>
                                </div>
                                <div class="card-body">
                                    <div class="timeline timeline-inverse">
                                        @forelse($auditoria['timeline'] as $evento)
                                            <div class="time-label">
                                                <span class="bg-primary">{{ explode(' ', $evento['data'])[0] }}</span>
                                            </div>
                                            <div>
                                                <i class="fas fa-history bg-info"></i>
                                                <div class="timeline-item">
                                                    <span class="time"><i class="far fa-clock"></i> {{ explode(' ', $evento['data'])[1] ?? '' }}</span>
                                                    <h3 class="timeline-header">{{ $evento['titulo'] }}</h3>
                                                    <div class="timeline-body">
                                                        {{ $evento['descricao'] }}<br>
                                                        <small class="text-muted">{{ $evento['fonte'] }} · {{ $evento['usuario'] ?: 'Sistema' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted mb-0">Nenhum evento encontrado.</p>
                                        @endforelse
                                        <div><i class="far fa-clock bg-gray"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="tab-pane fade" id="alertas" role="tabpanel">
            <div class="row">
                @foreach($alertas as $nivel => $grupos)
                    <div class="col-xl-4 mb-4">
                        <div class="card dashboard-panel shadow-sm border-0 h-100">
                            <div class="card-header border-0 text-white {{ $nivel === 'criticos' ? 'bg-danger' : ($nivel === 'atencao' ? 'bg-warning' : 'bg-info') }}">
                                <h3 class="card-title font-weight-bold mb-0 text-white">{{ strtoupper($nivel) }}</h3>
                            </div>
                            <div class="card-body p-0">
                                @foreach($grupos as $titulo => $itens)
                                    <div class="border-bottom px-4 py-3">
                                        <div class="font-weight-bold mb-2">{{ $titulo }}</div>
                                        <ul class="mb-0 pl-3">
                                            @forelse($itens as $item)
                                                <li class="mb-1">
                                                    @if(isset($item->titulo) && filled($item->titulo))
                                                        {{ $item->titulo }}
                                                    @elseif(isset($item->razao_social) && filled($item->razao_social))
                                                        {{ $item->razao_social }}
                                                    @else
                                                        {{ $item->nome_etapa ?? 'Registro' }}
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="text-muted">Nenhum registro.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="tab-pane fade" id="widgets" role="tabpanel">
            <div class="row">
                @forelse($widgets_salvos as $widget)
                    <div class="col-xl-3 col-md-6 mb-3">
                        @include('admin.dashboard.partials.card', ['card' => $widget])
                        @can('delete dashboard personalizado')
                            <form action="{{ route('dashboard.widgets.destroy', $widget['id']) }}" method="POST" class="mt-2 text-right">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remover</button>
                            </form>
                        @endcan
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border shadow-sm">Nenhum widget salvo ainda. Use o botão <strong>Novo widget</strong> para montar seu painel personalizado.</div>
                    </div>
                @endforelse
            </div>

            <div class="row mt-2">
                <div class="col-xl-6 mb-4">
                    <div class="card dashboard-panel shadow-sm border-0 h-100">
                        <div class="card-header border-0 bg-white">
                            <h3 class="card-title font-weight-bold mb-0">Filtros salvos</h3>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr><th>Nome</th><th>Escopo</th><th class="text-right">Ações</th></tr>
                                </thead>
                                <tbody>
                                @forelse($filtros_salvos as $filtroSalvo)
                                    <tr>
                                        <td>{{ $filtroSalvo->nome }}</td>
                                        <td>{{ $filtroSalvo->publico ? 'Público' : 'Privado' }}</td>
                                        <td class="text-right">
                                            <a class="btn btn-xs btn-outline-primary" href="{{ route('dashboard.index', $filtroSalvo->filtros_json ?? []) }}">Aplicar</a>
                                            @can('delete dashboard personalizado')
                                                <form action="{{ route('dashboard.filters.destroy', $filtroSalvo) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-xs btn-outline-danger">Excluir</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Nenhum filtro salvo.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 mb-4">
                    <div class="card dashboard-panel shadow-sm border-0 h-100">
                        <div class="card-header border-0 bg-white">
                            <h3 class="card-title font-weight-bold mb-0">Salvar layout rápido</h3>
                        </div>
                        <div class="card-body">
                            @can('edit dashboard personalizado')
                                <form action="{{ route('dashboard.layouts.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label>Nome do layout</label>
                                        <input type="text" name="nome" class="form-control" placeholder="Ex: Diretoria - visão mensal" required>
                                    </div>
                                    <input type="hidden" name="filtros_json[data_inicio]" value="{{ optional($filtros['data_inicio'])->format('Y-m-d') }}">
                                    <input type="hidden" name="filtros_json[data_fim]" value="{{ optional($filtros['data_fim'])->format('Y-m-d') }}">
                                    <input type="hidden" name="filtros_json[empresa_contratante_id]" value="{{ $filtros['empresa_contratante_id'] }}">
                                    <input type="hidden" name="filtros_json[empresa_contratada_id]" value="{{ $filtros['empresa_contratada_id'] }}">
                                    <input type="hidden" name="filtros_json[status]" value="{{ $filtros['status'] }}">
                                    <input type="hidden" name="filtros_json[tipo_contratacao]" value="{{ $filtros['tipo_contratacao'] }}">
                                    <input type="hidden" name="filtros_json[categoria]" value="{{ $filtros['categoria'] }}">
                                    <input type="hidden" name="filtros_json[setor_id]" value="{{ $filtros['setor_id'] }}">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="padrao" value="1" id="layoutPadrao">
                                        <label class="form-check-label" for="layoutPadrao">Definir como layout padrão</label>
                                    </div>
                                    <button class="btn btn-primary">Salvar layout</button>
                                </form>
                            @else
                                <p class="text-muted mb-0">Seu perfil não possui permissão para salvar layouts.</p>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('create dashboard personalizado')
<div class="modal fade" id="modalNovoWidget" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('dashboard.widgets.store') }}">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Novo widget</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Título</label>
                    <input type="text" class="form-control" name="titulo" required>
                </div>
                <div class="form-group">
                    <label>Métrica</label>
                    <select class="form-control" name="metric_key" required>
                        @foreach($widgets_disponiveis as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Tema</label>
                        <select class="form-control" name="cor">
                            @foreach(['blue','emerald','amber','red','violet','slate','indigo','pink','teal','orange','cyan','purple'] as $cor)
                                <option value="{{ $cor }}">{{ ucfirst($cor) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Ícone</label>
                        <input type="text" class="form-control" name="icone" value="fas fa-chart-pie">
                    </div>
                </div>
                @can('share dashboard personalizado')
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="visivel_para_todos" value="1" id="visivelParaTodos">
                    <label class="form-check-label" for="visivelParaTodos">Compartilhar com outros usuários</label>
                </div>
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Salvar widget</button>
            </div>
        </form>
    </div>
</div>
@endcan

@can('edit dashboard personalizado')
<div class="modal fade" id="modalSalvarFiltro" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('dashboard.filters.store') }}">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Salvar filtro atual</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nome do filtro</label>
                    <input type="text" class="form-control" name="nome" required>
                </div>
                @foreach(['data_inicio','data_fim','empresa_contratante_id','empresa_contratada_id','status','tipo_contratacao','categoria','setor_id'] as $campo)
                    <input type="hidden" name="{{ $campo }}" value="{{ is_object($filtros[$campo] ?? null) ? optional($filtros[$campo])->format('Y-m-d') : ($filtros[$campo] ?? '') }}">
                @endforeach
                @can('share dashboard personalizado')
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="publico" value="1" id="filtroPublico">
                        <label class="form-check-label" for="filtroPublico">Disponibilizar para outros usuários</label>
                    </div>
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Salvar filtro</button>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection

@push('css')
<style>
.dashboard-header h1{font-size:1.8rem;font-weight:700;}
.dashboard-panel,.dashboard-kpi,.dashboard-summary{border-radius:18px;}
.dashboard-tabs .nav-link{border-radius:999px;padding:.7rem 1rem;font-weight:600;background:#fff;color:#556070;border:1px solid #e8ecf3;}
.dashboard-tabs .nav-link.active{background:#1f4fd6;border-color:#1f4fd6;box-shadow:0 10px 25px rgba(31,79,214,.18);}
.dashboard-kpi{position:relative;overflow:hidden;background:#fff;border:1px solid #ebeff5;padding:1.15rem 1.1rem;display:flex;gap:1rem;min-height:150px;box-shadow:0 10px 30px rgba(16,24,40,.06);}
.dashboard-kpi__icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;}
.dashboard-kpi__body{min-width:0;}
.dashboard-kpi__label{font-size:.88rem;font-weight:700;color:#4c5665;text-transform:uppercase;letter-spacing:.03em;}
.dashboard-kpi__value{font-size:1.55rem;font-weight:800;color:#162033;line-height:1.15;margin:.55rem 0 .35rem;word-break:break-word;}
.dashboard-kpi__meta{font-size:.86rem;color:#6b7484;line-height:1.4;}
.dashboard-summary{display:flex;gap:1rem;align-items:center;padding:1.15rem 1.2rem;background:#fff;border:1px solid #ebeff5;box-shadow:0 10px 30px rgba(16,24,40,.06);}
.dashboard-summary__icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;}
.dashboard-summary__label{font-size:.85rem;text-transform:uppercase;font-weight:700;color:#526070;letter-spacing:.03em;}
.dashboard-summary__value{font-size:1.45rem;font-weight:800;color:#152132;line-height:1.2;margin:.3rem 0;}
.dashboard-summary__meta{font-size:.88rem;color:#6b7484;}
.dashboard-theme-blue .dashboard-kpi__icon,.dashboard-theme-blue.dashboard-summary .dashboard-summary__icon{background:rgba(37,99,235,.12);color:#2563eb;}
.dashboard-theme-emerald .dashboard-kpi__icon,.dashboard-theme-emerald.dashboard-summary .dashboard-summary__icon{background:rgba(16,185,129,.12);color:#059669;}
.dashboard-theme-amber .dashboard-kpi__icon,.dashboard-theme-amber.dashboard-summary .dashboard-summary__icon{background:rgba(245,158,11,.12);color:#d97706;}
.dashboard-theme-red .dashboard-kpi__icon,.dashboard-theme-red.dashboard-summary .dashboard-summary__icon{background:rgba(239,68,68,.12);color:#dc2626;}
.dashboard-theme-violet .dashboard-kpi__icon,.dashboard-theme-violet.dashboard-summary .dashboard-summary__icon{background:rgba(139,92,246,.12);color:#7c3aed;}
.dashboard-theme-slate .dashboard-kpi__icon,.dashboard-theme-slate.dashboard-summary .dashboard-summary__icon{background:rgba(71,85,105,.12);color:#334155;}
.dashboard-theme-indigo .dashboard-kpi__icon,.dashboard-theme-indigo.dashboard-summary .dashboard-summary__icon{background:rgba(79,70,229,.12);color:#4338ca;}
.dashboard-theme-pink .dashboard-kpi__icon,.dashboard-theme-pink.dashboard-summary .dashboard-summary__icon{background:rgba(236,72,153,.12);color:#db2777;}
.dashboard-theme-teal .dashboard-kpi__icon,.dashboard-theme-teal.dashboard-summary .dashboard-summary__icon{background:rgba(20,184,166,.12);color:#0f766e;}
.dashboard-theme-orange .dashboard-kpi__icon,.dashboard-theme-orange.dashboard-summary .dashboard-summary__icon{background:rgba(249,115,22,.12);color:#ea580c;}
.dashboard-theme-cyan .dashboard-kpi__icon,.dashboard-theme-cyan.dashboard-summary .dashboard-summary__icon{background:rgba(6,182,212,.12);color:#0891b2;}
.dashboard-theme-purple .dashboard-kpi__icon,.dashboard-theme-purple.dashboard-summary .dashboard-summary__icon{background:rgba(147,51,234,.12);color:#9333ea;}
.chart-empty-state{position:absolute;inset:1rem;display:flex;align-items:center;justify-content:center;border:1px dashed #dbe2ea;border-radius:14px;background:#f8fafc;color:#7b8796;font-weight:600;z-index:2;}
.timeline>div>.timeline-item{border-radius:14px;box-shadow:0 8px 20px rgba(15,23,42,.08);border:1px solid #edf1f7;}
.gap-2{gap:.5rem;}
@media (max-width: 767.98px){
    .dashboard-kpi,.dashboard-summary{min-height:auto;}
    .dashboard-kpi__value,.dashboard-summary__value{font-size:1.25rem;}
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const dashboardCharts = @json($charts);

function toggleEmptyState(id, hasData) {
    const canvas = document.getElementById(id);
    const empty = document.getElementById(id + 'Empty');
    if (!canvas || !empty) return;
    empty.classList.toggle('d-none', hasData);
    canvas.style.opacity = hasData ? '1' : '.15';
}

function makeChart(id, type, sourceKey, horizontal = false) {
    const el = document.getElementById(id);
    const source = dashboardCharts[sourceKey] || {labels: [], values: []};
    if (!el) return;

    const hasData = Array.isArray(source.values) && source.values.some(value => Number(value) > 0);
    toggleEmptyState(id, hasData);
    if (!hasData) return;

    new Chart(el, {
        type: type,
        data: {
            labels: source.labels,
            datasets: [{
                label: 'Total',
                data: source.values,
                borderWidth: 1,
                borderRadius: 8,
                tension: .35,
                fill: type === 'line',
            }]
        },
        options: {
            indexAxis: horizontal ? 'y' : 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: type !== 'bar' && type !== 'line' },
            },
            scales: type === 'doughnut' || type === 'pie' ? {} : {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { autoSkip: false, maxRotation: horizontal ? 0 : 35, minRotation: horizontal ? 0 : 0 } }
            }
        }
    });
}

$(function () {
    makeChart('chartProcessosMes', 'line', 'processos_por_mes');
    makeChart('chartProcessosStatus', 'doughnut', 'processos_por_status');
    makeChart('chartProcessosCategoria', 'bar', 'processos_por_categoria');
    makeChart('chartProcessosTipo', 'bar', 'processos_por_tipo');
    makeChart('chartRankingContratadas', 'bar', 'ranking_contratadas', true);
    makeChart('chartEmpresasTipo', 'pie', 'empresas_por_tipo');
    makeChart('chartEmpresasUf', 'bar', 'empresas_por_uf');
    makeChart('chartProcessosStatus2', 'bar', 'processos_por_status');
    makeChart('chartProcessosMes2', 'line', 'processos_por_mes');
    makeChart('chartEtapasStatus', 'doughnut', 'etapas_por_status');
    makeChart('chartEtapasSetor', 'bar', 'etapas_por_setor', true);
    makeChart('chartEtapasOrdem', 'line', 'etapas_por_ordem');
    makeChart('chartAditivosTipo', 'pie', 'aditivos_por_tipo');
    makeChart('chartAditivosPercentual', 'bar', 'aditivos_top_percentual', true);
    makeChart('chartItensGrupo', 'bar', 'itens_por_grupo', true);
    makeChart('chartItensSubgrupo', 'bar', 'itens_por_subgrupo', true);
    makeChart('chartAuditoriaTipo', 'doughnut', 'auditoria_por_tipo');
    makeChart('chartAuditoriaUsuarios', 'bar', 'auditoria_top_usuarios', true);
});
</script>
@endpush
