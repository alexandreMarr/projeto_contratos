<div class="modal fade" id="modalEditarEtapa" tabindex="-1" role="dialog" aria-labelledby="modalEditarEtapaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="form-editar-etapa">
                @csrf
                @method('PUT')

                <input type="hidden" id="modal_etapa_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEtapaLabel">Atualizar Etapa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="$('#modalEditarEtapa').modal('hide');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" id="modal_etapa_status" class="form-control" required>
                                    <option value="PENDENTE">Pendente</option>
                                    <option value="EM_ANDAMENTO">Em Andamento</option>
                                    <option value="AGUARDANDO">Aguardando</option>
                                    <option value="CONCLUIDA">Concluída</option>
                                    <option value="REPROVADA">Reprovada</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data Início</label>
                                <input type="date" name="data_inicio" id="modal_etapa_data_inicio" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data Limite</label>
                                <input type="date" name="data_limite" id="modal_etapa_data_limite" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Data Conclusão</label>
                                <input type="date" name="data_conclusao" id="modal_etapa_data_conclusao" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Responsável</label>
                                <select name="responsavel_user_id" id="modal_etapa_responsavel_user_id" class="form-control">
                                    <option value="">Selecione</option>
                                    @isset($usuarios)
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                        @endforeach
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Parecer</label>
                        <textarea name="parecer" id="modal_etapa_parecer" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label>Observações</label>
                        <textarea name="observacoes" id="modal_etapa_observacoes" rows="4" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Atualização
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
