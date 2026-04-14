<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-paperclip"></i> Upload de documento</h3>
    </div>
    <div class="card-body">
        <form id="form-anexo-processo" action="{{ route('processos-contratacao.anexos.store', $processo) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo do anexo</label>
                        <select name="tipo_anexo" class="form-control" required>
                            <option value="PROPOSTA_PDF">Proposta PDF</option>
                            <option value="PLANILHA_SERVICOS">Planilha de Serviços</option>
                            <option value="MINUTA">Minuta</option>
                            <option value="CONTRATO_ASSINADO">Contrato Assinado</option>
                            <option value="ADITIVO">Aditivo</option>
                            <option value="OUTRO">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Arquivo</label>
                        <input type="file" name="arquivo" id="arquivo_anexo" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="d-block">&nbsp;</label>
                        <div class="form-check">
                            <input type="checkbox" name="executar_extracao" value="1" class="form-check-input" id="executar_extracao" checked>
                            <label class="form-check-label" for="executar_extracao">Executar extração</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="importar_itens" value="1" class="form-check-input" id="importar_itens">
                            <label class="form-check-label" for="importar_itens">Importar itens sugeridos</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Observações</label>
                <textarea name="observacoes" class="form-control" rows="3"></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-info" id="btn-preview-extracao"><i class="fas fa-search"></i> Pré-analisar documento</button>
                <button type="submit" class="btn btn-success" id="btn-upload-anexo"><i class="fas fa-upload"></i> Enviar anexo</button>
            </div>
        </form>
        <div id="extracao-preview-container" class="mt-4"></div>
    </div>
</div>
