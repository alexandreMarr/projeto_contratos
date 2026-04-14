<div class="etapas-grid">
    @forelse($etapas as $etapa)
        @php
            $canView = $etapa->userPodeVisualizar($authUser);
            $canEdit = $etapa->userPodeEditar($authUser);
            $canApprove = $etapa->userPodeAprovar($authUser);
            $canReprove = $etapa->userPodeReprovar($authUser);
            $canAct = $canEdit || $canApprove || $canReprove || $canManageEtapas;

            $cor = $etapa->cor_card;
            $status = $etapa->status_exibicao;
            $isBlocked = $etapa->esta_bloqueada;
            $isLockedVisual = $isBlocked && !in_array($status, ['APROVADA', 'REPROVADA', 'CANCELADA'], true);
            $canCancelar = $canManageEtapas && !in_array($etapa->status_normalizado, ['APROVADA', 'CANCELADA'], true);
            $badgeClass = 'badge-secondary';
            if ($status === 'APROVADA') $badgeClass = 'badge-success';
            elseif ($status === 'REPROVADA') $badgeClass = 'badge-warning';
            elseif ($status === 'CANCELADA') $badgeClass = 'badge-danger';
            elseif ($status === 'EM_ANDAMENTO') $badgeClass = 'badge-info';
            elseif ($status === 'LIBERADA') $badgeClass = 'badge-primary';
            $modalId = 'modal-etapa-' . $grupoId . '-' . $etapa->id;
        @endphp

        <div class="etapa-card {{ $isLockedVisual ? 'is-blocked' : '' }} {{ $etapa->esta_atrasada ? 'is-late' : '' }}" style="--etapa-cor: {{ $cor }};">
            <div class="etapa-card-top">
                <div class="etapa-top-left">
                    <span class="etapa-index">{{ $tituloGrupo }} · Etapa {{ str_pad((string) $etapa->ordem, 2, '0', STR_PAD_LEFT) }}</span>
                    <h5 class="etapa-title mb-0">@if($isLockedVisual)<i class="fas fa-lock text-muted mr-1"></i>@endif{{ $etapa->nome_etapa }}</h5>
                </div>
                <span class="badge {{ $badgeClass }} etapa-status-badge">{{ $status }}</span>
            </div>

            <div class="etapa-card-body">
                <div class="etapa-meta-list">
                    <div class="etapa-meta-item"><span class="meta-label">Setor</span><span class="meta-value">{{ $etapa->setor_responsavel ?: '-' }}</span></div>
                    <div class="etapa-meta-item"><span class="meta-label">Início</span><span class="meta-value">{{ optional($etapa->data_inicio)->format('d/m/Y') ?: '-' }}</span></div>
                    <div class="etapa-meta-item"><span class="meta-label">Prazo</span><span class="meta-value">{{ optional($etapa->data_limite)->format('d/m/Y') ?: '-' }}</span></div>
                    <div class="etapa-meta-item"><span class="meta-label">Conclusão</span><span class="meta-value">{{ optional($etapa->data_conclusao)->format('d/m/Y H:i') ?: '-' }}</span></div>
                    <div class="etapa-meta-item meta-item-wide"><span class="meta-label">SLA</span><span class="meta-value">{{ $etapa->prazo_limite_dias }} dia(s)</span></div>
                </div>

                @if($etapa->esta_atrasada)
                    <div class="etapa-alert etapa-alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> Processo atrasado</div>
                @endif
                @if($isLockedVisual)
                    <div class="etapa-alert etapa-alert-secondary"><i class="fas fa-lock mr-1"></i> Bloqueada aguardando aprovação da etapa anterior</div>
                @endif
                @if(in_array($etapa->status_normalizado, ['REPROVADA', 'CANCELADA'], true))
                    <div class="etapa-alert etapa-alert-warning"><i class="fas fa-ban mr-1"></i> Fluxo interrompido nesta etapa</div>
                @endif
            </div>

            <div class="etapa-card-footer">
                <button type="button" class="btn btn-sm btn-outline-primary btn-block" data-toggle="modal" data-target="#{{ $modalId }}">
                    <i class="fas fa-external-link-alt mr-1"></i> Abrir etapa
                </button>
            </div>
        </div>

        <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content modal-etapa" style="border-top: 4px solid {{ $cor }};">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-1">{{ $etapa->nome_etapa }}</h5>
                            <div class="small text-muted">{{ $tituloGrupo }} · Etapa {{ str_pad((string) $etapa->ordem, 2, '0', STR_PAD_LEFT) }} · {{ $etapa->setor_responsavel ?: 'Sem setor' }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge {{ $badgeClass }} mr-3">{{ $status }}</span>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
                        </div>
                    </div>
                    <div class="modal-body">
                        @if(!$canView && !$canManageEtapas)
                            <div class="alert alert-secondary mb-0">Você não possui permissão para visualizar esta etapa.</div>
                        @else
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="info-box-etapa">
                                        <div class="ibox-item"><span class="ibox-label">Setor responsável</span><strong>{{ $etapa->setor_responsavel ?: '-' }}</strong></div>
                                        <div class="ibox-item"><span class="ibox-label">Data início</span><strong>{{ optional($etapa->data_inicio)->format('d/m/Y') ?: '-' }}</strong></div>
                                        <div class="ibox-item"><span class="ibox-label">Prazo</span><strong>{{ optional($etapa->data_limite)->format('d/m/Y') ?: '-' }}</strong></div>
                                        <div class="ibox-item"><span class="ibox-label">Conclusão</span><strong>{{ optional($etapa->data_conclusao)->format('d/m/Y H:i') ?: '-' }}</strong></div>
                                        <div class="ibox-item"><span class="ibox-label">SLA</span><strong>{{ $etapa->prazo_limite_dias }} dia(s)</strong></div>
                                    </div>
                                    @if($isLockedVisual)
                                        <div class="alert alert-secondary mt-3 mb-0"><i class="fas fa-lock mr-1"></i> Esta etapa está bloqueada. Ela só será liberada quando a etapa anterior for aprovada.</div>
                                    @endif
                                    @if($etapa->esta_atrasada)
                                        <div class="alert alert-danger mt-3 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> Etapa vencida.</div>
                                    @endif
                                </div>
                                <div class="col-lg-8">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#detalhes-{{ $grupoId }}-{{ $etapa->id }}">Detalhes</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#historico-{{ $grupoId }}-{{ $etapa->id }}">Histórico</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#anexos-{{ $grupoId }}-{{ $etapa->id }}">Anexos</a></li>
                                    </ul>

                                    <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white">
                                        <div class="tab-pane fade show active" id="detalhes-{{ $grupoId }}-{{ $etapa->id }}">
                                            <div class="mb-3">
                                                <label class="font-weight-bold">Observações atuais</label>
                                                <div class="border rounded bg-light p-3">{!! nl2br(e($etapa->observacoes ?: 'Sem observações.')) !!}</div>
                                            </div>

                                            @if($canAct)
                                                @if($isLockedVisual)
                                                    <div class="alert alert-secondary"><i class="fas fa-lock mr-1"></i> Edição indisponível enquanto a etapa estiver bloqueada.</div>
                                                @elseif(in_array($etapa->status_normalizado, ['APROVADA', 'CANCELADA'], true) && !$canManageEtapas)
                                                    <div class="alert alert-info">Esta etapa já foi finalizada e está disponível apenas para consulta.</div>
                                                @else
                                                    <form class="form-etapa-modal" action="{{ route('processos-contratacao.etapas.update', $etapa) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="_acao" value="salvar">
                                                        <div class="form-group">
                                                            <label>Observações</label>
                                                            <textarea name="observacoes" class="form-control" rows="6">{{ $etapa->observacoes }}</textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Prazo da etapa</label>
                                                            <input type="date" name="data_limite" class="form-control" value="{{ optional($etapa->data_limite)->format('Y-m-d') }}" {{ ($canEdit || $canManageEtapas) ? '' : 'readonly' }}>
                                                            <small class="text-muted">A data de início é automática e não pode ser alterada.</small>
                                                        </div>
                                                        @if($etapa->permite_anexo)
                                                            <div class="form-group">
                                                                <label>Anexar arquivo</label>
                                                                <input type="file" name="anexo" class="form-control-file">
                                                            </div>
                                                        @endif
                                                        <div class="d-flex flex-wrap mt-4">
                                                            @if($canEdit || $canManageEtapas)
                                                                <button type="submit" data-acao="salvar" class="btn btn-primary mr-2 mb-2"><i class="fas fa-save mr-1"></i> Salvar</button>
                                                            @endif
                                                            @if($canApprove || $canManageEtapas)
                                                                <button type="submit" data-acao="aprovar" class="btn btn-success mr-2 mb-2"><i class="fas fa-check mr-1"></i> Aprovar / concluir</button>
                                                            @endif
                                                            @if($canReprove || $canManageEtapas)
                                                                <button type="submit" data-acao="reprovar" class="btn btn-warning mr-2 mb-2"><i class="fas fa-times mr-1"></i> Reprovar</button>
                                                            @endif
                                                            @if($canCancelar)
                                                                <button type="submit" data-acao="cancelar" class="btn btn-danger mb-2"><i class="fas fa-ban mr-1"></i> Cancelar etapa</button>
                                                            @endif
                                                        </div>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="historico-{{ $grupoId }}-{{ $etapa->id }}">
                                            @if($etapa->historicos->count())
                                                @foreach($etapa->historicos as $hist)
                                                    <div class="timeline-item-etapa">
                                                        <div class="timeline-title">{{ $hist->acao }}</div>
                                                        <div class="timeline-meta">{{ optional($hist->created_at)->format('d/m/Y H:i') }} · {{ optional($hist->usuario)->name ?: 'Sistema' }}</div>
                                                        <div class="timeline-desc">{{ $hist->descricao }}</div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-muted">Sem histórico nesta etapa.</div>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="anexos-{{ $grupoId }}-{{ $etapa->id }}">
                                            @if($etapa->anexos->count())
                                                @foreach($etapa->anexos as $anexo)
                                                    <div class="anexo-item">
                                                        <div>
                                                            <div class="font-weight-bold">{{ $anexo->nome_original }}</div>
                                                            <div class="small text-muted">{{ optional($anexo->usuario)->name ?: 'Sistema' }} · {{ optional($anexo->created_at)->format('d/m/Y H:i') }}</div>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $anexo->arquivo) }}" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-muted">Sem anexos nesta etapa.</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><p class="text-muted mb-0">Nenhuma etapa encontrada.</p></div>
    @endforelse
