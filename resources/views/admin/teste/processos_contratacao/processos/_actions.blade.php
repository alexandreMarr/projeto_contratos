<div class="btn-group">
    @can('view processos contratacao')
        <a href="{{ route('processos-contratacao.show', $processo) }}" class="btn btn-xs btn-info" title="Visualizar">
            <i class="fas fa-eye"></i>
        </a>
    @endcan

    @can('edit processos contratacao')
        <a href="{{ route('processos-contratacao.edit', $processo) }}" class="btn btn-xs btn-primary" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan

    @can('delete processos contratacao')
        <form action="{{ route('processos-contratacao.destroy', $processo) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Deseja realmente excluir este processo?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-xs btn-danger" title="Excluir">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
