@extends('adminlte::page')

@section('title', 'Novo Processo de Contratação')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-file-signature"></i> Novo Processo de Contratação
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('processos-contratacao.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.processos_contratacao._form')
    </form>
</div>
@stop
