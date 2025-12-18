<?php

namespace App\Exports;

use App\Models\TransacaoFaturamento;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Recomendado para ajustar largura
use Carbon\Carbon;

class TransacoesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;
    protected $parametrosAtivos;
    protected $taxas;
    protected $paramGlobal; // 1. Nova propriedade

    // 2. Construtor atualizado recebendo $paramGlobal
    public function __construct($query, $parametrosAtivos, $taxas, $paramGlobal)
    {
        $this->query = $query;
        $this->parametrosAtivos = $parametrosAtivos;
        $this->taxas = $taxas;
        $this->paramGlobal = $paramGlobal;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return $this->query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'ID Transação',
            'Faturada?',
            'ID Fatura',
            'Data Transação',
            'ID Credenciado',
            'Credenciado',
            'UF Credenciado', // Útil para conferência
            'ID Cliente',
            'Cliente',
            'ID Unidade',
            'Unidade',
            'ID Contrato',
            'Contrato',
            'ID Empenho',
            'Empenho',
            'ID Veículo',
            'Placa',
            'Grupo',
            'Subgrupo',
            'ID Produto',
            'Produto',
            'Qtd',
            'Valor Unitário',
            'Valor Total (Bruto)',
            'Alíquota IR Aplicada',
            'Valor IR Calculado',
            'Valor Líquido', // 3. Nova Coluna
        ];
    }

    /**
    * @param TransacaoFaturamento $row
    * @return array
    */
    public function map($row): array
    {
        $aliquota_ir = 0;
        $valor_ir = 0;

        // --- 4. LÓGICA DE CÁLCULO DO IR COM VALIDAÇÃO DE ESTADO ---
        if (!$this->parametrosAtivos['isento_ir']) {
            $deveCobrarIR = true;

            // Verifica o parâmetro global sobre cobrar fora de RO
            if ($this->paramGlobal && !$this->paramGlobal->cobrar_ir_fora_do_estado_rondonia) {
                // Navega segura: Credenciado -> Municipio -> Estado -> Sigla
                $uf = optional(optional(optional($row->credenciado)->municipio)->estado)->sigla;
                
                // Se UF existe e for diferente de 'RO', não cobra
                if ($uf && $uf !== 'RO') {
                    $deveCobrarIR = false;
                }
            }

            if ($deveCobrarIR) {
                $categoriaId = optional($row->produto)->produto_categoria_id;
                $taxa = $this->taxas->get($categoriaId);
                $aliquota_ir = $taxa ? $taxa->taxa_aliquota : 0;
                $valor_ir = $row->valor_total * $aliquota_ir;
            }
        }

        // Cálculo do Líquido
        $valor_liquido = $row->valor_total - $valor_ir;

        return [
            $row->id,
            $row->status_faturamento == 'pendente' ? 'Não' : 'Sim',
            $row->fatura_id,
            Carbon::parse($row->data_transacao)->format('d/m/Y H:i:s'),
            $row->credenciado_id,
            optional($row->credenciado)->razao_social ?? 'N/A',
            optional(optional(optional($row->credenciado)->municipio)->estado)->sigla ?? 'N/A', // Coluna UF
            $row->cliente_id,
            optional($row->cliente)->razao_social ?? 'N/A',
            $row->unidade_id,
            optional($row->unidade)->razao_social ?? 'N/A',
            $row->contrato_id,
            optional($row->contrato)->numero ?? 'N/A',
            $row->empenho_id,
            optional($row->empenho)->numero_empenho ?? 'N/A',
            $row->veiculo_id,
            optional($row->veiculo)->placa ?? 'N/A',
            optional(optional(optional($row->veiculo)->grupo)->grupoPai)->nome ?? 'N/A',
            optional(optional($row->veiculo)->grupo)->nome ?? 'N/A',
            $row->produto_id,
            optional($row->produto)->nome ?? 'N/A',
            $row->quantidade,
            $row->valor_unitario,
            $row->valor_total,    // Valor Bruto
            $aliquota_ir,         // Alíquota
            $valor_ir,            // Valor IR
            $valor_liquido,       // Valor Líquido
        ];
    }
}