@extends('adminlte::page')

@section('title', 'Editar Usuário')

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-user-cog mr-2"></i>Gerenciar Usuário
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('partials.session-messages')

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Dados principais</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nova senha</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Confirmar nova senha</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Salvar dados</button>
            </form>
        </div>
    </div>

    @can('assign roles to users')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white">
            <strong>Perfis de acesso</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.assignRoles', $user) }}" method="POST">
                @csrf
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-checkbox p-2 border rounded">
                                <input type="checkbox" class="custom-control-input" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, $userRoles) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="role-{{ $role->id }}">{{ ucfirst($role->name) }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button class="btn btn-info"><i class="fas fa-user-shield mr-1"></i>Salvar perfis</button>
            </form>
        </div>
    </div>
    @endcan

    @can('assign direct permissions to users')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white">
            <strong>Permissões diretas</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.assignDirectPermissions', $user) }}" method="POST">
                @csrf
                @foreach($permissionGroups as $group)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">{{ $group['label'] }}</h5>
                                <small class="text-muted">{{ $group['description'] }}</small>
                            </div>
                        </div>
                        <div class="row permissions-group" data-group="{{ \Illuminate\Support\Str::slug($group['label']) }}">
                            @foreach($group['permissions'] as $permissionName)
                                @php($permission = $permissions->firstWhere('name', $permissionName))
                                @if($permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox p-2 border rounded">
                                            <input class="custom-control-input" type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}" value="{{ $permission->id }}" {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <hr>
                    </div>
                @endforeach
                <button class="btn btn-secondary"><i class="fas fa-key mr-1"></i>Salvar permissões diretas</button>
            </form>
        </div>
    </div>
    @endcan

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <strong>Setores vinculados</strong>
        </div>
        <div class="card-body">
            <p class="text-muted">As ações dentro das etapas obedecem o vínculo do usuário com o setor e as permissões marcadas em cada setor.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Setor</th>
                            <th>Visualizar</th>
                            <th>Editar</th>
                            <th>Aprovar</th>
                            <th>Reprovar</th>
                            <th>Ativo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($user->setores as $setor)
                            <tr>
                                <td>{{ $setor->nome }}</td>
                                <td>{{ $setor->pivot->pode_visualizar ? 'Sim' : 'Não' }}</td>
                                <td>{{ $setor->pivot->pode_editar ? 'Sim' : 'Não' }}</td>
                                <td>{{ $setor->pivot->pode_aprovar ? 'Sim' : 'Não' }}</td>
                                <td>{{ $setor->pivot->pode_reprovar ? 'Sim' : 'Não' }}</td>
                                <td>{{ $setor->pivot->ativo ? 'Sim' : 'Não' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">Usuário ainda não vinculado a nenhum setor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Para alterar essas permissões, acesse o cadastro de setores.</small>
        </div>
    </div>

    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Voltar</a>
</div>
@stop
