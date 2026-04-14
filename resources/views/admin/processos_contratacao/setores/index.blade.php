@extends('adminlte::page')

@section('title', 'Setores')
@section('plugins.Datatables', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-sitemap"></i> Gerenciamento de Setores
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
                @can('create setores')
                    <a href="{{ route('setores.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Novo Setor
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
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
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
            <h3 class="card-title">Lista de Setores</h3>
        </div>
        <div class="card-body">
            <div class="scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver todas as colunas e ações.</div>
            <div class="table-responsive">
                <table id="setores-table" class="table table-bordered table-striped w-100" style="min-width: 860px;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Usuários Vinculados</th>
                            <th>Status</th>
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
    const table = $('#setores-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        scrollX: true,
        ajax: {
            url: "{{ route('setores.data') }}",
            data: function (d) {
                d.ativo = $('#filtro_ativo').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nome', name: 'nome' },
            { data: 'descricao', name: 'descricao' },
            { data: 'usuarios_count', name: 'usuarios_count', searchable: false },
            { data: 'ativo_badge', name: 'ativo', orderable: false, searchable: false },
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
