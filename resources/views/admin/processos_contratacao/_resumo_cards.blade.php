@php
    $etapaAtual = $processo->etapa_atual_real;
    $temAditivos = $processo->aditivos->count() > 0;
    $etapaAtual = $processo->etapa_atual_real;
    $prevEtapaAtual = $processo->previsao_conclusao_etapa_atual;
    $prevProcesso = $processo->previsao_conclusao_processo;
@endphp
<div class="row mb-3">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-file-signature"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Status</span>
                <span class="info-box-number">{{ $processo->status }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-stream"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Etapa Atual</span>
                <span class="info-box-number">{{ $etapaAtual->nome_etapa ?? 'Concluído' }}</span>
            </div>
        </div>
    </div>
    @if($temAditivos)
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-file-medical"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Valor de Aditivos</span>
                <span class="info-box-number">R$ {{ number_format($processo->valor_total_aditivos, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @endif
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Valor Proposto</span>
                <span class="info-box-number">R$ {{ number_format($processo->valor_proposto ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box bg-secondary">
            <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Valor Contratual Atual</span>
                <span class="info-box-number">R$ {{ number_format($processo->valor_contratual_atual, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

   <div class="col-md-3 col-sm-6 col-12">
        <div class="small-box bg-warning">
            <div class="inner">
                <p>PREV. ETAPA ATUAL</p>
                <h5>{{ $prevEtapaAtual ? \Carbon\Carbon::parse($prevEtapaAtual)->format('d/m/Y') : '-' }}</h5>
            </div>
            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-12">
        <div class="small-box bg-secondary">
            <div class="inner">
                <p>PREV. PROCESSO</p>
                <h5>{{ $prevProcesso ? \Carbon\Carbon::parse($prevProcesso)->format('d/m/Y') : '-' }}</h5>
            </div>
            <div class="icon"><i class="fas fa-flag-checkered"></i></div>
        </div>
    </div>

</div>
