<div class="btn-group">
    @can('view empresas')
        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-xs btn-outline-primary" title="Visualizar">
            <i class="fas fa-eye"></i>
        </a>
    @endcan
    @can('edit empresas')
        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-xs btn-outline-warning" title="Editar">
            <i class="fas fa-edit"></i>
        </a>
    @endcan
    @can('create processos contratacao')
        <a href="{{ route('processos-contratacao.create', ['empresa_contratada_id' => $empresa->id]) }}" class="btn btn-xs btn-outline-success" title="Novo Processo">
            <i class="fas fa-file-signature"></i>
        </a>
    @endcan
</div>
