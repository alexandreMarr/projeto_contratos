@extends('adminlte::page')

@section('title', 'Editar Setor')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-edit"></i> Editar Setor
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('setores.update', $setor) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.processos_contratacao.setores._form')
    </form>
</div>
@stop
