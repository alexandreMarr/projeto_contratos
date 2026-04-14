<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-robot"></i> Pré-análise do documento</h3>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>Arquivo:</strong> {{ $resultado['arquivo']['nome_original'] ?? '-' }}<br>
            <strong>Confiança:</strong> {{ number_format((($resultado['metadados']['confianca'] ?? 0) * 100), 0) }}%<br>
            <strong>Fonte:</strong> {{ $resultado['metadados']['fonte_texto'] ?? '-' }}
        </div>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <tr><th>Razão Social</th><td>{{ data_get($resultado, 'dados.razao_social') ?: '-' }}</td></tr>
                    <tr><th>Nome Fantasia</th><td>{{ data_get($resultado, 'dados.nome_fantasia') ?: '-' }}</td></tr>
                    <tr><th>CNPJ</th><td>{{ data_get($resultado, 'dados.cnpj') ?: '-' }}</td></tr>
                    <tr><th>E-mail</th><td>{{ data_get($resultado, 'dados.email') ?: '-' }}</td></tr>
                    <tr><th>Telefone</th><td>{{ data_get($resultado, 'dados.telefone') ?: '-' }}</td></tr>
                    <tr><th>Responsável</th><td>{{ data_get($resultado, 'dados.responsavel') ?: '-' }}</td></tr>
                </table>
            </div>

            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <tr><th>Objeto</th><td>{{ data_get($resultado, 'dados.objeto_resumido') ?: '-' }}</td></tr>
                    <tr><th>Valor Proposto</th><td>@if(data_get($resultado, 'dados.valor_proposto')) R$ {{ number_format(data_get($resultado, 'dados.valor_proposto'), 2, ',', '.') }} @else - @endif</td></tr>
                    <tr><th>Prazo Pagamento</th><td>{{ data_get($resultado, 'dados.prazo_pagamento_dias') ? data_get($resultado, 'dados.prazo_pagamento_dias') . ' dias' : '-' }}</td></tr>
                    <tr><th>Validade</th><td>{{ data_get($resultado, 'dados.validade_proposta') ?: '-' }}</td></tr>
                    <tr><th>Execução Início</th><td>{{ data_get($resultado, 'dados.prazo_execucao_inicio') ?: '-' }}</td></tr>
                    <tr><th>Execução Fim</th><td>{{ data_get($resultado, 'dados.prazo_execucao_fim') ?: '-' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <h5>Locais/KMs identificados</h5>
            @php($locais = data_get($resultado, 'dados.locais', []))
            @if(!empty($locais))
                @foreach($locais as $local)
                    <span class="badge badge-primary mr-1">{{ $local }}</span>
                @endforeach
            @else
                <p class="text-muted">Nenhum local/km identificado.</p>
            @endif
        </div>

        <div class="mt-3">
            <h5>Dados bancários</h5>
            @php($banco = data_get($resultado, 'dados.dados_bancarios', []))
            @if(!empty($banco))
                <table class="table table-sm table-bordered">
                    @foreach($banco as $chave => $valor)
                        <tr><th>{{ ucfirst($chave) }}</th><td>{{ $valor }}</td></tr>
                    @endforeach
                </table>
            @else
                <p class="text-muted">Nenhum dado bancário identificado.</p>
            @endif
        </div>

        <div class="mt-3">
            <h5>Itens sugeridos</h5>
            @php($itens = data_get($resultado, 'dados.itens', []))
            @if(!empty($itens))
                <table class="table table-sm table-striped">
                    <thead><tr><th>Descrição</th><th>Unidade</th><th>Quantidade</th><th>Valor Unit.</th><th>Valor Total</th></tr></thead>
                    <tbody>
                        @foreach($itens as $item)
                            <tr>
                                <td>{{ $item['descricao'] ?? '-' }}</td>
                                <td>{{ $item['unidade'] ?? '-' }}</td>
                                <td>{{ $item['quantidade'] ?? '-' }}</td>
                                <td>{{ $item['valor_unitario'] ?? '-' }}</td>
                                <td>{{ $item['valor_total'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">Nenhum item sugerido.</p>
            @endif
        </div>

        <div class="mt-3">
            <h5>Resumo do texto lido</h5>
            <div class="alert alert-light border">{{ data_get($resultado, 'dados.texto_base_resumo') ?: 'Sem resumo disponível.' }}</div>
        </div>

        <div class="mt-3">
            <h5>Observações da análise</h5>
            <ul>
                @foreach(data_get($resultado, 'metadados.observacoes', []) as $obs)
                    <li>{{ $obs }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
