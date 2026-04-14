@extends('adminlte::page')

@section('title', 'Empresas')

@section('plugins.DataTables', true)
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-building"></i> Cadastro de Empresas
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-filter mb-4 shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
                <i class="fas fa-filter mr-2"></i> Filtros de Pesquisa
            </h3>
        </div>
        <div class="card-body bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="filtro_razao_social" class="form-label fw-bold text-secondary">Razão Social</label>
                    <input type="text" id="filtro_razao_social" class="form-control" placeholder="Digite a razão social">
                </div>
                <div class="col-md-3">
                    <label for="filtro_cnpj" class="form-label fw-bold text-secondary">CNPJ</label>
                    <input type="text" id="filtro_cnpj" class="form-control" placeholder="00.000.000/0000-00">
                </div>
                <div class="col-md-3">
                    <label for="filtro_tipo_empresa" class="form-label fw-bold text-secondary">Tipo</label>
                    <select id="filtro_tipo_empresa" class="form-control select2">
                        <option value="">Todos</option>
                        <option value="CONTRATANTE">Contratante</option>
                        <option value="CONTRATADA">Contratada</option>
                        <option value="AMBAS">Ambas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro_ativo" class="form-label fw-bold text-secondary">Status</label>
                    <select id="filtro_ativo" class="form-control select2">
                        <option value="">Todos</option>
                        <option value="1">Ativa</option>
                        <option value="0">Inativa</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-top d-flex justify-content-end gap-2">
            @can('create empresas')
                <a href="{{ route('empresas.create') }}" class="btn btn-success px-4 shadow-sm mr-2">
                    <i class="fas fa-plus mr-1"></i> Nova Empresa
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
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-building"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Empresas</span>
                    <span class="info-box-number card-value" id="card-total-empresas">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ativas</span>
                    <span class="info-box-number card-value" id="card-ativas">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-warning">
                <span class="info-box-icon"><i class="fas fa-file-signature"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Com Processos</span>
                    <span class="info-box-number card-value" id="card-com-processos">...</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-secondary">
                <span class="info-box-icon"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Contratadas</span>
                    <span class="info-box-number card-value" id="card-contratadas">...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-filter mb-4 shadow-lg border-0">
        <div class="card-header bg-secondary text-white">
            <i class="fas fa-list"></i> Resultados
        </div>
        <div class="card-body">
            <div class="scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver todas as colunas e ações.</div>
            <div class="table-responsive">
            <table id="empresas-table" class="table table-hover align-middle text-sm w-100" style="width:100%; min-width: 980px;">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Razão Social</th>
                        <th>Nome Fantasia</th>
                        <th>CNPJ</th>
                        <th>Tipo</th>
                        <th>Cidade/UF</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
            </div>
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
    .filter-select,
    .form-control,
    .select2-container .select2-selection--single {
        height: 42px !important;
        border-radius: 8px !important;
        border: 1px solid #ccc !important;
        background-color: #fff !important;
    }
    .info-box { min-height: 80px; display:flex; margin-bottom:1rem; padding:.5rem; border-radius:.25rem; box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2); }
    .info-box-icon { border-radius:.25rem; display:flex; align-items:center; justify-content:center; font-size:1.8rem; width:70px; }
    .info-box-content { display:flex; flex-direction:column; justify-content:center; line-height:120%; flex:1; padding:0 10px; }
    .info-box-text { display:block; font-size:12px; text-transform:uppercase; }
    .info-box-number { display:block; font-weight:700; font-size:14px; }
    @media (max-width: 767.98px) {
        .card-footer { justify-content: stretch !important; }
        .card-footer .btn { width: 100%; margin-right: 0 !important; }
    }
</style>
@endpush

@push('js')
<script>
    function updateEmpresaCards() {
        let filters = {
            razao_social: $('#filtro_razao_social').val(),
            cnpj: $('#filtro_cnpj').val(),
            tipo_empresa: $('#filtro_tipo_empresa').val(),
            ativo: $('#filtro_ativo').val()
        };

        $('.card-value').text('Carregando...');

        $.ajax({
            url: "{{ route('empresas.stats') }}",
            method: 'GET',
            data: filters,
            success: function(response) {
                $('#card-total-empresas').text(response.total_empresas);
                $('#card-ativas').text(response.ativas);
                $('#card-com-processos').text(response.com_processos);
                $('#card-contratadas').text(response.contratadas);
            },
            error: function() {
                $('.card-value').text('Erro');
            }
        });
    }

    $(function() {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

        const table = $('#empresas-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            autoWidth: false,
            scrollX: true,
            ajax: {
                url: "{{ route('empresas.data') }}",
                data: function(d) {
                    d.razao_social = $('#filtro_razao_social').val();
                    d.cnpj = $('#filtro_cnpj').val();
                    d.tipo_empresa = $('#filtro_tipo_empresa').val();
                    d.ativo = $('#filtro_ativo').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'razao_social', name: 'razao_social' },
                { data: 'nome_fantasia', name: 'nome_fantasia' },
                { data: 'cnpj', name: 'cnpj' },
                { data: 'tipo_empresa', name: 'tipo_empresa' },
                { data: 'cidade_uf', name: 'cidade_uf', orderable: false, searchable: false },
                { data: 'ativo_badge', name: 'ativo', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: "text-center" }
            ],
            order: [[1, 'asc']],
            language: { url: '/storage/traducao_datatables_pt_br.json' },
            drawCallback: function() {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        updateEmpresaCards();

        $('#filter').click(function() {
            table.draw();
            updateEmpresaCards();
        });

        $('#reset').click(function() {
            $('#filtro_razao_social').val('');
            $('#filtro_cnpj').val('');
            $('#filtro_tipo_empresa').val('').trigger('change');
            $('#filtro_ativo').val('').trigger('change');
            table.draw();
            updateEmpresaCards();
        });
    });
</script>
@endpush
