<div class="btn-group">
    @can('view processos contratacao')
        <a href="{{ route('processos-contratacao.show', $processo) }}" class="btn btn-xs btn-outline-primary" title="Visualizar">
            <i class="fas fa-eye"></i>
        </a>
    @endcan
    @can('edit processos contratacao')
        <a href="{{ route('processos-contratacao.edit', $processo) }}" class="btn btn-xs btn-outline-warning" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan
</div>
