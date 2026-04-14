
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard Geral</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Usuários</span>
                <span class="info-box-number">{{ $totalUsuarios }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Empresas</span>
                <span class="info-box-number">{{ $totalEmpresas }}</span>
            </div>
        </div>
    </div>
</div>
@stop
