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
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @include('admin.processos_contratacao._resumo_cards')
    <div class="card card-primary card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="abas-processo" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#tab-dados"><i class="fa fa-info-circle"></i> Dados Gerais</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-etapas"><i class="fa fa-stream"></i> Etapas</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-anexos"><i class="fa fa-paperclip"></i> Anexos</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-itens"><i class="fa fa-list"></i> Itens</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-aditivos"><i class="fa fa-file-medical"></i> Aditivos</a></li>
                <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-historico"><i class="fa fa-history"></i> Histórico</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="abas-processo-content">
                <div class="tab-pane fade show active" id="tab-dados">@include('admin.processos_contratacao._tab_dados_gerais')</div>
                <div class="tab-pane fade" id="tab-etapas">@include('admin.processos_contratacao._tab_etapas')</div>
                <div class="tab-pane fade" id="tab-anexos">@include('admin.processos_contratacao._tab_anexos')</div>
                <div class="tab-pane fade" id="tab-itens">@include('admin.processos_contratacao._tab_itens')</div>
                <div class="tab-pane fade" id="tab-aditivos">@include('admin.processos_contratacao._tab_aditivos')</div>
                <div class="tab-pane fade" id="tab-historico">@include('admin.processos_contratacao._tab_historico')</div>
            </div>
        </div>
    </div>
    @include('admin.processos_contratacao._modal_nova_etapa_extra')
</div>
@stop

@push('css')
<style>
.info-box{min-height:80px;display:flex;margin-bottom:1rem;padding:.5rem;border-radius:.25rem;box-shadow:0 0 1px rgba(0,0,0,.125),0 1px 3px rgba(0,0,0,.2)}.info-box-icon{border-radius:.25rem;display:flex;align-items:center;justify-content:center;font-size:1.8rem;width:70px}.info-box-content{display:flex;flex-direction:column;justify-content:center;line-height:120%;flex:1;padding:0 10px}.info-box-text{display:block;font-size:12px;text-transform:uppercase}.info-box-number{display:block;font-weight:700;font-size:14px}
</style>
@endpush

@push('js')
<script>
$(function(){ $('.select2').select2({ theme: 'bootstrap4', width: '100%' }); });
</script>
@endpush
