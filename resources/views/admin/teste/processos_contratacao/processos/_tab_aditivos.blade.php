<div class="row">
    <div class="col-md-5">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Novo Aditivo / Reajuste</h3></div>
            <form action="{{ route('processos-contratacao.aditivos.store', $processo) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo *</label>
                        <select name="tipo" class="form-control select2" required>
                            <option value="ADITIVO_VALOR">Aditivo de Valor</option>
                            <option value="ADITIVO_PRAZO">Aditivo de Prazo</option>
                            <option value="REAJUSTE">Reajuste</option>
                            <option value="OUTRO">Outro</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Nº Documento</label><input type="text" name="numero_documento" class="form-control"></div>
                    <div class="form-group"><label>Data Referência</label><input type="date" name="data_referencia" class="form-control"></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Valor Anterior</label><input type="number" step="0.01" name="valor_anterior" class="form-control"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Valor Novo</label><input type="number" step="0.01" name="valor_novo" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Vigência Anterior Fim</label><input type="date" name="vigencia_anterior_fim" class="form-control"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Vigência Nova Fim</label><input type="date" name="vigencia_nova_fim" class="form-control"></div></div>
                    </div>
                    <div class="form-group"><label>Descrição</label><textarea name="descricao" rows="4" class="form-control"></textarea></div>
                    <div class="form-group"><label>Observações</label><textarea name="observacoes" rows="3" class="form-control"></textarea></div>
                </div>
                <div class="card-footer text-right">
                    @can('edit processos contratacao')
                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Salvar</button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Histórico de Aditivos</h3></div>
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nº Documento</th>
                            <th>Data</th>
                            <th>Impacto</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processo->aditivos->sortByDesc('created_at') as $aditivo)
                            <tr>
                                <td><span class="badge badge-info">{{ $aditivo->tipo }}</span></td>
                                <td>{{ $aditivo->numero_documento ?: '-' }}</td>
                                <td>{{ optional($aditivo->data_referencia)->format('d/m/Y') ?: '-' }}</td>
                                <td>
                                    @if($aditivo->valor_novo !== null)
                                        R$ {{ number_format($aditivo->valor_anterior ?? 0, 2, ',', '.') }}
                                        →
                                        R$ {{ number_format($aditivo->valor_novo ?? 0, 2, ',', '.') }}
                                    @elseif($aditivo->vigencia_nova_fim)
                                        {{ optional($aditivo->vigencia_anterior_fim)->format('d/m/Y') ?: '-' }}
                                        →
                                        {{ optional($aditivo->vigencia_nova_fim)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $aditivo->descricao ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">Nenhum aditivo lançado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
