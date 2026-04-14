@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary mb-3">
            <div class="card-header"><h3 class="card-title">Dados do Processo</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nº Processo Interno</label>
                            <input type="text"
                                class="form-control"
                                value="{{ $processo->numero_processo_interno ?? 'Gerado automaticamente ao salvar' }}"
                                readonly>

                            <input type="hidden"
                                name="numero_processo_interno"
                                value="{{ old('numero_processo_interno', $processo->numero_processo_interno ?? '') }}">
                            <small class="text-muted">O número será gerado automaticamente pelo sistema.</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                                   value="{{ old('titulo', $processo->titulo ?? '') }}" required>
                            @error('titulo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Objeto Resumido *</label>
                    <input type="text" name="objeto_resumido" class="form-control @error('objeto_resumido') is-invalid @enderror"
                           value="{{ old('objeto_resumido', $processo->objeto_resumido ?? '') }}" required>
                    @error('objeto_resumido') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Escopo Detalhado</label>
                    <textarea name="escopo_detalhado" rows="5" class="form-control">{{ old('escopo_detalhado', $processo->escopo_detalhado ?? '') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Empresa Contratante *</label>
                            <select name="empresa_contratante_id" class="form-control select2 @error('empresa_contratante_id') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                @foreach($contratantes as $empresa)
                                    <option value="{{ $empresa->id }}" @selected((string) old('empresa_contratante_id', $processo->empresa_contratante_id ?? $empresaContratantePadraoId ?? '') === (string) $empresa->id)>
                                        {{ $empresa->razao_social }}
                                    </option>
                                @endforeach
                            </select>
                            @error('empresa_contratante_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Empresa Contratada *</label>
                            <select name="empresa_contratada_id" class="form-control select2 @error('empresa_contratada_id') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                @foreach($contratadas as $empresa)
                                    <option value="{{ $empresa->id }}" @selected((string) old('empresa_contratada_id', $processo->empresa_contratada_id ?? request('empresa_contratada_id')) === (string) $empresa->id)>
                                        {{ $empresa->razao_social }}
                                    </option>
                                @endforeach
                            </select>
                            @error('empresa_contratada_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo Contratação *</label>
                            <select name="tipo_contratacao" class="form-control select2" required>
                                <option value="">Selecione</option>
                                <option value="NOVO_CONTRATO" @selected(old('tipo_contratacao', $processo->tipo_contratacao ?? '') === 'NOVO_CONTRATO')>
                                    Novo Contrato
                                </option>
                                <option value="ADITIVO" @selected(old('tipo_contratacao', $processo->tipo_contratacao ?? '') === 'ADITIVO')>
                                    Aditivo
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4"><div class="form-group"><label>Categoria</label><input type="text" name="categoria" class="form-control" value="{{ old('categoria', $processo->categoria ?? '') }}"></div></div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" class="form-control select2" required>
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status }}" @selected(old('status', $processo->status ?? 'RASCUNHO') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-info mb-3">
            <div class="card-header"><h3 class="card-title">Financeiro e Prazos</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>Valor Estimado</label><input type="number" step="0.01" name="valor_estimado" class="form-control" value="{{ old('valor_estimado', $processo->valor_estimado ?? '') }}"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Valor Proposto</label><input type="number" step="0.01" name="valor_proposto" class="form-control" value="{{ old('valor_proposto', $processo->valor_proposto ?? '') }}"></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Valor Aprovado Final</label><input type="number" step="0.01" name="valor_aprovado_final" class="form-control" value="{{ old('valor_aprovado_final', $processo->valor_aprovado_final ?? '') }}"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Data Solicitação</label><input type="date" name="data_solicitacao" class="form-control" value="{{ old('data_solicitacao', optional($processo->data_solicitacao ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Recebimento Proposta</label><input type="date" name="data_recebimento_proposta" class="form-control" value="{{ old('data_recebimento_proposta', optional($processo->data_recebimento_proposta ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Validade Proposta</label><input type="date" name="validade_proposta" class="form-control" value="{{ old('validade_proposta', optional($processo->validade_proposta ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Prazo Pagamento (dias)</label><input type="number" name="prazo_pagamento_dias" class="form-control" value="{{ old('prazo_pagamento_dias', $processo->prazo_pagamento_dias ?? '') }}"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><div class="form-group"><label>Execução Início</label><input type="date" name="prazo_execucao_inicio" class="form-control" value="{{ old('prazo_execucao_inicio', optional($processo->prazo_execucao_inicio ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Execução Fim</label><input type="date" name="prazo_execucao_fim" class="form-control" value="{{ old('prazo_execucao_fim', optional($processo->prazo_execucao_fim ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Vigência Início</label><input type="date" name="vigencia_inicio" class="form-control" value="{{ old('vigencia_inicio', optional($processo->vigencia_inicio ?? null)->format('Y-m-d')) }}"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Vigência Fim</label><input type="date" name="vigencia_fim" class="form-control" value="{{ old('vigencia_fim', optional($processo->vigencia_fim ?? null)->format('Y-m-d')) }}"></div></div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Complementares</h3></div>
            <div class="card-body">
                <div class="row">
                    {{-- <div class="col-md-4"><div class="form-group"><label>Nº Contrato Assinado</label><input type="text" name="numero_contrato_assinado" class="form-control" value="{{ old('numero_contrato_assinado', $processo->numero_contrato_assinado ?? '') }}"></div></div> --}}
                    <div class="col-md-4"><div class="form-group"><label>Prioridade</label><input type="text" name="prioridade" class="form-control" value="{{ old('prioridade', $processo->prioridade ?? '') }}"></div></div>
                    {{-- <div class="col-md-4"><div class="form-group"><label>Origem</label><input type="text" name="origem" class="form-control" value="{{ old('origem', $processo->origem ?? '') }}"></div></div> --}}
                </div>
                <div class="form-group">
                    <label>Observações</label>
                    <textarea name="observacoes" rows="5" class="form-control">{{ old('observacoes', $processo->observacoes ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success mb-3">
            <div class="card-header">
                <h3 class="card-title">Leitura Inteligente Inicial</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Proposta PDF</label>
                    <input type="file" name="proposta_pdf" id="arquivo_proposta_pdf" class="form-control" accept=".pdf">
                    <small class="text-muted">Anexe a proposta para sugerir o preenchimento automático.</small>
                </div>

                <div class="form-group">
                    <label>Planilha de Serviços</label>
                    <input type="file" name="planilha_servicos" id="arquivo_planilha_servicos" class="form-control" accept=".xls,.xlsx,.csv">
                    <small class="text-muted">Opcional. Ajuda a sugerir itens e valores.</small>
                </div>

                <button type="button" id="btn-analisar-documentos" class="btn btn-info btn-block">
                    <i class="fas fa-search"></i> Ler proposta e sugerir preenchimento
                </button>
            </div>
        </div>

        <div id="preview-extracao-container" class="mb-3"></div>

        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Ações</h3></div>
            <div class="card-body">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-save mr-1"></i> Salvar Processo
                </button>
                <a href="{{ route('processos-contratacao.index') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
                @isset($processo)
                    <a href="{{ route('processos-contratacao.show', $processo) }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-eye mr-1"></i> Visualizar
                    </a>
                @endisset
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .extracao-table th,
    .extracao-table td {
        vertical-align: top;
        word-break: break-word;
        white-space: normal;
    }

    .extracao-resumo-box {
        max-height: 220px;
        overflow-y: auto;
        overflow-x: hidden;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .extracao-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    #preview-extracao-container .table-responsive {
        overflow-x: auto;
    }

    #preview-extracao-container {
        word-break: break-word;
    }
</style>
@endpush

@push('js')
<script>
$(function() {
    $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
});

$(function () {
    $('#btn-analisar-documentos').on('click', function () {
        const proposta = $('#arquivo_proposta_pdf')[0].files[0];

        if (!proposta) {
            Swal.fire('Atenção', 'Selecione ao menos a proposta em PDF.', 'warning');
            return;
        }

        let formData = new FormData();
        formData.append('tipo_anexo', 'PROPOSTA_PDF');
        formData.append('arquivo', proposta);
        formData.append('executar_extracao', 1);

        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Lendo documento...');

        $.ajax({
            url: "{{ route('processos-contratacao.documentos.preview-extracao') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
                $('#preview-extracao-container').html(resp.html);

                const dados = resp.resultado?.dados || {};

                if (dados.objeto_resumido) $('input[name="objeto_resumido"]').val(dados.objeto_resumido);
                if (dados.valor_proposto) $('input[name="valor_proposto"]').val(dados.valor_proposto);
                if (dados.prazo_pagamento_dias) $('input[name="prazo_pagamento_dias"]').val(dados.prazo_pagamento_dias);
                if (dados.prazo_execucao_inicio) $('input[name="prazo_execucao_inicio"]').val(dados.prazo_execucao_inicio);
                if (dados.prazo_execucao_fim) $('input[name="prazo_execucao_fim"]').val(dados.prazo_execucao_fim);

                if (dados.objeto_resumido && !$('input[name="titulo"]').val()) {
                    $('input[name="titulo"]').val(dados.objeto_resumido);
                }

                if (dados.locais && dados.locais.length && !$('textarea[name="escopo_detalhado"]').val()) {
                    $('textarea[name="escopo_detalhado"]').val(dados.locais.join("\n"));
                }

                Swal.fire('Sucesso', 'Leitura concluída. Revise os dados sugeridos.', 'success');
            },
            error: function () {
                Swal.fire('Erro', 'Não foi possível analisar o documento.', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-search"></i> Ler proposta e sugerir preenchimento');
            }
        });
    });
});
</script>
@endpush
