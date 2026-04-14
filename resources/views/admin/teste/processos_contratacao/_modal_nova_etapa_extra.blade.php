<div class="modal fade" id="modalNovaEtapaExtra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('processos-contratacao.etapas.store-extra', $processo) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nova Etapa Extra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="$('#modalNovaEtapaExtra').modal('hide');">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7"><div class="form-group"><label>Nome da Etapa *</label><input type="text" name="nome_etapa" class="form-control" required></div></div>
                        <div class="col-md-2"><div class="form-group"><label>Ordem *</label><input type="number" name="ordem" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>SLA *</label><input type="number" name="prazo_limite_dias" class="form-control" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Setor Responsável</label><input type="text" name="setor_responsavel" class="form-control"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Status</label>
                            <select name="status" class="form-control">
                                <option value="PENDENTE">Pendente</option>
                                <option value="EM_ANDAMENTO">Em Andamento</option>
                                <option value="AGUARDANDO">Aguardando</option>
                            </select>
                        </div></div>
                    </div>
                    <div class="form-group"><label>Observações</label><textarea name="observacoes" rows="4" class="form-control"></textarea></div>
                </div>

                <div class="modal-footer">
                    @can('manage etapas processos contratacao')
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar Etapa</button>
                    @endcan
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalNovaEtapaExtra').modal('hide');">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>
