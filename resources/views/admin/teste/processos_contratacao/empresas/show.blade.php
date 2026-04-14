@extends('adminlte::page')

@section('title', 'Visualizar Empresa')
@section('plugins.DataTables', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-building"></i> Empresa: {{ $empresa->razao_social }}
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-outline card-primary mb-3">
                <div class="card-header"><h3 class="card-title">Dados Gerais</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><strong>Razão Social:</strong><br>{{ $empresa->razao_social }}</div>
                        <div class="col-md-3"><strong>Nome Fantasia:</strong><br>{{ $empresa->nome_fantasia ?: '-' }}</div>
                        <div class="col-md-3"><strong>CNPJ:</strong><br>{{ $empresa->cnpj }}</div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3"><strong>Tipo:</strong><br>{{ $empresa->tipo_empresa }}</div>
                        <div class="col-md-3"><strong>Email:</strong><br>{{ $empresa->email ?: '-' }}</div>
                        <div class="col-md-3"><strong>Telefone:</strong><br>{{ $empresa->telefone ?: '-' }}</div>
                        <div class="col-md-3"><strong>Celular:</strong><br>{{ $empresa->celular ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-info mb-3">
                <div class="card-header"><h3 class="card-title">Endereço e Contato</h3></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Contato Principal:</strong> {{ $empresa->contato_principal ?: '-' }}</p>
                    <p class="mb-1"><strong>Cargo:</strong> {{ $empresa->cargo_contato ?: '-' }}</p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ trim(($empresa->endereco ?? '') . ', ' . ($empresa->numero ?? '')) ?: '-' }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $empresa->bairro ?: '-' }}</p>
                    <p class="mb-0"><strong>Cidade/UF:</strong> {{ ($empresa->cidade ?: '-') . '/' . ($empresa->uf ?: '-') }}</p>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title">Observações</h3></div>
                <div class="card-body">
                    {!! nl2br(e($empresa->observacoes ?: 'Nenhuma observação cadastrada.')) !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-success mb-3">
                <div class="card-header"><h3 class="card-title">Dados Bancários</h3></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Banco:</strong> {{ $empresa->banco ?: '-' }}</p>
                    <p class="mb-1"><strong>Agência:</strong> {{ $empresa->agencia ?: '-' }}</p>
                    <p class="mb-1"><strong>Conta:</strong> {{ $empresa->conta ?: '-' }}</p>
                    <p class="mb-0"><strong>PIX:</strong> {{ $empresa->chave_pix ?: '-' }}</p>
                </div>
            </div>

            <div class="card card-outline card-warning">
                <div class="card-header"><h3 class="card-title">Ações</h3></div>
                <div class="card-body">
                    @can('edit empresas')
                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit mr-1"></i> Editar Empresa
                        </a>
                    @endcan
                    @can('create processos contratacao')
                        <a href="{{ route('processos-contratacao.create', ['empresa_contratada_id' => $empresa->id]) }}" class="btn btn-success btn-block">
                            <i class="fas fa-file-signature mr-1"></i> Novo Processo
                        </a>
                    @endcan
                    <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary mt-4">
        <div class="card-header">
            <h3 class="card-title">Processos vinculados</h3>
        </div>
        <div class="card-body">
            <table id="processos-empresa-table" class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nº Processo</th>
                        <th>Título</th>
                        <th>Status</th>
                        <th>Valor Proposto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@push('js')
<script>
$(function() {
    $('#processos-empresa-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('processos-contratacao.index') }}",
            data: function(d) {
                d.empresa_contratada_id = "{{ $empresa->id }}";
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'numero_processo_interno', name: 'numero_processo_interno' },
            { data: 'titulo', name: 'titulo' },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'valor_proposto_formatado', name: 'valor_proposto', searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: { url: '/storage/traducao_datatables_pt_br.json' }
    });
});
</script>
@endpush
