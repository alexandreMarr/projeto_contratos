<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary mb-3">
            <div class="card-header"><h3 class="card-title">Resumo do Processo</h3></div>
            <div class="card-body">
                <p class="mb-2"><strong>Nº Processo:</strong> {{ $processo->numero_processo_interno }}</p>
                <p class="mb-2"><strong>Título:</strong> {{ $processo->titulo }}</p>
                <p class="mb-2"><strong>Objeto:</strong> {{ $processo->objeto_resumido }}</p>
                <p class="mb-2"><strong>Tipo:</strong> {{ $processo->tipo_contratacao ?: '-' }}</p>
                <p class="mb-2"><strong>Categoria:</strong> {{ $processo->categoria ?: '-' }}</p>
                <p class="mb-0"><strong>Escopo:</strong><br>{!! nl2br(e($processo->escopo_detalhado ?: '-')) !!}</p>
            </div>
        </div>

        <div class="card card-outline card-info mb-3">
            <div class="card-header"><h3 class="card-title">Empresas Vinculadas</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Contratante</h5>
                        <p class="mb-1"><strong>{{ $processo->empresaContratante->razao_social ?? '-' }}</strong></p>
                        <p class="mb-1">CNPJ: {{ $processo->empresaContratante->cnpj ?? '-' }}</p>
                        <p class="mb-0">Contato: {{ $processo->empresaContratante->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Contratada</h5>
                        <p class="mb-1"><strong>{{ $processo->empresaContratada->razao_social ?? '-' }}</strong></p>
                        <p class="mb-1">CNPJ: {{ $processo->empresaContratada->cnpj ?? '-' }}</p>
                        <p class="mb-0">Contato: {{ $processo->empresaContratada->email ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Observações</h3></div>
            <div class="card-body">
                {!! nl2br(e($processo->observacoes ?: 'Nenhuma observação cadastrada.')) !!}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success mb-3">
            <div class="card-header"><h3 class="card-title">Datas e Vigência</h3></div>
            <div class="card-body">
                <p class="mb-1"><strong>Solicitação:</strong> {{ optional($processo->data_solicitacao)->format('d/m/Y') ?: '-' }}</p>
                <p class="mb-1"><strong>Receb. Proposta:</strong> {{ optional($processo->data_recebimento_proposta)->format('d/m/Y') ?: '-' }}</p>
                <p class="mb-1"><strong>Validade:</strong> {{ optional($processo->validade_proposta)->format('d/m/Y') ?: '-' }}</p>
                <p class="mb-1"><strong>Execução:</strong> {{ optional($processo->prazo_execucao_inicio)->format('d/m/Y') ?: '-' }} até {{ optional($processo->prazo_execucao_fim)->format('d/m/Y') ?: '-' }}</p>
                <p class="mb-0"><strong>Vigência:</strong> {{ optional($processo->vigencia_inicio)->format('d/m/Y') ?: '-' }} até {{ optional($processo->vigencia_fim)->format('d/m/Y') ?: '-' }}</p>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Financeiro</h3></div>
            <div class="card-body">
                <p class="mb-1"><strong>Valor Estimado:</strong> R$ {{ number_format($processo->valor_estimado ?? 0, 2, ',', '.') }}</p>
                <p class="mb-1"><strong>Valor Proposto:</strong> R$ {{ number_format($processo->valor_proposto ?? 0, 2, ',', '.') }}</p>
                <p class="mb-1"><strong>Valor Aprovado:</strong> R$ {{ number_format($processo->valor_aprovado_final ?? 0, 2, ',', '.') }}</p>
                <p class="mb-1"><strong>Prazo Pagto:</strong> {{ $processo->prazo_pagamento_dias ?: '-' }} dias</p>
                <p class="mb-0"><strong>Nº Contrato:</strong> {{ $processo->numero_contrato_assinado ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>
