<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Importar Itens</h3>
            </div>
            <form action="{{ route('processos-contratacao.itens.importar', $processo) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">
                    <div class="form-group">
                        <label>Origem dos Itens *</label>
                        <select name="origem_tipo" id="origem_tipo" class="form-control" required>
                            <option value="">Selecione</option>
                            <option value="CONTRATO">Contrato</option>
                            <option value="ADITIVO">Aditivo</option>
                        </select>
                    </div>

                    <div class="form-group d-none" id="bloco_aditivo">
                        <label>Aditivo *</label>
                        <select name="aditivo_id" class="form-control">
                            <option value="">Selecione</option>
                            @foreach($processo->aditivos as $aditivo)
                                <option value="{{ $aditivo->id }}">
                                    {{ $aditivo->numero_documento ?: 'Aditivo #' . $aditivo->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Planilha *</label>
                        <input type="file" name="arquivo_planilha" class="form-control" accept=".xls,.xlsx,.csv" required>
                        <small class="text-muted">Importa somente a área de detalhamento da proposta.</small>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-import"></i> Importar Itens
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Itens / Serviços da Proposta</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Origem</th>
                            <th>Ordem</th>
                            <th>Código</th>
                            <th>Nível</th>
                            <th>Tipo</th>
                            <th>Grupo</th>
                            <th>Subgrupo</th>
                            <th>Descrição</th>
                            <th>Unid.</th>
                            <th>Qtd</th>
                            <th>Vlr Unit.</th>
                            <th>Vlr Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processo->itens as $item)
                            <tr>
                                <td>
                                    @if($item->origem_tipo === 'ADITIVO')
                                        <span class="badge badge-warning">ADITIVO</span>
                                    @else
                                        <span class="badge badge-primary">CONTRATO</span>
                                    @endif
                                </td>
                                <td>{{ $item->ordem }}</td>
                                <td>{{ $item->codigo_item ?? '-' }}</td>
                                <td>{{ $item->nivel }}</td>
                                <td>{{ $item->tipo_linha }}</td>
                                <td>{{ $item->grupo ?? '-' }}</td>
                                <td>{{ $item->subgrupo ?? '-' }}</td>
                                <td style="padding-left: {{ max(0, ($item->nivel - 1) * 18) }}px;">
                                    {{ $item->descricao }}
                                </td>
                                <td>{{ $item->unidade ?? '-' }}</td>
                                <td>{{ $item->quantidade !== null ? number_format($item->quantidade, 4, ',', '.') : '-' }}</td>
                                <td>{{ $item->valor_unitario !== null ? 'R$ ' . number_format($item->valor_unitario, 2, ',', '.') : '-' }}</td>
                                <td>{{ $item->valor_total !== null ? 'R$ ' . number_format($item->valor_total, 2, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">Nenhum item carregado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(function () {
    $('#origem_tipo').on('change', function () {
        if ($(this).val() === 'ADITIVO') {
            $('#bloco_aditivo').removeClass('d-none');
        } else {
            $('#bloco_aditivo').addClass('d-none');
            $('#bloco_aditivo select').val('');
        }
    });
});
</script>
@endpush
