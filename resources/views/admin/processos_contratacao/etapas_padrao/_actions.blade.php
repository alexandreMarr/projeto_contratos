<div class="btn-group">
    @can('edit etapas padrao')
        <a href="{{ route('etapas-padrao.edit', $etapa) }}" class="btn btn-xs btn-primary" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan

    @can('edit etapas padrao')
        <form action="{{ route('etapas-padrao.toggle', $etapa) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-xs btn-warning" title="{{ $etapa->ativo ? 'Inativar' : 'Ativar' }}">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    @endcan

    @can('create etapas padrao')
        <form action="{{ route('etapas-padrao.duplicar', $etapa) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-xs btn-info" title="Duplicar">
                <i class="fas fa-copy"></i>
            </button>
        </form>
    @endcan

    @can('delete etapas padrao')
        <form action="{{ route('etapas-padrao.destroy', $etapa) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Deseja realmente excluir esta etapa padrão?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-xs btn-danger" title="Excluir">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
