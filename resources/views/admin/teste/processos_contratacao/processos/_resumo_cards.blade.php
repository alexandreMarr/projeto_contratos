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
                <span class="info-box-number">{{ $processo->etapas->whereIn('status', ['PENDENTE','EM_ANDAMENTO','AGUARDANDO','ATRASADA'])->sortBy('ordem')->first()->nome_etapa ?? 'Concluído' }}</span>
            </div>
        </div>
    </div>
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
                <span class="info-box-text">Valor Aprovado</span>
                <span class="info-box-number">R$ {{ number_format($processo->valor_aprovado_final ?? 0, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
