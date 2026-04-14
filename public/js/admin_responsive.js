
(function ($) {
    'use strict';

    function ensureResponsiveTables(context) {
        var $ctx = context ? $(context) : $(document);

        $ctx.find('table').each(function () {
            var $table = $(this);

            if ($table.closest('.table-responsive').length === 0 && ($table.hasClass('table') || $table.hasClass('dataTable'))) {
                $table.wrap('<div class="table-responsive"></div>');
            }

            if (($table.hasClass('dataTable') || $.fn.dataTable && $.fn.dataTable.isDataTable(this))
                && $table.prev('.scroll-hint').length === 0) {
                $table.before('<div class="scroll-hint"><i class="fas fa-arrows-alt-h mr-1"></i>Arraste a tabela para o lado para ver todas as colunas.</div>');
            }
        });
    }

    $(function () {
        ensureResponsiveTables(document);

        $(document).on('init.dt draw.dt', function (e) {
            ensureResponsiveTables(e.target);
            if ($.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });

        $(window).on('resize', function () {
            if ($.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
    });
})(jQuery);
