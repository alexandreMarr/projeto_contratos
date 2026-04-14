<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Dados da Etapa</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-md-7">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" class="form-control"
                           value="{{ old('nome', $etapaTemplate->nome ?? '') }}" required>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Ordem *</label>
                    <input type="number" name="ordem" class="form-control"
                           value="{{ old('ordem', $etapaTemplate->ordem ?? '') }}" required min="1">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>SLA (dias)</label>
                    <input type="number" name="prazo_limite_dias" class="form-control"
                           value="{{ old('prazo_limite_dias', $etapaTemplate->prazo_limite_dias ?? '') }}" min="0">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Setor Responsável</label>
                    <select name="setor_id" class="form-control select2">
                        <option value="">Selecione</option>
                        @foreach($setores as $setor)
                            <option value="{{ $setor->id }}"
                                @selected(old('setor_id', $etapaTemplate->setor_id ?? '') == $setor->id)>
                                {{ $setor->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Cor Badge</label>
                    @php($corSelecionada = old('cor_badge', $etapaTemplate->cor_badge ?? 'primary'))
                    <select name="cor_badge" class="form-control">
                        @foreach(['primary','secondary','success','danger','warning','info','dark'] as $cor)
                            <option value="{{ $cor }}" @selected($corSelecionada === $cor)>{{ $cor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" id="ativo"
                               name="ativo" value="1"
                               @checked(old('ativo', $etapaTemplate->ativo ?? true))>
                        <label class="form-check-label" for="ativo">Etapa ativa</label>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="descricao" rows="4" class="form-control">{{ old('descricao', $etapaTemplate->descricao ?? '') }}</textarea>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="obrigatoria"
                           name="obrigatoria" value="1"
                           @checked(old('obrigatoria', $etapaTemplate->obrigatoria ?? true))>
                    <label class="form-check-label" for="obrigatoria">Obrigatória</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="permite_anexo"
                           name="permite_anexo" value="1"
                           @checked(old('permite_anexo', $etapaTemplate->permite_anexo ?? true))>
                    <label class="form-check-label" for="permite_anexo">Permite anexo</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exige_parecer"
                           name="exige_parecer" value="1"
                           @checked(old('exige_parecer', $etapaTemplate->exige_parecer ?? false))>
                    <label class="form-check-label" for="exige_parecer">Exige parecer</label>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exige_aprovacao"
                           name="exige_aprovacao" value="1"
                           @checked(old('exige_aprovacao', $etapaTemplate->exige_aprovacao ?? false))>
                    <label class="form-check-label" for="exige_aprovacao">Exige aprovação</label>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer text-right">
        <a href="{{ route('etapas-padrao.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Salvar
        </button>
    </div>
</div>
