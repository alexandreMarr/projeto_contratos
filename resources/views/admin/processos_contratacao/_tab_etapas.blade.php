@php
    $authUser = auth()->user();
    $canManageEtapas = $authUser && $authUser->can('manage etapas processos contratacao');

    $etapasContrato = $processo->etapas
        ->where('origem_tipo', 'CONTRATO')
        ->whereNull('processo_aditivo_id')
        ->sortBy('ordem')
        ->values();

    $haAtrasoContrato = $etapasContrato->contains(fn ($e) => $e->esta_atrasada);
@endphp

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header border-0 bg-white d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h3 class="card-title mb-0 font-weight-bold">Fluxo de Etapas</h3>
            <small class="text-muted">Etapas do contrato e dos aditivos, respeitando ordem, setor e permissões.</small>
        </div>

        @if($canManageEtapas)
            <button type="button" class="btn btn-success btn-sm mt-2 mt-md-0" data-toggle="modal" data-target="#modalNovaEtapaExtra">
                <i class="fas fa-plus mr-1"></i> Nova Etapa Extra
            </button>
        @endif
    </div>

    <div class="card-body bg-light">
        @if($haAtrasoContrato)
            <div class="alert alert-danger border-0 shadow-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Há etapas atrasadas no contrato.
            </div>
        @endif

        <div class="accordion" id="accordionFluxoEtapas">
            <div class="fluxo-group-card">
                <div class="fluxo-group-header" id="headingContrato">
                    <button class="btn btn-link fluxo-group-toggle" type="button" data-toggle="collapse" data-target="#collapseContrato" aria-expanded="true">
                        <span><i class="fas fa-file-contract mr-2"></i> Etapas do Contrato</span>
                        <span class="badge badge-primary">{{ $etapasContrato->count() }}</span>
                    </button>
                </div>

                <div id="collapseContrato" class="collapse show" data-parent="#accordionFluxoEtapas">
                    <div class="fluxo-group-body">
                        @include('admin.processos_contratacao.partials._etapas_cards', [
                            'etapas' => $etapasContrato,
                            'grupoId' => 'contrato',
                            'tituloGrupo' => 'Contrato',
                            'canManageEtapas' => $canManageEtapas,
                            'authUser' => $authUser,
                        ])
                    </div>
                </div>
            </div>

            @foreach($processo->aditivos as $aditivo)
                @php
                    $etapasAditivo = $processo->etapas
                        ->where('origem_tipo', 'ADITIVO')
                        ->where('processo_aditivo_id', $aditivo->id)
                        ->sortBy('ordem')
                        ->values();

                    $haAtrasoAditivo = $etapasAditivo->contains(fn ($e) => $e->esta_atrasada);
                @endphp

                <div class="fluxo-group-card mt-3">
                    <div class="fluxo-group-header" id="headingAditivo{{ $aditivo->id }}">
                        <button class="btn btn-link fluxo-group-toggle collapsed" type="button" data-toggle="collapse" data-target="#collapseAditivo{{ $aditivo->id }}" aria-expanded="false">
                            <span>
                                <i class="fas fa-file-medical mr-2"></i>
                                Etapas do Aditivo
                                <strong>{{ $aditivo->titulo ?: ($aditivo->numero_documento ?: ('#' . $aditivo->id)) }}</strong>
                            </span>
                            <span class="badge badge-info">{{ $etapasAditivo->count() }}</span>
                        </button>
                    </div>

                    <div id="collapseAditivo{{ $aditivo->id }}" class="collapse" data-parent="#accordionFluxoEtapas">
                        <div class="fluxo-group-body">
                            @if($haAtrasoAditivo)
                                <div class="alert alert-danger border-0 shadow-sm mb-3">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Há etapas atrasadas neste aditivo.
                                </div>
                            @endif

                            <div class="mb-3">
                                <div class="small text-muted">Objeto do aditivo</div>
                                <div class="font-weight-bold">{{ $aditivo->objeto ?: $aditivo->descricao }}</div>
                            </div>

                            @include('admin.processos_contratacao.partials._etapas_cards', [
                                'etapas' => $etapasAditivo,
                                'grupoId' => 'aditivo-' . $aditivo->id,
                                'tituloGrupo' => 'Aditivo',
                                'canManageEtapas' => $canManageEtapas,
                                'authUser' => $authUser,
                            ])
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('css')
<style>
.fluxo-group-card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 6px 18px rgba(15,23,42,.05)}
.fluxo-group-header{background:#f8fafc;border-bottom:1px solid #e5e7eb}
.fluxo-group-toggle{width:100%;text-align:left;padding:16px 18px;display:flex;align-items:center;justify-content:space-between;color:#111827!important;text-decoration:none!important;font-weight:700}
.fluxo-group-body{padding:18px}
</style>
@endpush
