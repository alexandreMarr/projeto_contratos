@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-users-cog mr-2"></i>Usuários do Sistema
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('partials.session-messages')

    @can('create users')
        <div class="mb-3">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-1"></i>Novo Usuário
            </a>
        </div>
    @endcan

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="scroll-hint px-3 pt-3"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver as ações.</div>
            <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle" style="min-width: 920px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Roles</th>
                        <th>Setores</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse($user->roles as $role)
                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">Sem role</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse($user->setores as $setor)
                                    <span class="badge badge-light border">{{ $setor->nome }}</span>
                                @empty
                                    <span class="text-muted">Sem setor</span>
                                @endforelse
                            </td>
                            <td>
                                <div class="table-actions">
                                @can('edit users')
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning mb-1" title="Gerenciar"><i class="fas fa-user-cog d-md-none"></i><span class="d-none d-md-inline">Gerenciar</span></a>
                                @endcan
                                @can('delete users')
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" title="Excluir" onclick="return confirm('Excluir este usuário?')"><i class="fas fa-trash d-md-none"></i><span class="d-none d-md-inline">Excluir</span></button>
                                        </form>
                                    @endif
                                @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-end">{{ $users->links() }}</div>
</div>
@stop
