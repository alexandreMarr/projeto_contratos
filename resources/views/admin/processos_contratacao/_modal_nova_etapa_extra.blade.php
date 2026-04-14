<div class="modal fade" id="modalNovaEtapaExtra" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('processos-contratacao.etapas-extra.store') }}" method="POST">
                @csrf
                <input type="hidden" name="processo_contratacao_id" value="{{ $processo->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">Nova Etapa Extra</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Origem da Etapa *</label>
                                <select name="origem_tipo" id="origem_tipo_etapa_extra" class="form-control">
                                    <option value="CONTRATO">Contrato</option>
                                    <option value="ADITIVO">Aditivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 origem-aditivo-extra d-none">
                            <div class="form-group">
                                <label>Aditivo</label>
                                <select name="processo_aditivo_id" class="form-control select2" data-placeholder="Selecione o aditivo">
                                    <option value=""></option>
                                    @foreach($processo->aditivos as $aditivo)
                                        <option value="{{ $aditivo->id }}">{{ $aditivo->numero_documento ?: '#' . $aditivo->id }} - {{ \Illuminate\Support\Str::limit($aditivo->descricao, 70) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Nome da Etapa *</label>
                                <input type="text" name="nome_etapa" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Ordem *</label>
                                <input type="number" name="ordem" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>SLA</label>
                                <input type="number" name="prazo_limite_dias" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Setor Responsável</label>
                                <input type="text" name="setor_responsavel" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="BLOQUEADA">Bloqueada</option>
                                    <option value="LIBERADA">Liberada</option>
                                    <option value="EM_ANDAMENTO">Em Andamento</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="observacoes" rows="4" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar Etapa</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
$(function(){
    function toggleOrigemEtapaExtra() {
        const origem = $('#origem_tipo_etapa_extra').val();
        $('.origem-aditivo-extra').toggleClass('d-none', origem !== 'ADITIVO');
    }
    $(document).on('change', '#origem_tipo_etapa_extra', toggleOrigemEtapaExtra);
    toggleOrigemEtapaExtra();
});
</script>
@endpush
