<div class="card card-outline card-info mt-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-robot"></i> Pré-análise do documento
        </h3>
    </div>

    <div class="card-body">
        <div class="mb-3">
            <p class="mb-1"><strong>Arquivo:</strong> {{ $resultado['arquivo']['nome_original'] ?? '-' }}</p>
            <p class="mb-1"><strong>Confiança:</strong> {{ number_format((($resultado['metadados']['confianca'] ?? 0) * 100), 0) }}%</p>
            <p class="mb-0"><strong>Fonte:</strong> {{ $resultado['metadados']['fonte_texto'] ?? '-' }}</p>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered extracao-table mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%;">Razão Social</th>
                                <td>{{ data_get($resultado, 'dados.razao_social') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nome Fantasia</th>
                                <td>{{ data_get($resultado, 'dados.nome_fantasia') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>CNPJ</th>
                                <td>{{ data_get($resultado, 'dados.cnpj') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>E-mail</th>
                                <td>{{ data_get($resultado, 'dados.email') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Telefone</th>
                                <td>{{ data_get($resultado, 'dados.telefone') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Responsável</th>
                                <td>{{ data_get($resultado, 'dados.responsavel') ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered extracao-table mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%;">Objeto</th>
                                <td>{{ data_get($resultado, 'dados.objeto_resumido') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Valor Proposto</th>
                                <td>
                                    @if(data_get($resultado, 'dados.valor_proposto'))
                                        R$ {{ number_format(data_get($resultado, 'dados.valor_proposto'), 2, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Prazo Pagamento</th>
                                <td>
                                    {{ data_get($resultado, 'dados.prazo_pagamento_dias') ? data_get($resultado, 'dados.prazo_pagamento_dias') . ' dias' : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Validade</th>
                                <td>{{ data_get($resultado, 'dados.validade_proposta') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Execução Início</th>
                                <td>{{ data_get($resultado, 'dados.prazo_execucao_inicio') ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Execução Fim</th>
                                <td>{{ data_get($resultado, 'dados.prazo_execucao_fim') ?: '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <h5 class="mb-2">Locais/KMs identificados</h5>
            @php($locais = data_get($resultado, 'dados.locais', []))
            @if(!empty($locais))
                <div class="extracao-badges">
                    @foreach($locais as $local)
                        <span class="badge badge-primary mr-1 mb-1">{{ $local }}</span>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0">Nenhum local/km identificado.</p>
            @endif
        </div>

        <div class="mt-4">
            <h5 class="mb-2">Dados bancários</h5>
            @php($banco = data_get($resultado, 'dados.dados_bancarios', []))
            @if(!empty($banco))
                <div class="table-responsive">
                    <table class="table table-sm table-bordered extracao-table">
                        <tbody>
                            @foreach($banco as $chave => $valor)
                                <tr>
                                    <th style="width: 25%;">{{ ucfirst($chave) }}</th>
                                    <td>{{ $valor }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Nenhum dado bancário identificado.</p>
            @endif
        </div>

        <div class="mt-4">
            <h5 class="mb-2">Itens sugeridos</h5>
            @php($itens = data_get($resultado, 'dados.itens', []))
            @if(!empty($itens))
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered extracao-table">
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th style="width: 90px;">Unidade</th>
                                <th style="width: 110px;">Qtd</th>
                                <th style="width: 120px;">Vlr. Unit.</th>
                                <th style="width: 120px;">Vlr. Total</th>
                            </tr>
                        </thead>
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
                </div>
            @else
                <p class="text-muted mb-0">Nenhum item sugerido.</p>
            @endif
        </div>

        <div class="mt-4">
            <h5 class="mb-2">Resumo do texto lido</h5>
            <div class="extracao-resumo-box">
                {{ data_get($resultado, 'dados.texto_base_resumo') ?: 'Sem resumo disponível.' }}
            </div>
        </div>

        <div class="mt-4">
            <h5 class="mb-2">Observações da análise</h5>
            <ul class="mb-0">
                @foreach(data_get($resultado, 'metadados.observacoes', []) as $obs)
                    <li>{{ $obs }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
