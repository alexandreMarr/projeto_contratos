<div class="btn-group btn-group-sm table-actions" role="group">
    @can('edit setores')
        <a href="{{ route('setores.edit', $setor) }}" class="btn btn-xs btn-primary" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan

    @can('delete setores')
        <form action="{{ route('setores.destroy', $setor) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Deseja realmente excluir este setor?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-xs btn-danger" title="Excluir">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
