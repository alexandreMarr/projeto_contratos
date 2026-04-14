@push('js')
<script>
$(function () {
    $('#btn-preview-extracao').on('click', function (e) {
        e.preventDefault();

        const form = $('#form-anexo-processo');
        const formData = new FormData(form[0]);
        const btn = $(this);
        const container = $('#extracao-preview-container');

        if (!$('#arquivo_anexo').val()) {
            Swal.fire('Atenção!', 'Selecione um arquivo antes de executar a pré-análise.', 'warning');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Analisando...');
        container.html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Processando pré-análise...</div>');

        $.ajax({
            url: "{{ route('processos-contratacao.documentos.preview-extracao') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (resp) {
                container.html(resp.html);
            },
            error: function (xhr) {
                container.html('');
                Swal.fire('Erro!', xhr.responseJSON?.message || 'Falha ao processar a pré-análise.', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-search"></i> Pré-analisar documento');
            }
        });
    });
});
</script>
@endpush
