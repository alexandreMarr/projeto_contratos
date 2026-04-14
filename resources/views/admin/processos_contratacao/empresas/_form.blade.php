@csrf

<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">Dados Cadastrais</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Razão Social *</label>
                            <input type="text" id="razao_social" name="razao_social" class="form-control @error('razao_social') is-invalid @enderror"
                                   value="{{ old('razao_social', $empresa->razao_social ?? '') }}" required>
                            @error('razao_social') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo da Empresa *</label>
                            <select name="tipo_empresa" class="form-control select2 @error('tipo_empresa') is-invalid @enderror" required>
                                <option value="CONTRATANTE" @selected(old('tipo_empresa', $empresa->tipo_empresa ?? '') === 'CONTRATANTE')>Contratante</option>
                                <option value="CONTRATADA" @selected(old('tipo_empresa', $empresa->tipo_empresa ?? '') === 'CONTRATADA')>Contratada</option>
                                <option value="AMBAS" @selected(old('tipo_empresa', $empresa->tipo_empresa ?? '') === 'AMBAS')>Ambas</option>
                            </select>
                            @error('tipo_empresa') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nome Fantasia</label>
                            <input type="text" id="nome_fantasia" name="nome_fantasia" class="form-control"
                                   value="{{ old('nome_fantasia', $empresa->nome_fantasia ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>CNPJ *</label>
                            <input type="text" id="cnpj" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror"
                                   value="{{ old('cnpj', $empresa->cnpj ?? '') }}" required>
                            @error('cnpj') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small id="cnpj-feedback" class="form-text text-muted"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="ativo" class="form-control select2">
                                <option value="1" @selected((string) old('ativo', $empresa->ativo ?? 1) === '1')>Ativa</option>
                                <option value="0" @selected((string) old('ativo', $empresa->ativo ?? 1) === '0')>Inativa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Inscrição Estadual</label>
                            <input type="text" name="inscricao_estadual" class="form-control"
                                   value="{{ old('inscricao_estadual', $empresa->inscricao_estadual ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Inscrição Municipal</label>
                            <input type="text" name="inscricao_municipal" class="form-control"
                                   value="{{ old('inscricao_municipal', $empresa->inscricao_municipal ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="{{ old('email', $empresa->email ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" id="telefone" name="telefone" class="form-control"
                                   value="{{ old('telefone', $empresa->telefone ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-info mb-3">
            <div class="card-header">
                <h3 class="card-title">Contato e Endereço</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Contato Principal</label>
                            <input type="text" name="contato_principal" class="form-control"
                                   value="{{ old('contato_principal', $empresa->contato_principal ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cargo do Contato</label>
                            <input type="text" name="cargo_contato" class="form-control"
                                   value="{{ old('cargo_contato', $empresa->cargo_contato ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" name="celular" class="form-control"
                                   value="{{ old('celular', $empresa->celular ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>CEP</label>
                            <input type="text" id="cep" name="cep" class="form-control"
                                   value="{{ old('cep', $empresa->cep ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cidade</label>
                            <input type="text" id="cidade" name="cidade" class="form-control"
                                   value="{{ old('cidade', $empresa->cidade ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>UF</label>
                            <input type="text" id="uf" name="uf" class="form-control" maxlength="2"
                                   value="{{ old('uf', $empresa->uf ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Endereço</label>
                            <input type="text" id="endereco" name="endereco" class="form-control"
                                   value="{{ old('endereco', $empresa->endereco ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número</label>
                            <input type="text" id="numero" name="numero" class="form-control"
                                   value="{{ old('numero', $empresa->numero ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Bairro</label>
                    <input type="text" id="bairro" name="bairro" class="form-control"
                           value="{{ old('bairro', $empresa->bairro ?? '') }}">
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Observações</h3>
            </div>
            <div class="card-body">
                <textarea name="observacoes" rows="5" class="form-control">{{ old('observacoes', $empresa->observacoes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-success mb-3">
            <div class="card-header">
                <h3 class="card-title">Dados Bancários</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Banco</label>
                    <input type="text" name="banco" class="form-control"
                           value="{{ old('banco', $empresa->banco ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Agência</label>
                    <input type="text" name="agencia" class="form-control"
                           value="{{ old('agencia', $empresa->agencia ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Conta</label>
                    <input type="text" name="conta" class="form-control"
                           value="{{ old('conta', $empresa->conta ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Chave PIX</label>
                    <input type="text" name="chave_pix" class="form-control"
                           value="{{ old('chave_pix', $empresa->chave_pix ?? '') }}">
                </div>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Ações</h3>
            </div>
            <div class="card-body">
                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-save mr-1"></i> Salvar
                </button>
                <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar
                </a>
                @isset($empresa)
                    <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-eye mr-1"></i> Visualizar
                    </a>
                @endisset
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(function() {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });

        function apenasNumeros(valor) {
            return (valor || '').replace(/\D/g, '');
        }

        function formatarCnpj(cnpj) {
            cnpj = apenasNumeros(cnpj);

            if (cnpj.length > 14) {
                cnpj = cnpj.substring(0, 14);
            }

            cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
            cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
            cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');

            return cnpj;
        }

        function preencherCampo(seletor, valor, sobrescrever = true) {
            const $campo = $(seletor);

            if (!$campo.length) return;

            if (sobrescrever || !$campo.val()) {
                $campo.val(valor ?? '');
            }
        }

        function limparFeedbackCnpj() {
            $('#cnpj-feedback')
                .removeClass('text-danger text-success text-muted')
                .text('');
        }

        $('#cnpj').on('input', function() {
            $(this).val(formatarCnpj($(this).val()));
            limparFeedbackCnpj();
        });

        $('#cnpj').on('blur', function() {
            let cnpjDigitado = $(this).val();
            let cnpjLimpo = apenasNumeros(cnpjDigitado);

            $('#cnpj').val(formatarCnpj(cnpjLimpo));
            limparFeedbackCnpj();

            if (!cnpjLimpo) {
                return;
            }

            if (cnpjLimpo.length !== 14) {
                $('#cnpj-feedback')
                    .addClass('text-danger')
                    .text('Informe um CNPJ válido com 14 dígitos.');
                return;
            }

            $('#cnpj-feedback')
                .addClass('text-muted')
                .text('Consultando CNPJ...');

            $.ajax({
                url: "{{ route('empresas.buscarcnpj', ':cnpj') }}".replace(':cnpj', cnpjLimpo),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (!response.success || !response.data) {
                        $('#cnpj-feedback')
                            .removeClass('text-muted text-success')
                            .addClass('text-danger')
                            .text(response.message || 'Não foi possível consultar o CNPJ.');
                        return;
                    }

                    const data = response.data;

                    preencherCampo('#razao_social', data.razao_social);
                    preencherCampo('#nome_fantasia', data.nome_fantasia);
                    preencherCampo('#email', data.email);
                    preencherCampo('#telefone', data.telefone);
                    preencherCampo('#cep', data.cep);
                    preencherCampo('#cidade', data.cidade);
                    preencherCampo('#uf', data.uf ? String(data.uf).toUpperCase() : '');
                    preencherCampo('#bairro', data.bairro);
                    preencherCampo('#numero', data.numero);
                    preencherCampo('#endereco', data.endereco);

                    $('#cnpj-feedback')
                        .removeClass('text-muted text-danger')
                        .addClass('text-success')
                        .text('Dados preenchidos com sucesso.');
                },
                error: function(xhr) {
                    let mensagem = 'Erro ao consultar o CNPJ.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        mensagem = xhr.responseJSON.message;
                    }

                    $('#cnpj-feedback')
                        .removeClass('text-muted text-success')
                        .addClass('text-danger')
                        .text(mensagem);
                }
            });
        });

        // Formata CNPJ ao carregar a tela, caso já exista valor
        const cnpjInicial = $('#cnpj').val();
        if (cnpjInicial) {
            $('#cnpj').val(formatarCnpj(cnpjInicial));
        }
    });
</script>
@endpush
