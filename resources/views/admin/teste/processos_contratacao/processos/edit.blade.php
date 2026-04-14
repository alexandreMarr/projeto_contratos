@extends('adminlte::page')

@section('title', 'Editar Processo de Contratação')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-edit"></i> Editar Processo de Contratação
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('processos-contratacao.update', $processo) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.processos_contratacao._form')
    </form>
</div>
@stop
