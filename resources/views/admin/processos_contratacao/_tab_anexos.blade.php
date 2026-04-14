<div class="row">
    <div class="col-md-5">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Novo Anexo</h3></div>
            <form action="{{ route('processos-contratacao.anexos.store', $processo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo do Anexo *</label>
                        <select name="tipo_anexo" id="tipo_anexo" class="form-control" required>
                            <option value="PROPOSTA_PDF">Proposta PDF</option>
                            <option value="PLANILHA_SERVICOS">Planilha de Serviços</option>
                            <option value="CONTRATO_ASSINADO">Contrato Assinado</option>
                            <option value="ADITIVO">Aditivo</option>
                            <option value="OUTRO">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Arquivo *</label>
                        <input type="file"
                            name="arquivo"
                            id="arquivo_anexo"
                            class="form-control"
                            accept=".pdf,.xls,.xlsx,.csv">
                    </div>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" rows="4" class="form-control"></textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    @can('edit processos contratacao')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload mr-1"></i> Enviar Anexo
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Arquivos do Processo</h3>
                @can('edit processos contratacao')
                    <button type="button"
                            class="btn btn-primary btn-sm"
                            id="btn-extrair-documentos"
                            data-url="{{ route('processos-contratacao.extrair-dados', $processo) }}">
                        <i class="fas fa-robot"></i> Ler Proposta / Planilha
                    </button>
                @endcan
            </div>
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Arquivo</th>
                            <th>Data</th>
                            <th>Extração</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processo->anexos as $anexo)
                            <tr>
                                <td><span class="badge badge-info">{{ $anexo->tipo_anexo }}</span></td>
                                <td>{{ $anexo->nome_original }}</td>
                                <td>{{ $anexo->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($anexo->extraido_com_sucesso)
                                        <span class="badge badge-success">Processado</span>
                                    @else
                                        <span class="badge badge-secondary">Pendente</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('processos-contratacao.anexos.download', [$processo, $anexo]) }}" class="btn btn-xs btn-outline-info">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    @can('edit processos contratacao')
                                        <form action="{{ route('processos-contratacao.anexos.destroy', [$processo, $anexo]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Excluir anexo?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Nenhum anexo cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="extracao-preview-container" class="mt-3"></div>

                @if(!empty($processo->dados_extraidos_json))
                    <hr>
                    <h5 class="mb-3">Pré-leitura dos documentos</h5>
                    <pre class="p-3 bg-light border rounded" style="max-height: 280px; overflow:auto;">{{ json_encode($processo->dados_extraidos_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// $(function() {
//     $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

//     $('#btn-extrair-documentos').on('click', function() {
//         let btn = $(this);
//         let url = btn.data('url');

//         btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
//         $.post(url, {_token: '{{ csrf_token() }}'})
//             .done(function(resp) {
//                 Swal.fire('Sucesso!', resp.message || 'Leitura executada com sucesso.', 'success')
//                     .then(() => window.location.reload());
//             })
//             .fail(function(xhr) {
//                 Swal.fire('Erro!', xhr.responseJSON?.message || 'Falha ao processar documentos.', 'error');
//             })
//             .always(function() {
//                 btn.prop('disabled', false).html('<i class="fas fa-robot"></i> Ler Proposta / Planilha');
//             });
//     });
// });

$(function () {
    $('#btn-extrair-documentos').on('click', function () {
        const btn = $(this);
        const url = btn.data('url');

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Lendo...');

        $.ajax({
            url: url,
            type: 'POST',
            data: {},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
                $('#extracao-preview-container').html(resp.html);
                Swal.fire('Sucesso', resp.message, 'success');
            },
            error: function (xhr) {
                Swal.fire(
                    'Erro',
                    xhr.responseJSON?.message || 'Não foi possível extrair os dados do anexo.',
                    'error'
                );
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-robot"></i> Ler Proposta / Planilha');
            }
        });
    });
});

$(function () {
    function atualizarAcceptAnexo() {
        const tipo = $('#tipo_anexo').val();
        const input = $('#arquivo_anexo');

        if (tipo === 'PROPOSTA_PDF' || tipo === 'CONTRATO_ASSINADO') {
            input.attr('accept', '.pdf');
        } else if (tipo === 'PLANILHA_SERVICOS') {
            input.attr('accept', '.xls,.xlsx,.csv');
        } else {
            input.attr('accept', '.pdf,.xls,.xlsx,.csv,.doc,.docx');
        }
    }

    $('#tipo_anexo').on('change', atualizarAcceptAnexo);
    atualizarAcceptAnexo();
});

</script>
@endpush
