@php
    $valorContratoAtual = (float) $processo->valor_contratual_atual;
    $sequenciaAditivo = $processo->aditivos->count() + 1;
    $baseDocumento = strtoupper(str_replace([' ', '/'], '-', $processo->numero_contrato_assinado ?: $processo->numero_processo_interno ?: ('PC-' . str_pad((string) $processo->id, 6, '0', STR_PAD_LEFT))));
    $numeroDocumentoPreview = 'ADT-' . $baseDocumento . '-' . str_pad((string) $sequenciaAditivo, 3, '0', STR_PAD_LEFT);
    $bloqueadoAditivo = !$processo->contrato_etapas_concluidas;
@endphp

<div class="row">
    <div class="col-md-5">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Novo Aditivo</h3>
                <span class="badge badge-primary">Mesma empresa do contrato</span>
            </div>

            <form action="{{ route('processos-contratacao.aditivos.store', $processo) }}" method="POST" id="form-aditivo">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info py-2">
                        <strong>Valor contratual atual:</strong>
                        R$ {{ number_format($valorContratoAtual, 2, ',', '.') }}
                    </div>

                    @if($bloqueadoAditivo)
                        <div class="alert alert-warning">
                            <i class="fas fa-lock mr-1"></i>
                            Você só poderá cadastrar aditivo quando <strong>todas as etapas do contrato</strong> estiverem aprovadas/concluídas.
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Título do aditivo *</label>
                        <input type="text" name="titulo" class="form-control" required value="{{ old('titulo') }}" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label>Nº Documento</label>
                        <input type="text" class="form-control" value="{{ $numeroDocumentoPreview }}" readonly>
                        <small class="text-muted">Gerado automaticamente com base no contrato e na sequência do aditivo.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de início do aditivo</label>
                                <input type="date" name="data_referencia" class="form-control" value="{{ old('data_referencia') }}" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Boletim de medição</label>
                                <input type="text" name="boletim_medicao" class="form-control" placeholder="Ex.: BM-04/2026" value="{{ old('boletim_medicao') }}" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Objeto do aditivo *</label>
                        <textarea name="objeto" rows="4" class="form-control" required {{ $bloqueadoAditivo ? 'disabled' : '' }}>{{ old('objeto') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Escopo do aditivo *</label>
                        <textarea name="escopo" rows="4" class="form-control" required {{ $bloqueadoAditivo ? 'disabled' : '' }}>{{ old('escopo') }}</textarea>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header py-2"><strong>Situação do contrato anterior</strong></div>
                        <div class="card-body pb-2">
                            <div class="form-group mb-3">
                                <label>Todo o valor do contrato já foi realizado? *</label>
                                <select name="contrato_realizado_total" id="contrato_realizado_total" class="form-control" required {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                                    <option value="1" @selected(old('contrato_realizado_total') === '1')>Sim</option>
                                    <option value="0" @selected(old('contrato_realizado_total', '0') === '0')>Não</option>
                                </select>
                            </div>

                            <div class="form-group valor-medicao-wrap">
                                <label>Valor executado pelo boletim de medição</label>
                                <input type="text" name="valor_executado_medicao" id="valor_executado_medicao" class="form-control js-money"
                                       value="{{ old('valor_executado_medicao') }}" placeholder="0,00" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Valor do contrato anterior</label>
                                        <input type="text" id="valor_anterior_exibicao" class="form-control" value="R$ {{ number_format($valorContratoAtual, 2, ',', '.') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Saldo do contrato anterior</label>
                                        <input type="text" id="saldo_contrato_anterior_exibicao" class="form-control" value="R$ {{ number_format($valorContratoAtual, 2, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border mb-3">
                        <div class="card-header py-2"><strong>Dados financeiros do aditivo</strong></div>
                        <div class="card-body pb-2">
                            <div class="form-group">
                                <label>Valor do aditivo *</label>
                                <input type="text" name="valor_aditivo" id="valor_aditivo" class="form-control js-money" required value="{{ old('valor_aditivo') }}" placeholder="0,00" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Novo valor do contrato</label>
                                        <input type="text" id="novo_valor_contrato_exibicao" class="form-control" value="R$ {{ number_format($valorContratoAtual, 2, ',', '.') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>% do aditivo sobre o contrato</label>
                                        <input type="text" id="percentual_aditivo_exibicao" class="form-control" value="0,0000%" readonly>
                                    </div>
                                </div>
                            </div>

                            <div id="alerta_percentual_aditivo" class="alert alert-warning d-none mb-0">
                                Este aditivo ultrapassa <strong>30%</strong> do valor do contrato e deve passar pelo conselho para aprovação.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data fim contrato atual</label>
                                <input type="date" name="vigencia_anterior_fim" class="form-control" value="{{ old('vigencia_anterior_fim', optional($processo->vigencia_fim)->format('Y-m-d')) }}" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nova data fim após aditivo</label>
                                <input type="date" name="vigencia_nova_fim" class="form-control" value="{{ old('vigencia_nova_fim') }}" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipo *</label>
                        <select name="tipo" class="form-control select2" required {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            <option value="ADITIVO_VALOR" @selected(old('tipo') === 'ADITIVO_VALOR')>Aditivo de Valor</option>
                            <option value="ADITIVO_PRAZO" @selected(old('tipo') === 'ADITIVO_PRAZO')>Aditivo de Prazo</option>
                            <option value="REAJUSTE" @selected(old('tipo') === 'REAJUSTE')>Reajuste</option>
                            <option value="OUTRO" @selected(old('tipo') === 'OUTRO')>Outro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" rows="3" class="form-control" {{ $bloqueadoAditivo ? 'disabled' : '' }}>{{ old('observacoes') }}</textarea>
                    </div>
                </div>

                <div class="card-footer text-right">
                    @can('edit processos contratacao')
                        <button type="submit" class="btn btn-success" {{ $bloqueadoAditivo ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i> Salvar
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title mb-0">Histórico de Aditivos</h3></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Documento</th>
                                <th>Financeiro</th>
                                <th>Etapas</th>
                                <th>Status legal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($processo->aditivos->sortByDesc('created_at') as $aditivo)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $aditivo->titulo ?: ($aditivo->descricao ?: '-') }}</div>
                                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($aditivo->objeto ?: $aditivo->escopo ?: '', 90) }}</div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $aditivo->numero_documento ?: '-' }}</div>
                                        <div class="small text-muted">Início: {{ optional($aditivo->data_referencia)->format('d/m/Y') ?: '-' }}</div>
                                        <div class="small text-muted">Fim atual: {{ optional($aditivo->vigencia_anterior_fim)->format('d/m/Y') ?: '-' }}</div>
                                        <div class="small text-muted">Novo fim: {{ optional($aditivo->vigencia_nova_fim)->format('d/m/Y') ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div><strong>Anterior:</strong> R$ {{ number_format((float) $aditivo->valor_anterior, 2, ',', '.') }}</div>
                                        <div><strong>Executado:</strong> R$ {{ number_format((float) $aditivo->valor_executado_medicao, 2, ',', '.') }}</div>
                                        <div><strong>Saldo:</strong> R$ {{ number_format((float) $aditivo->saldo_contrato_anterior, 2, ',', '.') }}</div>
                                        <div><strong>Aditivo:</strong> R$ {{ number_format((float) $aditivo->valor_aditivo, 2, ',', '.') }}</div>
                                        <div><strong>Novo contrato:</strong> R$ {{ number_format((float) $aditivo->valor_novo, 2, ',', '.') }}</div>
                                        <div><strong>%:</strong> {{ $aditivo->percentual_exibicao }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $aditivo->etapas->count() }} etapa(s)</span>
                                        <div class="small text-muted mt-1">
                                            Ordens: {{ $aditivo->etapas->pluck('ordem')->implode(', ') ?: '1, 2, 4, 5, 7' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $aditivo->status_legal_badge_class }}">{{ $aditivo->status_legal }}</span>
                                        <div class="small text-muted mt-1">
                                            {{ $aditivo->exige_aprovacao_conselho ? 'Exige aprovação do conselho' : 'Dentro do limite legal' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Nenhum aditivo lançado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
(function () {
    const valorContratoAtual = {{ json_encode(round($valorContratoAtual, 2)) }};

    function parseMoneyBR(value) {
        if (!value) return 0;
        value = String(value).replace(/\s/g, '').replace('R$', '');
        if (value.includes('.') && value.includes(',')) {
            value = value.replace(/\./g, '').replace(',', '.');
        } else if (value.includes(',')) {
            value = value.replace(',', '.');
        }
        const num = parseFloat(value);
        return isNaN(num) ? 0 : num;
    }

    function formatMoneyBR(value) {
        return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
    }

    function formatPercentBR(value) {
        if (!isFinite(value)) return '∞';
        return value.toLocaleString('pt-BR', { minimumFractionDigits: 4, maximumFractionDigits: 4 }) + '%';
    }

    function applyMoneyMask(input) {
        if (!input) return;
        input.addEventListener('input', function () {
            let digits = this.value.replace(/\D/g, '');
            if (!digits) {
                this.value = '';
                recalcAditivo();
                return;
            }
            const number = parseFloat(digits) / 100;
            this.value = number.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            recalcAditivo();
        });
    }

    function recalcAditivo() {
        const realizadoTotalEl = document.getElementById('contrato_realizado_total');
        const valorExecutadoInput = document.getElementById('valor_executado_medicao');
        const valorAditivoInput = document.getElementById('valor_aditivo');
        const alerta = document.getElementById('alerta_percentual_aditivo');

        if (!realizadoTotalEl || !valorExecutadoInput || !valorAditivoInput || !alerta) return;

        const realizadoTotal = realizadoTotalEl.value === '1';
        let valorExecutado = realizadoTotal ? valorContratoAtual : parseMoneyBR(valorExecutadoInput.value);
        if (valorExecutado > valorContratoAtual) valorExecutado = valorContratoAtual;

        const saldo = Math.max(valorContratoAtual - valorExecutado, 0);
        const valorAditivo = parseMoneyBR(valorAditivoInput.value);
        const novoValor = valorContratoAtual + valorAditivo;
        const percentual = valorContratoAtual > 0 ? ((valorAditivo / valorContratoAtual) * 100) : (valorAditivo > 0 ? Infinity : 0);

        document.getElementById('valor_anterior_exibicao').value = formatMoneyBR(valorContratoAtual);
        document.getElementById('saldo_contrato_anterior_exibicao').value = formatMoneyBR(saldo);
        document.getElementById('novo_valor_contrato_exibicao').value = formatMoneyBR(novoValor);
        document.getElementById('percentual_aditivo_exibicao').value = formatPercentBR(percentual);

        if (percentual > 30 || !isFinite(percentual)) {
            alerta.classList.remove('d-none');
        } else {
            alerta.classList.add('d-none');
        }

        if (realizadoTotal) {
            valorExecutadoInput.value = formatMoneyBR(valorContratoAtual).replace('R$', '').trim();
            document.querySelector('.valor-medicao-wrap').classList.add('d-none');
        } else {
            document.querySelector('.valor-medicao-wrap').classList.remove('d-none');
        }
    }

    document.querySelectorAll('.js-money').forEach(applyMoneyMask);
    document.getElementById('contrato_realizado_total')?.addEventListener('change', recalcAditivo);
    recalcAditivo();
})();
</script>
@endpush
