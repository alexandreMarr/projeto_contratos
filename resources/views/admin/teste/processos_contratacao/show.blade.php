@extends('adminlte::page')

@section('title', 'Detalhes do Processo')
@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)

@section('content_header')
    <h1>
        Processo de Contratação - {{ $processo->numero_processo_interno }}
        <small class="float-right">{{ $processo->titulo }}</small>
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('admin.processos_contratacao._resumo_cards')

    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="abas-processo" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-dados-link" data-toggle="pill" href="#tab-dados" role="tab">
                        <i class="fa fa-info-circle"></i> Dados Gerais
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-etapas-link" data-toggle="pill" href="#tab-etapas" role="tab">
                        <i class="fa fa-stream"></i> Etapas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-anexos-link" data-toggle="pill" href="#tab-anexos" role="tab">
                        <i class="fa fa-paperclip"></i> Anexos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-itens-link" data-toggle="pill" href="#tab-itens" role="tab">
                        <i class="fa fa-list"></i> Itens
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-aditivos-link" data-toggle="pill" href="#tab-aditivos" role="tab">
                        <i class="fa fa-file-medical"></i> Aditivos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-historico-link" data-toggle="pill" href="#tab-historico" role="tab">
                        <i class="fa fa-history"></i> Histórico
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="abas-processo-content">
                <div class="tab-pane fade show active" id="tab-dados" role="tabpanel">
                    @include('admin.processos_contratacao._tab_dados_gerais')
                </div>
                <div class="tab-pane fade" id="tab-etapas" role="tabpanel">
                    @include('admin.processos_contratacao._tab_etapas')
                </div>
                <div class="tab-pane fade" id="tab-anexos" role="tabpanel">
                    @include('admin.processos_contratacao._tab_anexos')
                </div>
                <div class="tab-pane fade" id="tab-itens" role="tabpanel">
                    @include('admin.processos_contratacao._tab_itens')
                </div>
                <div class="tab-pane fade" id="tab-aditivos" role="tabpanel">
                    @include('admin.processos_contratacao._tab_aditivos')
                </div>
                <div class="tab-pane fade" id="tab-historico" role="tabpanel">
                    @include('admin.processos_contratacao._tab_historico')
                </div>
            </div>
        </div>
    </div>

    @include('admin.processos_contratacao._modal_editar_etapa')
    @include('admin.processos_contratacao._modal_nova_etapa_extra')
</div>
@stop

@push('css')
<style>
    .info-box { min-height: 80px; display:flex; margin-bottom:1rem; padding:.5rem; border-radius:.25rem; box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2); }
    .info-box-icon { border-radius:.25rem; display:flex; align-items:center; justify-content:center; font-size:1.8rem; width:70px; }
    .info-box-content { display:flex; flex-direction:column; justify-content:center; line-height:120%; flex:1; padding:0 10px; }
    .info-box-text { display:block; font-size:12px; text-transform:uppercase; }
    .info-box-number { display:block; font-weight:700; font-size:14px; }
</style>
@endpush

@push('js')
<script>
$(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

    $(document).on('click', '.btn-editar-etapa', function() {
        $('#etapa-edit-titulo').text($(this).data('nome'));
        $('#etapa-edit-url').val($(this).data('url'));
        $('#etapa-status').val($(this).data('status'));
        $('#etapa-observacoes').val($(this).data('observacoes'));
        $('#etapa-data-limite').val($(this).data('data_limite'));
        $('#etapa-data-conclusao').val($(this).data('data_conclusao'));
        $('#etapa-alert-container').empty();
        $('#modalEditarEtapa').modal('show');
    });

    $('#btn-salvar-etapa').on('click', function() {
        const btn = $(this);
        const url = $('#etapa-edit-url').val();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Salvando...');

        $.ajax({
            url: url,
            type: 'POST',
            data: $('#form-editar-etapa').serialize(),
            success: function(resp) {
                $('#modalEditarEtapa').modal('hide');
                Swal.fire('Sucesso!', resp.message || 'Etapa atualizada com sucesso.', 'success')
                    .then(() => window.location.reload());
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Erro ao salvar etapa.';
                $('#etapa-alert-container').html(`<div class="alert alert-danger">${msg}</div>`);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Salvar Alterações');
            }
        });
    });
});
</script>
@endpush
