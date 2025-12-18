<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\TransacaoFaturamento;
use App\Models\ParametroCliente;
use App\Models\ParametroGlobal;
use App\Models\ParametroTaxaAliquota;
use App\Exports\TransacoesExport; 
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Carbon\Carbon;
use App\Models\Fatura;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GerarFaturaPdfJob; 

class FaturamentoExportController extends Controller
{
    /**
     * Constrói a query base de transações.
     */
    private function buildTransacoesQuery(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|integer|exists:empresa,id',
            'periodo' => 'required|date_format:Y-m',
        ]);

        $billable_empresa_id = $request->input('cliente_id');
        $empresa = Empresa::find($billable_empresa_id);
        
        $dataInicio = Carbon::createFromFormat('Y-m', $request->periodo)->startOfMonth();
        $dataFim = $dataInicio->copy()->endOfMonth();

        // Carrega o Estado para poder verificar a sigla (RO, SP, etc)
        $query = TransacaoFaturamento::with([
                'credenciado.municipio.estado', // <<< Importante carregar isso
                'produto', 
                'empenho', 
                'veiculo.grupo.grupoPai'
            ])
            ->whereBetween('data_transacao', [$dataInicio, $dataFim])
            ->whereIn('status', ['confirmada', 'liquidada']);
        
        if ($empresa->empresa_tipo_id == 1) {
            $query->where('cliente_id', $empresa->id)->whereNull('unidade_id');
        } else {
            $query->where('unidade_id', $empresa->id);
        }

        return $query;
    }

    /**
     * Helper para buscar parâmetros e taxas
     */
    private function getParametrosETaxas($billable_empresa_id)
    {
        $empresa = Empresa::with('organizacao', 'matriz.organizacao')->find($billable_empresa_id);
        
        $publico_ids = [1, 2, 3, 5];
        $is_publico = in_array($empresa->organizacao_id, $publico_ids);
        
        $parametro_owner_id = ($empresa->empresa_tipo_id == 2) ? $empresa->empresa_matriz_id : $empresa->id;
        $paramCliente = ParametroCliente::where('empresa_id', $parametro_owner_id)->first();
        
        $parametrosAtivos = [
            'isento_ir' => false,
        ];

        if ($paramCliente && !$paramCliente->ativar_parametros_globais) {
            $parametrosAtivos['isento_ir'] = $paramCliente->isento_ir;
        }

        $matriz = ($empresa->empresa_tipo_id == 2) ? $empresa->matriz : $empresa;
        $organizacao_id_para_taxa = $matriz ? $matriz->organizacao_id : null;

        $taxas = collect();
        if ($organizacao_id_para_taxa) {
             $taxas = ParametroTaxaAliquota::where('organizacao_id', $organizacao_id_para_taxa)
                         ->get()
                         ->keyBy('produto_categoria_id');
        }

        return compact('parametrosAtivos', 'taxas');
    }

    /**
     * Exportar para Excel (XLS)
     */
    public function exportXLS(Request $request)
    {
        
        $cliente_id = $request->input('cliente_id');
        $periodo = $request->input('periodo');
        $cliente = Empresa::find($cliente_id);
        
        $query = $this->buildTransacoesQuery($request);
        extract($this->getParametrosETaxas($cliente_id)); 
        
        $paramGlobal = ParametroGlobal::first(); // <-- Busca o parametro global

        $fileName = "transacoes_{$cliente->razao_social}_{$periodo}.xlsx";

        // Passa o $paramGlobal para o construtor do Export
        return Excel::download(new TransacoesExport($query, $parametrosAtivos, $taxas, $paramGlobal), $fileName);
    }

    /**
     * Exportar para PDF (Lista de Transações)
     */
    public function exportPDF(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '2G');

        $cliente_id = $request->input('cliente_id');
        $periodo = $request->input('periodo');
        $cliente = Empresa::find($cliente_id);

        $query = $this->buildTransacoesQuery($request);
        extract($this->getParametrosETaxas($cliente_id)); 
        
        $paramGlobal = ParametroGlobal::first();

        // Processa os dados manualmente para o PDF
        $transacoes = $query->get()->map(function($row) use ($parametrosAtivos, $taxas, $paramGlobal) {
            $aliquota_ir = 0;
            $valor_ir_calculado = 0; // Armazena numérico para conta
            
            if (!$parametrosAtivos['isento_ir']) {
                $deveCobrarIR = true;
                if ($paramGlobal && !$paramGlobal->cobrar_ir_fora_do_estado_rondonia) {
                    $uf = optional(optional(optional($row->credenciado)->municipio)->estado)->sigla;
                    if ($uf && $uf !== 'RO') {
                        $deveCobrarIR = false;
                    }
                }

                if ($deveCobrarIR) {
                    $categoriaId = optional($row->produto)->produto_categoria_id;
                    $taxa = $taxas->get($categoriaId);
                    $aliquota_ir = $taxa ? $taxa->taxa_aliquota : 0;
                    $valor_ir_calculado = $row->valor_total * $aliquota_ir;
                }
            }
            
            $valor_liquido = $row->valor_total - $valor_ir_calculado;

            // Formatação para exibição
            $row->faturada_texto = $row->status_faturamento == 'pendente' ? 'Não' : 'Sim';
            $row->data_formatada = Carbon::parse($row->data_transacao)->format('d/m/Y H:i');
            $row->credenciado_nome = optional($row->credenciado)->razao_social ?? 'N/A';
            $row->grupo_nome = optional(optional(optional($row->veiculo)->grupo)->grupoPai)->nome ?? 'N/A';
            $row->subgrupo_nome = optional(optional($row->veiculo)->grupo)->nome ?? 'N/A';
            $row->produto_nome = optional($row->produto)->nome ?? 'N/A';
            $row->placa = optional($row->veiculo)->placa ?? 'N/A';
            $row->aliquota_formatada = number_format($aliquota_ir * 100, 2, ',', '.') . '%';
            
            $row->valor_ir_calculado = 'R$ ' . number_format($valor_ir_calculado, 2, ',', '.');
            $row->valor_bruto = 'R$ ' . number_format($row->valor_total, 2, ',', '.');
            
            // NOVA COLUNA PARA O PDF
            $row->valor_liquido = 'R$ ' . number_format($valor_liquido, 2, ',', '.');
            
            return $row;
        });
        
        $data = [
            'transacoes' => $transacoes,
            'cliente' => $cliente,
            'periodo' => Carbon::createFromFormat('Y-m', $periodo)->locale('pt_BR')->translatedFormat('F/Y'),
            'paramGlobal' => $paramGlobal, 
        ];
        
        $headerHtml = view('admin.faturamento.exports.transacoes_header', $data)->render();
        $footerHtml = view('admin.faturamento.exports.fatura_footer', $data)->render();
        
        $fileName = "transacoes_pdf_{$cliente->razao_social}_{$periodo}.pdf";

        $pdf = PDF::loadView('admin.faturamento.exports.transacoes_pdf', $data)
                  ->setPaper('a4', 'portrait')
                  ->setOption('enable-local-file-access', true)
                  ->setOption('enable-external-links', true)
                  ->setOption('margin-top', '35mm')     
                  ->setOption('margin-bottom', '35mm')  
                  ->setOption('margin-left', '10mm')     
                  ->setOption('margin-right', '10mm')    
                  ->setOption('header-html', $headerHtml)
                  ->setOption('footer-html', $footerHtml)
                  ->setOption('header-spacing', 5)
                  ->setOption('footer-spacing', 5);
        
        return $pdf->stream($fileName);
    }

    /**
     * Exportar Fatura Individual (PDF)
     */
    public function exportFaturaPDF(Request $request, Fatura $fatura)
     {
         set_time_limit(300);
         ini_set('memory_limit', '2G');

         // Carrega relações (importante carregar credenciado.municipio.estado dentro da transacao)
         $fatura->load([
             'cliente.municipio.estado', 
             'itens.transacao' => function($query) {
                 $query->with(['credenciado.municipio.estado', 'produto', 'veiculo.grupo.grupoPai']); // <<< Ajustado
             },
             'descontos.usuario', 
             'pagamentos'
         ]);

         $paramGlobal = ParametroGlobal::first();
         extract($this->getParametrosETaxas($fatura->cliente_id)); 
         $totalDescontosManuais = $fatura->valor_descontos_manuais;

         // Passamos $paramGlobal para o closure
         $transacoesProcessadas = $fatura->itens->map(function($item) use ($parametrosAtivos, $taxas, $paramGlobal) {
             $tr = $item->transacao; 
             $aliquota_ir = 0;
             $valor_ir_num = 0;

             if ($tr && !$parametrosAtivos['isento_ir']) {
                 
                 // <<<--- NOVA LÓGICA DE IR (Estado RO) ---
                 $deveCobrarIR = true;
                 if ($paramGlobal && !$paramGlobal->cobrar_ir_fora_do_estado_rondonia) {
                     // Verifica UF do credenciado da transação
                     $uf = optional(optional(optional($tr->credenciado)->municipio)->estado)->sigla;
                     if ($uf && $uf !== 'RO') {
                         $deveCobrarIR = false;
                     }
                 }
                 
                 if ($deveCobrarIR) {
                     $categoriaId = optional($tr->produto)->produto_categoria_id;
                     $taxa = $taxas->get($categoriaId);
                     $aliquota_ir = $taxa ? $taxa->taxa_aliquota : 0;
                 }
                 // --- FIM DA NOVA LÓGICA ---
             }
             
             if ($tr) { 
                $valor_ir_num = $item->valor_subtotal * $aliquota_ir;
             }

             return (object) [
                 'id' => $tr->id ?? $item->id,
                 'data' => $tr ? $tr->data_transacao->format('d/m/y H:i') : 'N/A',
                 'credenciado' => optional(optional($tr)->credenciado)->nome ?? 'N/A',
                 'grupo' => optional(optional(optional(optional($tr)->veiculo)->grupo)->grupoPai)->nome ?? 'N/A',
                 'subgrupo' => optional(optional(optional($tr)->veiculo)->grupo)->nome ?? 'N/A',
                 'produto' => $item->descricao_produto,
                 'placa' => optional(optional($tr)->veiculo)->placa ?? 'N/A',
                 'valor_bruto' => number_format($item->valor_subtotal, 2, ',', '.'),
                 'aliquota_ir' => number_format($aliquota_ir * 100, 2, ',') . '%',
                 'valor_ir' => number_format($valor_ir_num, 2, ',', '.'),
             ];
         });

         $data = [
             'fatura' => $fatura,
             'paramGlobal' => $paramGlobal,
             'totalDescontosManuais' => $totalDescontosManuais,
             'transacoes' => $transacoesProcessadas, 
         ];

         $headerHtml = view('admin.faturamento.exports.fatura_header', $data)->render();
         $footerHtml = view('admin.faturamento.exports.fatura_footer', $data)->render();
        
         $fileName = "fatura_{$fatura->numero_fatura}_{$fatura->cliente->razao_social}.pdf";

         $pdf = PDF::loadView('admin.faturamento.exports.fatura_pdf', $data)
                   ->setPaper('a4', 'portrait')
                   ->setOption('enable-local-file-access', true) 
                   ->setOption('enable-external-links', true)
                   ->setOption('margin-top', '35mm')     
                   ->setOption('margin-bottom', '35mm')  
                   ->setOption('margin-left', '10mm')    
                   ->setOption('margin-right', '10mm')   
                   ->setOption('header-html', $headerHtml)
                   ->setOption('footer-html', $footerHtml)
                   ->setOption('header-spacing', 5) 
                   ->setOption('footer-spacing', 5); 
        
         return $pdf->stream($fileName);
     }
}