@extends('adminlte::page')

@section('title', 'Etapas Padrão')
@section('plugins.Datatables', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-list-ol"></i> Gerenciamento de Etapas Padrão
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <h3 class="card-title">Filtros</h3>
            <div class="card-tools">
                @can('create etapas padrao')
                    <a href="{{ route('etapas-padrao.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nova Etapa
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Status</label>
                    <select id="filtro_ativo" class="form-control">
                        <option value="">Todos</option>
                        <option value="1">Ativas</option>
                        <option value="0">Inativas</option>
                    </select>
                </div>
                <div class="col-md-9 d-flex align-items-end justify-content-end">
                    <button id="btn-filtrar" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <button id="btn-limpar" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">Lista de Etapas Padrão</h3>
        </div>
        <div class="card-body">
            <div class="scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver todas as colunas e ações.</div>
            <div class="table-responsive">
                <table id="etapas-padrao-table" class="table table-bordered table-striped w-100" style="min-width: 980px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ordem</th>
                            <th>Nome</th>
                            <th>Setor</th>
                            <th>SLA</th>
                            <th>Obrigatória</th>
                            <th>Status</th>
                            <th>Cor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
$(function () {
    const table = $('#etapas-padrao-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        scrollX: true,
        ajax: {
            url: "{{ route('etapas-padrao.data') }}",
            data: function (d) {
                d.ativo = $('#filtro_ativo').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'ordem', name: 'ordem' },
            { data: 'nome', name: 'nome' },
            { data: 'setor_responsavel', name: 'setor_responsavel' },
            { data: 'prazo_limite_dias', name: 'prazo_limite_dias' },
            { data: 'obrigatoria_badge', name: 'obrigatoria', orderable: false, searchable: false },
            { data: 'ativo_badge', name: 'ativo', orderable: false, searchable: false },
            { data: 'cor_badge', name: 'cor_badge' },
            { data: 'acoes', name: 'acoes', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'asc']],
        language: {
            url: '/storage/traducao_datatables_pt_br.json'
        }
    });

    $('#btn-filtrar').on('click', function () {
        table.draw();
    });

    $('#btn-limpar').on('click', function () {
        $('#filtro_ativo').val('');
        table.draw();
    });
});
</script>
@endpush
