@extends('adminlte::page')

@section('title', 'Processos de Contratação')

@section('plugins.DataTables', true)
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-file-signature"></i> Processos de Contratação
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-filter mb-4 shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0"><i class="fas fa-filter mr-2"></i> Filtros de Pesquisa</h3>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Nº Processo</label>
                    <input type="text" id="filtro_numero_processo" class="form-control" placeholder="Ex: PC-2026-0001">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Título / Objeto</label>
                    <input type="text" id="filtro_titulo" class="form-control" placeholder="Título do processo">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Status</label>
                    {{-- <select id="filtro_status" class="form-control select2">
                        <option value="">Todos</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select> --}}
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Contratada</label>
                    <select id="filtro_empresa_contratada_id" class="form-control select2">
                        <option value="">Todas</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->razao_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Contratante</label>
                    <select id="filtro_empresa_contratante_id" class="form-control select2">
                        <option value="">Todas</option>
                        {{-- @foreach($contratantes as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->razao_social }}</option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Tipo Contratação</label>
                    <input type="text" id="filtro_tipo_contratacao" class="form-control" placeholder="Ex: Emergencial">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Data Inicial</label>
                    <input type="date" id="filtro_data_inicio" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary">Data Final</label>
                    <input type="date" id="filtro_data_fim" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top d-flex justify-content-end gap-2">
            @can('create processos contratacao')
                <a href="{{ route('processos-contratacao.create') }}" class="btn btn-success px-4 shadow-sm mr-2">
                    <i class="fas fa-plus mr-1"></i> Novo Processo
                </a>
            @endcan
            <button type="button" id="filter" class="btn btn-primary px-4 shadow-sm mr-2">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            <button type="button" id="reset" class="btn btn-outline-secondary px-4 shadow-sm">
                <i class="fas fa-undo mr-1"></i> Limpar
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-folder-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number card-value" id="card-total-processos">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Em Análise</span>
                    <span class="info-box-number card-value" id="card-em-analise">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Aprovados</span>
                    <span class="info-box-number card-value" id="card-aprovados">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Etapas Atrasadas</span>
                    <span class="info-box-number card-value" id="card-etapas-atrasadas">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-primary">
                <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Proposto</span>
                    <span class="info-box-number card-value" id="card-valor-proposto">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-12">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Valor Aprovado</span>
                    <span class="info-box-number card-value" id="card-valor-aprovado">...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-filter mb-4 shadow-lg border-0">
        <div class="card-header bg-secondary text-white"><i class="fas fa-list"></i> Resultados</div>
        <div class="card-body">
            <table id="processos-table" class="table table-hover align-middle text-sm" style="width:100%;">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nº Processo</th>
                        <th>Título</th>
                        <th>Contratante</th>
                        <th>Contratada</th>
                        <th>Status</th>
                        <th>Etapa Atual</th>
                        <th>Valor Proposto</th>
                        <th>Valor Aprovado</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@push('css')
<style>
    .card-filter {
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border: none;
        overflow: hidden;
        background-color: #fff;
    }
    .info-box { min-height: 80px; display:flex; margin-bottom:1rem; padding:.5rem; border-radius:.25rem; box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2); }
    .info-box-icon { border-radius:.25rem; display:flex; align-items:center; justify-content:center; font-size:1.8rem; width:70px; }
    .info-box-content { display:flex; flex-direction:column; justify-content:center; line-height:120%; flex:1; padding:0 10px; }
    .info-box-text { display:block; font-size:12px; text-transform:uppercase; }
    .info-box-number { display:block; font-weight:700; font-size:14px; }
</style>
@endpush

@push('js')
<script>
function updateProcessoCards() {
    let filters = {
        numero_processo_interno: $('#filtro_numero_processo').val(),
        titulo: $('#filtro_titulo').val(),
        status: $('#filtro_status').val(),
        empresa_contratante_id: $('#filtro_empresa_contratante_id').val(),
        empresa_contratada_id: $('#filtro_empresa_contratada_id').val(),
        tipo_contratacao: $('#filtro_tipo_contratacao').val(),
        data_inicio: $('#filtro_data_inicio').val(),
        data_fim: $('#filtro_data_fim').val()
    };

    $('.card-value').text('Carregando...');

    $.ajax({
        url: "{{ route('processos-contratacao.stats') }}",
        method: 'GET',
        data: filters,
        success: function(response) {
            $('#card-total-processos').text(response.total_processos);
            $('#card-em-analise').text(response.em_analise);
            $('#card-aprovados').text(response.aprovados);
            $('#card-etapas-atrasadas').text(response.etapas_atrasadas);
            $('#card-valor-proposto').text(response.valor_proposto);
            $('#card-valor-aprovado').text(response.valor_aprovado);
        },
        error: function() {
            $('.card-value').text('Erro');
        }
    });
}

$(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

    const table = $('#processos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('processos-contratacao.index') }}",
            data: function(d) {
                d.numero_processo_interno = $('#filtro_numero_processo').val();
                d.titulo = $('#filtro_titulo').val();
                d.status = $('#filtro_status').val();
                d.empresa_contratante_id = $('#filtro_empresa_contratante_id').val();
                d.empresa_contratada_id = $('#filtro_empresa_contratada_id').val();
                d.tipo_contratacao = $('#filtro_tipo_contratacao').val();
                d.data_inicio = $('#filtro_data_inicio').val();
                d.data_fim = $('#filtro_data_fim').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'numero_processo_interno', name: 'numero_processo_interno' },
            { data: 'titulo', name: 'titulo' },
            { data: 'empresa_contratante_nome', name: 'empresaContratante.razao_social' },
            { data: 'empresa_contratada_nome', name: 'empresaContratada.razao_social' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'etapa_atual', name: 'etapa_atual', orderable: false, searchable: false },
            { data: 'valor_proposto_formatado', name: 'valor_proposto', searchable: false },
            { data: 'valor_aprovado_formatado', name: 'valor_aprovado_final', searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "text-center" }
        ],
        order: [[0, 'desc']],
        language: { url: '/storage/traducao_datatables_pt_br.json' }
    });

    updateProcessoCards();

    $('#filter').click(function() {
        table.draw();
        updateProcessoCards();
    });

    $('#reset').click(function() {
        $('#filtro_numero_processo, #filtro_titulo, #filtro_tipo_contratacao, #filtro_data_inicio, #filtro_data_fim').val('');
        $('#filtro_status, #filtro_empresa_contratante_id, #filtro_empresa_contratada_id').val('').trigger('change');
        table.draw();
        updateProcessoCards();
    });
});
</script>
@endpush
