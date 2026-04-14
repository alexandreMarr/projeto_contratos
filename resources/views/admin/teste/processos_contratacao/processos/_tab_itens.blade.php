<div class="card card-outline card-primary">
    <div class="card-header"><h3 class="card-title">Itens / Serviços da Proposta</h3></div>
    <div class="card-body">
        <table id="itens-processo-table" class="table table-bordered table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Ordem</th>
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
                @forelse($processo->itens->sortBy('ordem') as $item)
                    <tr>
                        <td>{{ $item->ordem }}</td>
                        <td>{{ $item->grupo ?: '-' }}</td>
                        <td>{{ $item->subgrupo ?: '-' }}</td>
                        <td>{{ $item->descricao }}</td>
                        <td>{{ $item->unidade ?: '-' }}</td>
                        <td>{{ $item->quantidade !== null ? number_format($item->quantidade, 2, ',', '.') : '-' }}</td>
                        <td>R$ {{ number_format($item->valor_unitario ?? 0, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item->valor_total ?? 0, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">Nenhum item carregado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
