@extends('adminlte::page')

@section('title', 'Novo Perfil')

@section('content_header')
    <h1 class="fw-bold text-primary">
        <i class="fas fa-user-shield mr-2"></i>Novo Perfil de Acesso
    </h1>
@stop

@section('content')
<div class="container-fluid">
    @include('partials.session-messages')

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white"><strong>Dados do perfil</strong></div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Nome do perfil</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white"><strong>Permissões do perfil</strong></div>
            <div class="card-body">
                @foreach($permissionGroups as $group)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="mb-0">{{ $group['label'] }}</h5>
                                <small class="text-muted">{{ $group['description'] }}</small>
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input select-all-group" id="select-all-{{ \Illuminate\Support\Str::slug($group['label']) }}">
                                <label class="custom-control-label" for="select-all-{{ \Illuminate\Support\Str::slug($group['label']) }}">Selecionar todos</label>
                            </div>
                        </div>
                        <div class="row permissions-group" data-group="{{ \Illuminate\Support\Str::slug($group['label']) }}">
                            @foreach($group['permissions'] as $permissionName)
                                @php($permission = \App\Models\Permission::where('name', $permissionName)->first())
                                @if($permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox p-2 border rounded">
                                            <input class="custom-control-input" type="checkbox" name="permissions[]" id="permission-{{ $permission->id }}" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <hr>
                    </div>
                @endforeach
            </div>
        </div>

        <button class="btn btn-success"><i class="fas fa-save mr-1"></i>Salvar perfil</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Voltar</a>
    </form>
</div>
@stop

@push('js')
<script>
$(document).on('change', '.select-all-group', function() {
    const groupId = $(this).attr('id').replace('select-all-', '');
    const checked = $(this).is(':checked');
    $(`.permissions-group[data-group="${groupId}"] input[type="checkbox"]`).prop('checked', checked);
});
</script>
@endpush
