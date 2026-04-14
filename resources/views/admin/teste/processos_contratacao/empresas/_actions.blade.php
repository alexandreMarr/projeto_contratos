<div class="btn-group">
    @can('view empresas')
        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-xs btn-info" title="Visualizar">
            <i class="fas fa-eye"></i>
        </a>
    @endcan

    @can('edit empresas')
        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-xs btn-primary" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan

    @can('delete empresas')
        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Deseja realmente excluir esta empresa?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-xs btn-danger" title="Excluir">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endcan
</div>
