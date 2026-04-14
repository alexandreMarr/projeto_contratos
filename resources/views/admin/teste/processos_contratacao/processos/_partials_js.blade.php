@push('js')
<script>
$(function () {
    if ($('#processos-contratacao-table').length) {
        const table = $('#processos-contratacao-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('processos-contratacao.data') }}",
                data: function (d) {
                    d.status = $('#filtro_status').val();
                    d.empresa_contratada_id = $('#filtro_empresa_contratada').val();
                    d.empresa_contratante_id = $('#filtro_empresa_contratante').val();
                    d.tipo_contratacao = $('#filtro_tipo_contratacao').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'numero_processo_interno', name: 'numero_processo_interno' },
                { data: 'titulo', name: 'titulo' },
                { data: 'contratante_nome', name: 'contratante.razao_social', orderable: false },
                { data: 'contratada_nome', name: 'contratada.razao_social', orderable: false },
                { data: 'status', name: 'status' },
                { data: 'etapa_atual_nome', name: 'etapaAtual.nome_etapa', orderable: false },
                { data: 'valor_proposto', name: 'valor_proposto' },
                { data: 'valor_aprovado_final', name: 'valor_aprovado_final' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            language: {
                url: "/storage/traducao_datatables_pt_br.json"
            }
        });

        function atualizarCards() {
            $.get("{{ route('processos-contratacao.stats') }}", {
                status: $('#filtro_status').val(),
                empresa_contratada_id: $('#filtro_empresa_contratada').val()
            }, function (resp) {
                $('#card-total-processos').text(resp.total_processos);
                $('#card-em-analise').text(resp.em_analise);
                $('#card-aprovados').text(resp.aprovados);
                $('#card-contratados').text(resp.contratados);
                $('#card-valor-total-proposto').text(resp.valor_total_proposto);
                $('#card-valor-total-aprovado').text(resp.valor_total_aprovado);
            });
        }

        $('#btn-filtrar-processos').on('click', function () {
            table.draw();
            atualizarCards();
        });

        $('#btn-limpar-processos').on('click', function () {
            $('#filtro_status').val('').trigger('change');
            $('#filtro_empresa_contratada').val('').trigger('change');
            $('#filtro_empresa_contratante').val('').trigger('change');
            $('#filtro_tipo_contratacao').val('');
            table.draw();
            atualizarCards();
        });

        atualizarCards();
    }
});
</script>
@endpush
