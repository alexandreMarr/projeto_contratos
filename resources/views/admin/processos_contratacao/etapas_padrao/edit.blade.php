@extends('adminlte::page')

@section('title', 'Editar Etapa Padrão')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-edit"></i> Editar Etapa Padrão
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('etapas-padrao.update', $etapaTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.processos_contratacao.etapas_padrao._form')
    </form>
</div>
@stop
