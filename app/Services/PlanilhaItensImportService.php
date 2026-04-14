<?php

namespace App\Services;

use App\Models\ProcessoAnexo;
use App\Models\ProcessoContratacao;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlanilhaItensImportService
{
    public function importar(
        ProcessoContratacao $processo,
        ProcessoAnexo $anexo,
        string $origemTipo = 'CONTRATO',
        ?int $aditivoId = null
    ): array {
        $path = storage_path('app/public/' . $anexo->caminho_arquivo);

        if (!file_exists($path)) {
            throw new \RuntimeException('Arquivo da planilha não encontrado.');
        }

        $spreadsheet = IOFactory::load($path);

        $sheetInfo = $this->findBestSheetAndHeader($spreadsheet);

        if (!$sheetInfo) {
            throw new \RuntimeException('Não foi possível localizar uma aba com o detalhamento da proposta.');
        }

        /** @var Worksheet $sheet */
        $sheet = $sheetInfo['sheet'];
        $headerRowIndex = $sheetInfo['header_row_index'];
        $columns = $sheetInfo['columns'];

        $highestRow = $sheet->getHighestDataRow();
        $rows = [];

        for ($row = $headerRowIndex + 1; $row <= $highestRow; $row++) {
            $rows[] = [
                'row_index' => $row,
                'item' => $this->cellValue($sheet, $columns['item'], $row),
                'descricao' => $this->cellValue($sheet, $columns['descricao'], $row),
                'unidade' => $this->cellValue($sheet, $columns['unidade'], $row),
                'valor_unitario' => $this->cellValue($sheet, $columns['valor_unitario'], $row),
                'quantidade' => $this->cellValue($sheet, $columns['quantidade'], $row),
                'financeiro' => $this->cellValue($sheet, $columns['financeiro'], $row),
            ];
        }

        $rows = $this->sanitizeRows($rows);

        if (empty($rows)) {
            throw new \RuntimeException('Nenhuma linha válida foi encontrada no detalhamento da proposta.');
        }

        $inserted = 0;
        $ordem = ((int) $processo->itens()->max('ordem')) + 1;
        $grupoAtual = null;
        $subgrupoAtual = null;

        foreach ($rows as $row) {
            $codigo = $row['item'];
            $descricao = $row['descricao'];
            $unidade = $this->normalizeText($row['unidade']);
            $valorUnitario = $this->toDecimal($row['valor_unitario']);
            $quantidade = $this->toDecimal($row['quantidade']);
            $valorTotal = $this->toDecimal($row['financeiro']);

            $classificacao = $this->classificarLinha(
                $codigo,
                $descricao,
                $unidade,
                $quantidade,
                $valorUnitario,
                $valorTotal
            );

            if ($classificacao['tipo_linha'] === 'GRUPO') {
                $grupoAtual = $descricao;
                $subgrupoAtual = null;
            }

            if ($classificacao['tipo_linha'] === 'SUBGRUPO') {
                $subgrupoAtual = $descricao;
            }

            $processo->itens()->create([
                'anexo_id' => $anexo->id,
                'origem_tipo' => $origemTipo,
                'aditivo_id' => $aditivoId,
                'codigo_item' => $codigo !== '' ? $codigo : null,
                'codigo_pai' => $classificacao['codigo_pai'],
                'item_referencia' => $codigo !== '' ? $codigo : null,
                'nivel' => $classificacao['nivel'],
                'tipo_linha' => $classificacao['tipo_linha'],
                'grupo' => $grupoAtual,
                'subgrupo' => $subgrupoAtual,
                'descricao' => $descricao,
                'unidade' => $unidade,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'valor_total' => $valorTotal,
                'ordem' => $ordem,
                'ativo' => true,
            ]);

            $ordem++;
            $inserted++;
        }

        return [
            'success' => true,
            'sheet' => $sheet->getTitle(),
            'header_row' => $headerRowIndex,
            'inserted' => $inserted,
        ];
    }

    protected function findBestSheetAndHeader($spreadsheet): ?array
    {
        $best = null;
        $bestScore = -1;

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $highestRow = min($sheet->getHighestDataRow(), 80);
            $highestColumn = $sheet->getHighestDataColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            for ($row = 1; $row <= $highestRow; $row++) {
                $headerMap = $this->detectHeaderMap($sheet, $row, $highestColumnIndex);

                if (!$headerMap['valid']) {
                    continue;
                }

                $score = $this->scoreSheet($sheet, $row, $headerMap['columns']);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [
                        'sheet' => $sheet,
                        'header_row_index' => $row,
                        'columns' => $headerMap['columns'],
                        'score' => $score,
                    ];
                }
            }
        }

        return $best;
    }

    protected function detectHeaderMap(Worksheet $sheet, int $row, int $highestColumnIndex): array
    {
        $columns = [
            'item' => null,
            'descricao' => null,
            'unidade' => null,
            'valor_unitario' => null,
            'quantidade' => null,
            'financeiro' => null,
        ];

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $value = $this->normalizeHeader(
                (string) $sheet->getCellByColumnAndRow($col, $row)->getFormattedValue()
            );

            if ($value === '') {
                continue;
            }

            if ($columns['item'] === null && $this->headerMatches($value, ['item', 'codigo', 'código'])) {
                $columns['item'] = $col;
                continue;
            }

            if ($columns['descricao'] === null && $this->headerMatches($value, ['descricao', 'descrição'])) {
                $columns['descricao'] = $col;
                continue;
            }

            if ($columns['unidade'] === null && $this->headerMatches($value, ['un', 'und', 'unidade'])) {
                $columns['unidade'] = $col;
                continue;
            }

            if ($columns['valor_unitario'] === null && $this->headerMatches($value, ['valor unit', 'valor unitario', 'valor unit.', 'vlr unit'])) {
                $columns['valor_unitario'] = $col;
                continue;
            }

            if ($columns['quantidade'] === null && $this->headerMatches($value, ['quantidade', 'quant', 'qtd'])) {
                $columns['quantidade'] = $col;
                continue;
            }

            if ($columns['financeiro'] === null && $this->headerMatches($value, ['financeiro', 'total', 'valor total', 'vlr total'])) {
                $columns['financeiro'] = $col;
                continue;
            }
        }

        $required = ['item', 'descricao', 'quantidade'];
        $valid = true;

        foreach ($required as $field) {
            if ($columns[$field] === null) {
                $valid = false;
                break;
            }
        }

        if ($columns['valor_unitario'] === null && $columns['financeiro'] === null) {
            $valid = false;
        }

        return [
            'valid' => $valid,
            'columns' => $columns,
        ];
    }

    protected function scoreSheet(Worksheet $sheet, int $headerRow, array $columns): int
    {
        $score = 0;
        $highestRow = min($sheet->getHighestDataRow(), $headerRow + 60);

        $sheetName = mb_strtoupper($sheet->getTitle(), 'UTF-8');

        foreach (['RESUMO', 'ORÇAMENT', 'ADITIVO', 'PLANILHA', 'DETALHAMENTO'] as $word) {
            if (str_contains($sheetName, $word)) {
                $score += 10;
            }
        }

        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $descricao = $this->cellValue($sheet, $columns['descricao'], $row);
            $item = $this->cellValue($sheet, $columns['item'], $row);

            if ($descricao !== '') {
                $score += 1;
            }

            if ($item !== '') {
                $score += 1;
            }

            $valor = '';
            if (!empty($columns['financeiro'])) {
                $valor = $this->cellValue($sheet, $columns['financeiro'], $row);
            }

            if ($valor !== '' && $this->toDecimal($valor) !== null) {
                $score += 2;
            }
        }

        return $score;
    }

    protected function sanitizeRows(array $rows): array
    {
        $output = [];
        $emptyCount = 0;

        foreach ($rows as $row) {
            $item = trim((string)($row['item'] ?? ''));
            $descricao = trim((string)($row['descricao'] ?? ''));
            $unidade = trim((string)($row['unidade'] ?? ''));
            $quantidade = trim((string)($row['quantidade'] ?? ''));
            $valorUnitario = trim((string)($row['valor_unitario'] ?? ''));
            $financeiro = trim((string)($row['financeiro'] ?? ''));

            $linhaVazia = ($item === '' && $descricao === '' && $unidade === '' && $quantidade === '' && $valorUnitario === '' && $financeiro === '');

            if ($linhaVazia) {
                $emptyCount++;
                if ($emptyCount >= 5) {
                    break;
                }
                continue;
            }

            $emptyCount = 0;

            if ($this->isRepeatedHeader($item, $descricao, $unidade, $quantidade, $valorUnitario, $financeiro)) {
                continue;
            }

            if ($this->isNoiseRow($item, $descricao)) {
                continue;
            }

            $output[] = [
                'item' => $item,
                'descricao' => $descricao,
                'unidade' => $unidade,
                'valor_unitario' => $valorUnitario,
                'quantidade' => $quantidade,
                'financeiro' => $financeiro,
            ];
        }

        return $output;
    }

    protected function classificarLinha($codigo, $descricao, $unidade, $quantidade, $valorUnitario, $valorTotal): array
    {
        $codigo = trim((string) $codigo);
        $descricao = trim((string) $descricao);
        $temUnidade = !empty($unidade);
        $temValores = !is_null($quantidade) || !is_null($valorUnitario) || !is_null($valorTotal);

        if ($this->looksLikeSectionLabel($descricao) && !$temUnidade && !$temValores) {
            return [
                'tipo_linha' => 'GRUPO',
                'nivel' => 1,
                'codigo_pai' => null,
            ];
        }

        if ($codigo !== '' && preg_match('/^\d+(\.0+)?$/', $codigo) && !$temUnidade) {
            return [
                'tipo_linha' => 'GRUPO',
                'nivel' => 1,
                'codigo_pai' => null,
            ];
        }

        if ($codigo !== '' && preg_match('/^\d+\.\d+$/', $codigo) && !$temUnidade && !$temValores) {
            return [
                'tipo_linha' => 'SUBGRUPO',
                'nivel' => 2,
                'codigo_pai' => $this->parentCode($codigo),
            ];
        }

        if ($codigo !== '' && preg_match('/^\d+\.\d+(\.\d+)?$/', $codigo)) {
            return [
                'tipo_linha' => 'ITEM',
                'nivel' => substr_count($codigo, '.') + 1,
                'codigo_pai' => $this->parentCode($codigo),
            ];
        }

        if ($codigo !== '' && preg_match('/^\d{4,}$/', $codigo)) {
            return [
                'tipo_linha' => 'ITEM',
                'nivel' => 3,
                'codigo_pai' => null,
            ];
        }

        if (mb_strtoupper($codigo, 'UTF-8') === 'S/CÓDIGO') {
            return [
                'tipo_linha' => 'ITEM',
                'nivel' => 3,
                'codigo_pai' => null,
            ];
        }

        return [
            'tipo_linha' => $temValores || $temUnidade ? 'ITEM' : 'SUBGRUPO',
            'nivel' => $temValores || $temUnidade ? 3 : 2,
            'codigo_pai' => null,
        ];
    }

    protected function looksLikeSectionLabel(string $descricao): bool
    {
        $descricao = mb_strtoupper(trim($descricao), 'UTF-8');

        if ($descricao === '') {
            return false;
        }

        foreach ([
            'SERVIÇOS EXTRAS',
            'PISTA DE ROLAMENTO',
            'REPARO PROFUNDO',
            'MOBILIZAÇÃO',
            'ADMINISTRAÇÃO',
            'SERVIÇOS PRELIMINARES',
            'MOBILIZAÇÃO E DESMOBILIZAÇÃO',
            'RECOMPOSIÇÃO DE EROSÕES',
            'DRENAGEM SUPERFICIAL',
            'FORNECIMENTO DE MATERIAL BETUMINOSO',
            'TRANSPORTE DE MATERIAL BETUMINOSO',
            'MOMENTO DE TRANSPORTE',
            'SERVIÇO',
        ] as $section) {
            if ($descricao === $section) {
                return true;
            }
        }

        return false;
    }

    protected function isRepeatedHeader($item, $descricao, $unidade, $quantidade, $valorUnitario, $financeiro): bool
    {
        $joined = mb_strtoupper(trim(implode(' | ', [$item, $descricao, $unidade, $quantidade, $valorUnitario, $financeiro])), 'UTF-8');

        return str_contains($joined, 'ITEM')
            && str_contains($joined, 'DESCRI')
            && (str_contains($joined, 'QUANT') || str_contains($joined, 'QTD'));
    }

    protected function isNoiseRow($item, $descricao): bool
    {
        $joined = mb_strtoupper(trim($item . ' ' . $descricao), 'UTF-8');

        foreach ([
            'DETALHAMENTO DA PROPOSTA',
            'PLANILHA ORÇAMENTÁRIA',
            'RESUMO DA PLANILHA',
            'OBRA:',
            'LOCAL:',
            'TOTAL GERAL',
        ] as $noise) {
            if (str_contains($joined, $noise)) {
                return true;
            }
        }

        return false;
    }

    protected function parentCode(string $codigo): ?string
    {
        if (!str_contains($codigo, '.')) {
            return null;
        }

        $parts = explode('.', $codigo);
        array_pop($parts);

        return implode('.', $parts);
    }

    protected function cellValue(Worksheet $sheet, ?int $column, int $row): string
    {
        if (!$column) {
            return '';
        }

        $value = $sheet->getCellByColumnAndRow($column, $row)->getFormattedValue();
        return trim((string) $value);
    }

    protected function toDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);
        $value = str_replace(['R$', ' '], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }

    protected function normalizeHeader(string $header): string
    {
        $header = mb_strtoupper(trim($header), 'UTF-8');
        $header = str_replace([':', '.', '(', ')', 'R$', 'Ç', 'Ã', 'Á', 'É', 'Í', 'Ó', 'Ú'], ['', '', '', '', '', 'C', 'A', 'A', 'E', 'I', 'O', 'U'], $header);
        $header = preg_replace('/\s+/', ' ', $header);

        return $header;
    }

    protected function headerMatches(string $value, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $keyword = $this->normalizeHeader($keyword);
            if (str_contains($value, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeText($value): ?string
    {
        $value = trim((string) $value);
        return $value !== '' ? $value : null;
    }
}
