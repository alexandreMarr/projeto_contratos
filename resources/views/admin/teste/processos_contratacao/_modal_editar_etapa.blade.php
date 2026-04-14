<div class="modal fade" id="modalEditarEtapa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-editar-etapa" onsubmit="return false;">
                @csrf
                @method('PUT')
                <input type="hidden" id="etapa-edit-url">

                <div class="modal-header">
                    <h5 class="modal-title">Atualizar Etapa <span id="etapa-edit-titulo"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="$('#modalEditarEtapa').modal('hide');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="etapa-alert-container"></div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select id="etapa-status" name="status" class="form-control">
                                    <option value="PENDENTE">Pendente</option>
                                    <option value="EM_ANDAMENTO">Em Andamento</option>
                                    <option value="AGUARDANDO">Aguardando</option>
                                    <option value="CONCLUIDA">Concluída</option>
                                    <option value="REPROVADA">Reprovada</option>
                                    <option value="CANCELADA">Cancelada</option>
                                    <option value="ATRASADA">Atrasada</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data Limite</label>
                                <input type="date" id="etapa-data-limite" name="data_limite" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data Conclusão</label>
                                <input type="date" id="etapa-data-conclusao" name="data_conclusao" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observações</label>
                        <textarea id="etapa-observacoes" name="observacoes" rows="5" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    @can('manage etapas processos contratacao')
                        <button type="button" id="btn-salvar-etapa" class="btn btn-success">
                            <i class="fa fa-save"></i> Salvar Alterações
                        </button>
                    @endcan
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalEditarEtapa').modal('hide');">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>
