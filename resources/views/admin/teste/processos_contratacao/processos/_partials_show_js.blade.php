@push('js')
<script>
$(function () {
    $('#form-anexo-processo').on('submit', function () {
        $('#btn-upload-anexo').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enviando...');
    });

    $(document).on('click', '.btn-editar-etapa', function () {
        $('#modal_etapa_id').val($(this).data('id'));
        $('#modal_etapa_status').val($(this).data('status'));
        $('#modal_etapa_data_inicio').val($(this).data('data_inicio'));
        $('#modal_etapa_data_limite').val($(this).data('data_limite'));
        $('#modal_etapa_data_conclusao').val($(this).data('data_conclusao'));
        $('#modal_etapa_observacoes').val($(this).data('observacoes'));
        $('#modalEditarEtapa').modal('show');
    });

    $('#form-editar-etapa').on('submit', function (e) {
        e.preventDefault();

        const etapaId = $('#modal_etapa_id').val();
        const url = "{{ route('processos-contratacao.etapas.update', ':id') }}".replace(':id', etapaId);

        $.ajax({
            url: url,
            type: 'PUT',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (resp) {
                $('#modalEditarEtapa').modal('hide');
                Swal.fire('Sucesso!', resp.message, 'success').then(() => location.reload());
            },
            error: function (xhr) {
                Swal.fire('Erro!', xhr.responseJSON?.message || 'Falha ao atualizar etapa.', 'error');
            }
        });
    });

    $('#form-etapa-extra').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('processos-contratacao.etapas-extra.store') }}",
            type: 'POST',
            data: $(this).serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (resp) {
                $('#modalNovaEtapaExtra').modal('hide');
                Swal.fire('Sucesso!', resp.message, 'success').then(() => location.reload());
            },
            error: function (xhr) {
                Swal.fire('Erro!', xhr.responseJSON?.message || 'Falha ao criar etapa extra.', 'error');
            }
        });
    });
});
</script>
@endpush
