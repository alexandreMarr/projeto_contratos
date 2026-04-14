@extends('adminlte::page')

@section('title', 'Novo Setor')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-plus-circle"></i> Novo Setor
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('setores.store') }}" method="POST">
        @csrf
        @include('admin.processos_contratacao.setores._form')
    </form>
</div>
@stop
