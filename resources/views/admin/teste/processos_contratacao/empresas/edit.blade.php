@extends('adminlte::page')

@section('title', 'Editar Empresa')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary mb-3">
        <i class="fas fa-edit"></i> Editar Empresa
    </h1>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('empresas.update', $empresa) }}" method="POST">
        @method('PUT')
        @include('admin.empresas._form')
    </form>
</div>
@stop
