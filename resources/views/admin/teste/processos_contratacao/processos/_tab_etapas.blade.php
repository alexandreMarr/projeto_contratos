<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Etapas do Processo</h3>
        @can('manage etapas processos contratacao')
            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalNovaEtapaExtra">
                <i class="fas fa-plus"></i> Nova Etapa Extra
            </button>
        @endcan
    </div>
    <div class="card-body">
        <div class="timeline timeline-inverse">
            @forelse($processo->etapas->sortBy('ordem') as $etapa)
                <div class="time-label">
                    <span class="bg-{{ $etapa->status === 'CONCLUIDA' ? 'success' : ($etapa->status === 'ATRASADA' ? 'danger' : 'info') }}">
                        {{ str_pad($etapa->ordem, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div>
                    <i class="fas fa-stream bg-{{ $etapa->status === 'CONCLUIDA' ? 'success' : ($etapa->status === 'ATRASADA' ? 'danger' : 'primary') }}"></i>
                    <div class="timeline-item">
                        <span class="time">
                            <i class="far fa-clock"></i>
                            SLA: {{ $etapa->prazo_limite_dias }} dia(s)
                            @if($etapa->data_limite)
                                | Limite: {{ $etapa->data_limite->format('d/m/Y') }}
                            @endif
                        </span>
                        <h3 class="timeline-header">
                            <strong>{{ $etapa->nome_etapa }}</strong>
                            <span class="badge badge-{{ $etapa->status === 'CONCLUIDA' ? 'success' : ($etapa->status === 'ATRASADA' ? 'danger' : 'secondary') }} ml-2">
                                {{ $etapa->status }}
                            </span>
                        </h3>
                        <div class="timeline-body">
                            <p class="mb-1"><strong>Setor:</strong> {{ $etapa->setor_responsavel ?: '-' }}</p>
                            <p class="mb-1"><strong>Responsável:</strong> {{ $etapa->responsavel->name ?? '-' }}</p>
                            <p class="mb-1"><strong>Início:</strong> {{ optional($etapa->data_inicio)->format('d/m/Y') ?: '-' }}</p>
                            <p class="mb-1"><strong>Conclusão:</strong> {{ optional($etapa->data_conclusao)->format('d/m/Y') ?: '-' }}</p>
                            <p class="mb-0"><strong>Observações:</strong><br>{!! nl2br(e($etapa->observacoes ?: '-')) !!}</p>
                        </div>
                        @can('manage etapas processos contratacao')
                            <div class="timeline-footer">
                                <button class="btn btn-primary btn-sm btn-editar-etapa"
                                        data-id="{{ $etapa->id }}"
                                        data-url="{{ route('processos-contratacao.etapas.update', [$processo, $etapa]) }}"
                                        data-nome="{{ $etapa->nome_etapa }}"
                                        data-status="{{ $etapa->status }}"
                                        data-observacoes="{{ $etapa->observacoes }}"
                                        data-data_limite="{{ optional($etapa->data_limite)->format('Y-m-d') }}"
                                        data-data_conclusao="{{ optional($etapa->data_conclusao)->format('Y-m-d') }}">
                                    <i class="fas fa-edit"></i> Atualizar Etapa
                                </button>
                            </div>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-muted">Nenhuma etapa encontrada.</p>
            @endforelse
        </div>
    </div>
</div>
