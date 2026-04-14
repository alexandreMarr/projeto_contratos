@extends('adminlte::page')

@section('title', 'Novo Usuário')
@section('plugins.Select2', true)

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-user-plus mr-2"></i>Novo Usuário
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('partials.session-messages')

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">
                <strong>Dados do usuário</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Senha</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar senha</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <strong>Perfis de acesso</strong>
                <small>O vínculo de setor é configurado na tela de Setores.</small>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($roles as $role)
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-checkbox p-2 border rounded">
                                <input type="checkbox" class="custom-control-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="role-{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted">Nenhum perfil cadastrado.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white">
                <strong>Setores existentes</strong>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">As permissões operacionais das etapas são definidas por setor. Depois de criar o usuário, vincule-o nos setores necessários.</p>
                <div class="row">
                    @forelse($setores as $setor)
                        <div class="col-md-3 mb-2">
                            <span class="badge badge-light border px-3 py-2">{{ $setor->nome }}</span>
                        </div>
                    @empty
                        <div class="col-12 text-muted">Nenhum setor ativo cadastrado.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <button class="btn btn-success"><i class="fas fa-save mr-1"></i>Salvar usuário</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@stop
