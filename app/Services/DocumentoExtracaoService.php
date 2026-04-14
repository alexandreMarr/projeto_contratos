<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentoExtracaoService
{
    public function extrair(UploadedFile $arquivo): array
    {
        $nome = $arquivo->getClientOriginalName();
        $mime = $arquivo->getMimeType();
        $ext = Str::lower($arquivo->getClientOriginalExtension());

        $texto = $this->obterTextoBase($arquivo, $ext);

        $intervaloExecucao = $this->extrairPrazoExecucao($texto);
        $vigencia = $this->extrairPrazoVigencia($texto);

        $dados = [
            'razao_social' => $this->extrairRazaoSocial($texto),
            'nome_fantasia' => $this->extrairNomeFantasia($texto),
            'cnpj' => $this->extrairCnpj($texto),
            'email' => $this->extrairEmail($texto),
            'telefone' => $this->extrairTelefone($texto),
            'objeto_resumido' => $this->extrairObjeto($texto),
            'titulo' => $this->extrairTitulo($texto),
            'valor_proposto' => $this->extrairValorTotal($texto),
            'prazo_pagamento_dias' => $this->extrairPrazoPagamentoDias($texto),
            'validade_proposta' => $this->extrairValidade($texto),
            'prazo_execucao_inicio' => $intervaloExecucao['inicio'],
            'prazo_execucao_fim' => $intervaloExecucao['fim'],
            'vigencia_inicio' => $vigencia['inicio'],
            'vigencia_fim' => $vigencia['fim'],
            'dados_bancarios' => $this->extrairDadosBancarios($texto),
            'responsavel' => $this->extrairResponsavel($texto),
            'locais' => $this->extrairLocaisKm($texto),
            'itens' => $this->extrairItensHeuristica($texto),
            'texto_base_resumo' => Str::limit(preg_replace('/\s+/', ' ', $texto), 2000),
        ];

        return [
            'sucesso' => true,
            'arquivo' => [
                'nome_original' => $nome,
                'mime_type' => $mime,
                'extensao' => $ext,
                'hash' => sha1_file($arquivo->getRealPath()),
                'tamanho_bytes' => $arquivo->getSize(),
            ],
            'dados' => $dados,
            'metadados' => [
                'confianca' => $this->calcularConfianca($dados),
                'fonte_texto' => $this->identificarFonteTexto($ext, $texto),
                'observacoes' => $this->montarObservacoes($dados),
            ],
        ];
    }

    protected function obterTextoBase(UploadedFile $arquivo, string $ext): string
    {
        if (in_array($ext, ['txt', 'csv'])) {
            return (string) @file_get_contents($arquivo->getRealPath());
        }

        if ($ext === 'pdf') {
            $texto = $this->tentarExtrairTextoPdf($arquivo);
            if (!empty(trim($texto ?? ''))) {
                return $this->normalizarTexto($texto);
            }
        }

        if (in_array($ext, ['xlsx', 'xls'])) {
            $texto = $this->tentarExtrairTextoExcel($arquivo);
            if (!empty(trim($texto ?? ''))) {
                return $this->normalizarTexto($texto);
            }
        }

        return $this->normalizarTexto($arquivo->getClientOriginalName());
    }

    protected function tentarExtrairTextoPdf(UploadedFile $arquivo): ?string
    {
        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($arquivo->getRealPath());
                return $pdf->getText();
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    protected function tentarExtrairTextoExcel(UploadedFile $arquivo): ?string
    {
        if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo->getRealPath());
                $linhas = [];

                foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
                    $linhas[] = 'ABA: ' . $sheet->getTitle();

                    foreach (array_slice($sheet->toArray(null, true, true, true), 0, 300) as $row) {
                        $linha = implode(' | ', array_filter(array_map(function ($v) {
                            return is_scalar($v) ? trim((string) $v) : '';
                        }, $row)));

                        if ($linha !== '') {
                            $linhas[] = $linha;
                        }
                    }
                }

                return implode("\n", $linhas);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    protected function normalizarTexto(string $texto): string
    {
        $texto = str_replace(["\r\n", "\r"], "\n", $texto);
        $texto = preg_replace('/[ \t]+/', ' ', $texto);
        $texto = preg_replace("/\n{2,}/", "\n", $texto);
        return trim($texto);
    }

    protected function identificarFonteTexto(string $ext, string $texto): string
    {
        if ($ext === 'pdf' && strlen($texto) > 50) {
            return 'pdf';
        }

        if (in_array($ext, ['xlsx', 'xls']) && strlen($texto) > 50) {
            return 'excel';
        }

        if (in_array($ext, ['txt', 'csv'])) {
            return 'texto';
        }

        return 'fallback_nome_arquivo';
    }

    protected function montarObservacoes(array $dados): array
    {
        $obs = ['Os dados devem ser conferidos antes do salvamento final.'];

        if (empty($dados['cnpj'])) {
            $obs[] = 'CNPJ não identificado automaticamente.';
        }

        if (empty($dados['razao_social'])) {
            $obs[] = 'Razão social não identificada automaticamente.';
        }

        if (empty($dados['valor_proposto'])) {
            $obs[] = 'Valor total não identificado automaticamente.';
        }

        if (!empty($dados['itens'])) {
            $obs[] = 'Foram encontrados itens que podem ser importados.';
        }

        return $obs;
    }

    protected function extrairRazaoSocial(string $texto): ?string
    {
        $padroes = [
            '/2\.\s*Dados do Contratado.*?Nome\/Raz[aã]o Social:\s*([^\n]+)/is',
            '/Nome empresarial\s*[–:-]\s*([^\n]+)/iu',
            '/Nome\/Raz[aã]o Social:\s*([^\n]+)/iu',
            '/RTC CONSTRUTORA LTDA/iu',
            '/SUPORTE SERVIÇOS DE CONSULTORIA E ENGENHARIA CIVIL EIRELI/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return trim($m[1] ?? $m[0]);
            }
        }

        return null;
    }

    protected function extrairNomeFantasia(string $texto): ?string
    {
        $padroes = [
            '/Nome fantasia\s*[–:-]\s*([^\n]+)/iu',
            '/SUPORTE ENGENHARIA/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return trim($m[1] ?? $m[0]);
            }
        }

        return null;
    }

    protected function extrairCnpj(string $texto): ?string
    {
        $cnpjs = [];
        preg_match_all('/\b\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}\b/', $texto, $matches);

        if (!empty($matches[0])) {
            $cnpjs = array_values(array_unique($matches[0]));
        }

        if (empty($cnpjs)) {
            return null;
        }

        foreach ($cnpjs as $cnpj) {
            if ($cnpj !== '60.437.929/0001-04') {
                return $cnpj;
            }
        }

        return $cnpjs[0];
    }

    protected function extrairEmail(string $texto): ?string
    {
        preg_match_all('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/i', $texto, $matches);

        if (!empty($matches[0])) {
            return implode(' / ', array_values(array_unique($matches[0])));
        }

        return null;
    }

    protected function extrairTelefone(string $texto): ?string
    {
        if (preg_match('/\(?\d{2}\)?\s?\d{4,5}\-\d{4}/', $texto, $m)) {
            return $m[0];
        }

        return null;
    }

    protected function extrairTitulo(string $texto): ?string
    {
        $padroes = [
            '/T[íi]tulo:\s*([^\n]+)/iu',
            '/OBRA:\s*([^\n]+)/iu',
            '/PROPOSTA COMERCIAL DE SERVIÇOS DE EXECUÇÃO DE\s*([^\n]+)/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return trim($m[1]);
            }
        }

        return null;
    }

    protected function extrairObjeto(string $texto): ?string
    {
        $padroes = [
            '/Objetivo:\s*([^\n]+)/iu',
            '/Objeto:\s*([^\n]+)/iu',
            '/PROPOSTA COMERCIAL DE SERVIÇOS DE EXECUÇÃO DE\s*([^\n]+)/iu',
            '/prestação de serviços de pavimento,\s*contemplando,?\s*([^\n]+)/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return trim($m[1]);
            }
        }

        return null;
    }

    protected function extrairValorTotal(string $texto): ?float
    {
        $padroes = [
            '/TOTAL GERAL\s*:?\s*R\$\s*([\d\.\,]+)/iu',
            '/TOTAL DOS SERVIÇOS\s*R\$\s*([\d\.\,]+)/iu',
            '/valor total proposto.*?R\$\s*([\d\.\,]+)/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return $this->normalizarMoeda($m[1]);
            }
        }

        return null;
    }

    protected function extrairPrazoPagamentoDias(string $texto): ?int
    {
        $padroes = [
            '/Prazo para pagamento:\s*(\d{1,3})\s*dias?/iu',
            '/Forma de pagamento,\s*(\d{1,3})\s*dias?/iu',
            '/(\d{1,3})\s*dias?\s*ap[oó]s emiss[aã]o da nota fiscal/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return (int) $m[1];
            }
        }

        return null;
    }

    protected function extrairValidade(string $texto): ?string
    {
        if (preg_match('/Validade da Proposta:\s*(\d{1,3})\s*dias/iu', $texto, $m)) {
            return $m[1] . ' dias';
        }

        return null;
    }

    protected function extrairPrazoExecucao(string $texto): array
    {
        if (preg_match('/Prazo de Execu[cç][aã]o:\s*(\d{2}\/\d{2}\/\d{4})\s*[àa-]+\s*(\d{2}\/\d{2}\/\d{4})/iu', $texto, $m)) {
            return [
                'inicio' => $this->normalizarDataBr($m[1]),
                'fim' => $this->normalizarDataBr($m[2]),
            ];
        }

        if (preg_match('/Prazo de conclus[aã]o da obra\s*(\d{2}\/\d{2}\/\d{4})/iu', $texto, $m)) {
            return [
                'inicio' => null,
                'fim' => $this->normalizarDataBr($m[1]),
            ];
        }

        return ['inicio' => null, 'fim' => null];
    }

    protected function extrairPrazoVigencia(string $texto): array
    {
        if (preg_match('/Prazo de Vig[êe]ncia Contratual:\s*(\d{1,3})\s*dias?\s*ap[oó]s prazo de execu[cç][aã]o/iu', $texto, $m)) {
            return [
                'inicio' => null,
                'fim' => $m[1] . ' dias após execução',
            ];
        }

        return ['inicio' => null, 'fim' => null];
    }

    protected function extrairDadosBancarios(string $texto): array
    {
        $dados = [];

        if (preg_match('/Banco:\s*([^\n]+)/iu', $texto, $m)) {
            $dados['banco'] = trim($m[1]);
        } elseif (preg_match('/Bco\s*[–:-]\s*([^\n]+)/iu', $texto, $m)) {
            $dados['banco'] = trim($m[1]);
        }

        if (preg_match('/Ag[êe]ncia:\s*([^\n]+)/iu', $texto, $m)) {
            $dados['agencia'] = trim($m[1]);
        } elseif (preg_match('/Ag\.\s*[–:-]\s*([^\n]+)/iu', $texto, $m)) {
            $dados['agencia'] = trim($m[1]);
        }

        if (preg_match('/Conta Corrente:\s*([^\n]+)/iu', $texto, $m)) {
            $dados['conta'] = trim($m[1]);
        } elseif (preg_match('/C\/c\s*[–:-]\s*([^\n]+)/iu', $texto, $m)) {
            $dados['conta'] = trim($m[1]);
        }

        if (preg_match('/Chave Pix.*?:\s*([^\n]+)/iu', $texto, $m)) {
            $dados['pix'] = trim($m[1]);
        }

        return $dados;
    }

    protected function extrairResponsavel(string $texto): ?string
    {
        $padroes = [
            '/Respons[aá]vel:\s*([^\n]+)/iu',
            '/Representante:\s*([^\n]+)/iu',
            '/BARBARA CARVALHO DA SILVA/iu',
            '/Wdson Gutierizz de Oliveira Alves/iu',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $texto, $m)) {
                return trim($m[1] ?? $m[0]);
            }
        }

        return null;
    }

    protected function extrairLocaisKm(string $texto): array
    {
        $kms = [];

        preg_match_all('/KM\s*\d{1,3}\s*\+?\s*\d{1,3}/iu', $texto, $matches);

        if (!empty($matches[0])) {
            foreach ($matches[0] as $km) {
                $km = strtoupper(trim(preg_replace('/\s+/', ' ', $km)));
                $km = str_replace('KM ', 'KM ', $km);
                $kms[] = $km;
            }
        }

        return array_values(array_unique($kms));
    }

    protected function extrairItensHeuristica(string $texto): array
    {
        $itens = [];
        $linhas = preg_split('/\n/', $texto);

        foreach ($linhas as $linha) {
            $linha = trim($linha);

            if ($linha === '') {
                continue;
            }

            if (preg_match('/^\d+(\.\d+)?\s+(.+?)\s+(und|cj|h|m|m²|m³|tkm)\s+/iu', $linha, $m)) {
                $descricao = trim($m[2]);
                $unidade = trim($m[3]);

                $itens[] = [
                    'descricao' => $descricao,
                    'unidade' => $unidade,
                    'quantidade' => $this->extrairPrimeiroNumeroDepoisDaUnidade($linha, $unidade),
                    'valor_unitario' => $this->extrairPenultimoValorMonetario($linha),
                    'valor_total' => $this->extrairUltimoValorMonetario($linha),
                ];
            }

            if (count($itens) >= 100) {
                break;
            }
        }

        return $itens;
    }

    protected function extrairPrimeiroNumeroDepoisDaUnidade(string $linha, string $unidade): ?float
    {
        $pattern = '/' . preg_quote($unidade, '/') . '\s+([\d\.\,]+)/iu';

        if (preg_match($pattern, $linha, $m)) {
            return $this->normalizarNumero($m[1]);
        }

        return null;
    }

    protected function extrairPenultimoValorMonetario(string $linha): ?float
    {
        preg_match_all('/R\$\s*([\d\.\,]+)/iu', $linha, $matches);

        if (!empty($matches[1]) && count($matches[1]) >= 2) {
            return $this->normalizarMoeda($matches[1][count($matches[1]) - 2]);
        }

        return null;
    }

    protected function extrairUltimoValorMonetario(string $linha): ?float
    {
        preg_match_all('/R\$\s*([\d\.\,]+)/iu', $linha, $matches);

        if (!empty($matches[1])) {
            return $this->normalizarMoeda(end($matches[1]));
        }

        return null;
    }

    protected function normalizarNumero(string $valor): float
    {
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        return (float) $valor;
    }

    protected function normalizarMoeda(string $valor): float
    {
        return $this->normalizarNumero($valor);
    }

    protected function normalizarDataBr(string $data): ?string
    {
        $partes = explode('/', trim($data));

        if (count($partes) !== 3) {
            return null;
        }

        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }

    protected function calcularConfianca(array $dados): float
    {
        $campos = collect($dados)->except(['texto_base_resumo']);
        $preenchidos = $campos->filter(function ($valor) {
            if (is_array($valor)) {
                return !empty($valor);
            }

            return !is_null($valor) && $valor !== '';
        })->count();

        return $campos->count() > 0
            ? round($preenchidos / $campos->count(), 2)
            : 0.0;
    }
}
