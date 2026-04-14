<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Dados do Setor</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" class="form-control"
                           value="{{ old('nome', $setor->nome ?? '') }}" required
                           oninput="this.value = this.value.toUpperCase();">
                    <small class="text-muted">O nome será salvo sempre em maiúsculo.</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="ativo" name="ativo" value="1" @checked(old('ativo', $setor->ativo ?? true))>
                        <label class="form-check-label" for="ativo">Setor ativo</label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="3" class="form-control">{{ old('descricao', $setor->descricao ?? '') }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-2">
                    <label>Adicionar usuários ao setor</label>
                    <select id="usuario_selector" class="form-control select2" data-placeholder="Selecione os usuários">
                        <option value=""></option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Ao adicionar, defina o que cada usuário pode fazer dentro desse setor.</small>
                </div>
            </div>
        </div>

        @php
            $usuariosSetor = collect();
            if (old('usuarios_permissoes')) {
                $usuariosSetor = collect(old('usuarios_permissoes'))->map(function ($item) use ($usuarios) {
                    $usuario = $usuarios->firstWhere('id', (int) ($item['user_id'] ?? 0));
                    return [
                        'id' => $usuario?->id,
                        'name' => $usuario?->name,
                        'pode_visualizar' => !empty($item['pode_visualizar']),
                        'pode_editar' => !empty($item['pode_editar']),
                        'pode_aprovar' => !empty($item['pode_aprovar']),
                        'pode_reprovar' => !empty($item['pode_reprovar']),
                        'ativo' => !array_key_exists('ativo', $item) || !empty($item['ativo']),
                    ];
                })->filter(fn ($u) => !empty($u['id']));
            } elseif (isset($setor)) {
                $usuariosSetor = $setor->usuarios->map(fn ($usuario) => [
                    'id' => $usuario->id,
                    'name' => $usuario->name,
                    'pode_visualizar' => (bool) $usuario->pivot->pode_visualizar,
                    'pode_editar' => (bool) $usuario->pivot->pode_editar,
                    'pode_aprovar' => (bool) $usuario->pivot->pode_aprovar,
                    'pode_reprovar' => (bool) $usuario->pivot->pode_reprovar,
                    'ativo' => (bool) $usuario->pivot->ativo,
                ]);
            }
        @endphp

        <div class="mt-3">
            <h5 class="mb-3">Usuários vinculados e permissões</h5>
            <div id="usuarios-permissoes-container">
                @forelse($usuariosSetor as $index => $item)
                    <div class="card card-outline card-secondary usuario-permissao-item" data-user-id="{{ $item['id'] }}">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <strong>{{ $item['name'] }}</strong>
                            <div class="d-flex ml-auto align-items-center justify-content-end">
                                <button type="button" class="btn btn-xs btn-outline-primary toggle-permissoes mr-2"><i class="fas fa-chevron-down"></i></button>
                                <button type="button" class="btn btn-xs btn-outline-danger remover-usuario-permissao"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="card-body usuario-permissoes-body">
                            <input type="hidden" name="usuarios_permissoes[{{ $index }}][user_id]" value="{{ $item['id'] }}">
                            <div class="row">
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-visualizar" type="checkbox" name="usuarios_permissoes[{{ $index }}][pode_visualizar]" value="1" @checked($item['pode_visualizar'])><label class="form-check-label">Visualizar</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-editar" type="checkbox" name="usuarios_permissoes[{{ $index }}][pode_editar]" value="1" @checked($item['pode_editar'])><label class="form-check-label">Editar</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-aprovar" type="checkbox" name="usuarios_permissoes[{{ $index }}][pode_aprovar]" value="1" @checked($item['pode_aprovar'])><label class="form-check-label">Aprovar / concluir</label></div></div>
                                <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-reprovar" type="checkbox" name="usuarios_permissoes[{{ $index }}][pode_reprovar]" value="1" @checked($item['pode_reprovar'])><label class="form-check-label">Reprovar</label></div></div>
                            </div>
                            <div class="mt-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="usuarios_permissoes[{{ $index }}][ativo]" value="1" @checked($item['ativo'])><label class="form-check-label">Vínculo ativo neste setor</label></div></div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-light border" id="usuario-permissao-empty">Nenhum usuário vinculado ainda.</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('setores.index') }}" class="btn btn-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </div>
