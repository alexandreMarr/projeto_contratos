@extends('adminlte::page')

@section('title', 'Perfis')

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-user-shield mr-2"></i>Perfis de Acesso
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('partials.session-messages')

    @can('create roles')
        <div class="mb-3">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Novo perfil</a>
        </div>
    @endcan

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="scroll-hint px-3 pt-3"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver as ações.</div>
            <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0" style="min-width: 760px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Perfil</th>
                        <th>Qtd. permissões</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td><div class="table-actions">
                                @can('edit roles')
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit d-md-none"></i><span class="d-none d-md-inline">Editar</span></a>
                                @endcan
                                @can('delete roles')
                                    @if($role->name !== 'admin')
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger mb-1" onclick="return confirm('Excluir este perfil?')"><i class="fas fa-trash d-md-none"></i><span class="d-none d-md-inline">Excluir</span></button>
                                        </form>
                                    @endif
                                @endcan
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Nenhum perfil encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-end">{{ $roles->links() }}</div>
</div>
@stop