</div>

@once
@push('css')
<style>
.etapas-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:18px}.etapa-card{--etapa-cor:#6c757d;background:#fff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.06);transition:all .2s ease;position:relative}.etapa-card:hover{transform:translateY(-2px);box-shadow:0 12px 28px rgba(15,23,42,.10)}.etapa-card::before{content:'';display:block;height:6px;background:var(--etapa-cor)}.etapa-card.is-blocked{background:#f3f4f6;border-color:#d1d5db;filter:grayscale(.12)}.etapa-card.is-blocked::before{background:#9ca3af}.etapa-card.is-late{border-color:#dc3545;box-shadow:0 0 0 1px rgba(220,53,69,.15),0 8px 24px rgba(220,53,69,.08)}.etapa-card-top{padding:18px 18px 10px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.etapa-index{display:inline-block;font-size:12px;color:#6b7280;margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em}.etapa-title{font-size:1.15rem;font-weight:700;line-height:1.25;color:#111827}.etapa-status-badge{font-size:.75rem;padding:.45rem .65rem;border-radius:999px}.etapa-card-body{padding:0 18px 12px}.etapa-meta-list{display:grid;grid-template-columns:1fr 1fr;gap:12px}.meta-item-wide{grid-column:1 / -1}.etapa-meta-item{background:#f8fafc;border:1px solid #edf2f7;border-radius:12px;padding:10px 12px}.etapa-card.is-blocked .etapa-meta-item{background:#e5e7eb;border-color:#d1d5db}.meta-label{display:block;font-size:11px;color:#6b7280;text-transform:uppercase;margin-bottom:4px;letter-spacing:.03em}.meta-value{font-size:14px;font-weight:600;color:#111827}.etapa-alert{margin-top:12px;padding:10px 12px;border-radius:12px;font-size:.9rem;font-weight:600}.etapa-alert-danger{background:#fef2f2;color:#b91c1c}.etapa-alert-secondary{background:#eceff3;color:#4b5563}.etapa-alert-warning{background:#fff7ed;color:#b45309}.etapa-card-footer{padding:14px 18px 18px}.modal-etapa .modal-header{background:#fff}.info-box-etapa{background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:16px}.ibox-item + .ibox-item{margin-top:14px;padding-top:14px;border-top:1px solid #e5e7eb}.ibox-label{display:block;font-size:12px;color:#6b7280;text-transform:uppercase;margin-bottom:4px}.timeline-item-etapa{padding:12px 0;border-bottom:1px solid #eef2f7}.timeline-item-etapa:last-child{border-bottom:none}.timeline-title{font-weight:700;color:#111827;text-transform:capitalize}.timeline-meta{font-size:12px;color:#6b7280;margin-bottom:4px}.timeline-desc{color:#374151}.anexo-item{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #eef2f7}.anexo-item:last-child{border-bottom:none}@media (max-width:991px){.etapa-meta-list{grid-template-columns:1fr}.meta-item-wide{grid-column:auto}}
</style>
@endpush

@push('js')
<script>
$(document).on('click', '.form-etapa-modal button[type="submit"]', function(){
    const acao = $(this).data('acao') || 'salvar';
    $(this).closest('form').find('input[name="_acao"]').val(acao);
});
$(document).on('submit', '.form-etapa-modal', function(e){
    e.preventDefault();
    const form=this; const formData=new FormData(form);
    $.ajax({url:$(form).attr('action'),method:'POST',data:formData,processData:false,contentType:false,headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content'),'X-HTTP-Method-Override':'PUT'},success:function(resp){Swal.fire('Sucesso', resp.message || 'Etapa atualizada com sucesso.', 'success').then(function(){window.location.reload();});},error:function(xhr){let msg='Falha ao processar a etapa.'; if(xhr.responseJSON && xhr.responseJSON.message){msg=xhr.responseJSON.message;} Swal.fire('Erro', msg, 'error');}})
});
</script>
@endpush
@endonce