</div>

@push('css')
<style>
.usuario-permissao-item .card-header { cursor: pointer; }
.usuario-permissao-item { margin-bottom: 12px; }
.usuario-permissao-item .card-header > .d-flex, .usuario-permissao-item .card-header > div { margin-left:auto; }
</style>
@endpush

@push('js')
<script>
$(function () {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    let usuarioIndex = $('#usuarios-permissoes-container .usuario-permissao-item').length;

    function templateUsuario(userId, userName, index) {
        return `
        <div class="card card-outline card-secondary usuario-permissao-item" data-user-id="${userId}">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <strong>${userName}</strong>
                <div class="d-flex ml-auto align-items-center justify-content-end">
                    <button type="button" class="btn btn-xs btn-outline-primary toggle-permissoes mr-2"><i class="fas fa-chevron-down"></i></button>
                    <button type="button" class="btn btn-xs btn-outline-danger remover-usuario-permissao"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <div class="card-body usuario-permissoes-body">
                <input type="hidden" name="usuarios_permissoes[${index}][user_id]" value="${userId}">
                <div class="row">
                    <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-visualizar" type="checkbox" name="usuarios_permissoes[${index}][pode_visualizar]" value="1" checked><label class="form-check-label">Visualizar</label></div></div>
                    <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-editar" type="checkbox" name="usuarios_permissoes[${index}][pode_editar]" value="1"><label class="form-check-label">Editar</label></div></div>
                    <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-aprovar" type="checkbox" name="usuarios_permissoes[${index}][pode_aprovar]" value="1"><label class="form-check-label">Aprovar / concluir</label></div></div>
                    <div class="col-md-3"><div class="form-check"><input class="form-check-input perm-reprovar" type="checkbox" name="usuarios_permissoes[${index}][pode_reprovar]" value="1"><label class="form-check-label">Reprovar</label></div></div>
                </div>
                <div class="mt-2"><div class="form-check"><input class="form-check-input" type="checkbox" name="usuarios_permissoes[${index}][ativo]" value="1" checked><label class="form-check-label">Vínculo ativo neste setor</label></div></div>
            </div>
        </div>`;
    }

    $('#usuario_selector').on('change', function () {
        const userId = $(this).val();
        const userName = $(this).find('option:selected').text();
        if (!userId) return;
        if ($(`.usuario-permissao-item[data-user-id="${userId}"]`).length) {
            Swal.fire('Atenção', 'Este usuário já foi adicionado ao setor.', 'warning');
            $(this).val(null).trigger('change');
            return;
        }
        $('#usuario-permissao-empty').remove();
        $('#usuarios-permissoes-container').append(templateUsuario(userId, userName, usuarioIndex));
        usuarioIndex++;
        $(this).val(null).trigger('change');
    });

    $(document).on('click', '.remover-usuario-permissao', function () {
        $(this).closest('.usuario-permissao-item').remove();
        if (!$('#usuarios-permissoes-container .usuario-permissao-item').length) {
            $('#usuarios-permissoes-container').html('<div class="alert alert-light border" id="usuario-permissao-empty">Nenhum usuário vinculado ainda.</div>');
        }
    });

    $(document).on('click', '.toggle-permissoes', function () {
        $(this).closest('.usuario-permissao-item').find('.usuario-permissoes-body').slideToggle(150);
    });

    $(document).on('change', '.perm-editar, .perm-aprovar, .perm-reprovar', function () {
        if ($(this).is(':checked')) {
            $(this).closest('.usuario-permissao-item').find('.perm-visualizar').prop('checked', true);
        }
    });
});
</script>
@endpush
