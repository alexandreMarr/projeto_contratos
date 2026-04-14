<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessoAnexoRequest;
use App\Services\DocumentoExtracaoService;
use App\Http\Requests\PreviewDocumentoRequest;

use App\Models\ProcessoContratacao;
use App\Models\ProcessoAnexo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DocumentoAnaliseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit processos contratacao');
    }

    public function preview(PreviewDocumentoRequest $request, DocumentoExtracaoService $service)
    {
        $arquivo = $request->file('arquivo');
        $resultado = $service->extrair($arquivo);

        return response()->json([
            'message' => 'Pré-análise concluída com sucesso.',
            'resultado' => $resultado,
            'html' => view('admin.processos_contratacao.processos._extracao_preview', [
                'resultado' => $resultado,
            ])->render(),
        ]);
    }

//     public function extrairDadosProcesso(ProcessoContratacao $processoContratacao, Request $request, DocumentoExtracaoService $service)
// {
//     $anexo = $processoContratacao->anexos()
//         ->whereIn('tipo_anexo', ['PROPOSTA_PDF', 'PLANILHA_SERVICOS'])
//         ->orderByDesc('id')
//         ->first();

//     if (!$anexo) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Nenhum anexo de proposta ou planilha foi encontrado para este processo.'
//         ], 404);
//     }

//     $caminho = storage_path('app/public/' . $anexo->caminho_arquivo);

//     if (!file_exists($caminho)) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Arquivo não encontrado no storage.'
//         ], 404);
//     }

//     $uploadedFile = new \Illuminate\Http\UploadedFile(
//         $caminho,
//         $anexo->nome_original,
//         $anexo->mime_type,
//         null,
//         true
//     );

//     $resultado = $service->extrair($uploadedFile);

//     $anexo->update([
//         'extraido_com_sucesso' => true,
//         'dados_extraidos_json' => $resultado,
//     ]);

//     return response()->json([
//         'success' => true,
//         'message' => 'Leitura concluída com sucesso.',
//         'resultado' => $resultado,
//         'html' => view('admin.processos_contratacao.processos._extracao_preview', [
//             'resultado' => $resultado,
//         ])->render(),
//     ]);
// }

public function extrairDadosProcesso(
    \App\Models\ProcessoContratacao $processoContratacao,
    \Illuminate\Http\Request $request,
    \App\Services\DocumentoExtracaoService $service,
    \App\Services\ProcessoItemImportService $itemImportService
) {
    $proposta = $processoContratacao->anexos()
        ->where('tipo_anexo', 'PROPOSTA_PDF')
        ->latest()
        ->first();

    $planilha = $processoContratacao->anexos()
        ->where('tipo_anexo', 'PLANILHA_SERVICOS')
        ->latest()
        ->first();

    if (!$proposta && !$planilha) {
        return response()->json([
            'success' => false,
            'message' => 'Nenhum anexo de proposta ou planilha foi encontrado para este processo.'
        ], 404);
    }

    $resultadoProposta = null;
    $resultadoPlanilha = null;

    if ($proposta) {
        $caminhoProposta = storage_path('app/public/' . $proposta->caminho_arquivo);

        if (file_exists($caminhoProposta)) {
            $uploadedProposta = new \Illuminate\Http\UploadedFile(
                $caminhoProposta,
                $proposta->nome_original,
                $proposta->mime_type,
                null,
                true
            );

            $resultadoProposta = $service->extrair($uploadedProposta);

            $proposta->update([
                'extraido_com_sucesso' => true,
                'dados_extraidos_json' => $resultadoProposta,
            ]);
        }
    }

    if ($planilha) {
        $caminhoPlanilha = storage_path('app/public/' . $planilha->caminho_arquivo);

        if (file_exists($caminhoPlanilha)) {
            $uploadedPlanilha = new \Illuminate\Http\UploadedFile(
                $caminhoPlanilha,
                $planilha->nome_original,
                $planilha->mime_type,
                null,
                true
            );

            $resultadoPlanilha = $service->extrair($uploadedPlanilha);

            $planilha->update([
                'extraido_com_sucesso' => true,
                'dados_extraidos_json' => $resultadoPlanilha,
            ]);
        }
    }

    $dadosMesclados = [
        'razao_social' => data_get($resultadoProposta, 'dados.razao_social'),
        'nome_fantasia' => data_get($resultadoProposta, 'dados.nome_fantasia'),
        'cnpj' => data_get($resultadoProposta, 'dados.cnpj'),
        'email' => data_get($resultadoProposta, 'dados.email'),
        'telefone' => data_get($resultadoProposta, 'dados.telefone'),
        'objeto_resumido' => data_get($resultadoProposta, 'dados.objeto_resumido'),
        'titulo' => data_get($resultadoProposta, 'dados.titulo'),
        'valor_proposto' => data_get($resultadoProposta, 'dados.valor_proposto'),
        'prazo_pagamento_dias' => data_get($resultadoProposta, 'dados.prazo_pagamento_dias'),
        'validade_proposta' => data_get($resultadoProposta, 'dados.validade_proposta'),
        'prazo_execucao_inicio' => data_get($resultadoProposta, 'dados.prazo_execucao_inicio'),
        'prazo_execucao_fim' => data_get($resultadoProposta, 'dados.prazo_execucao_fim'),
        'vigencia_inicio' => data_get($resultadoProposta, 'dados.vigencia_inicio'),
        'vigencia_fim' => data_get($resultadoProposta, 'dados.vigencia_fim'),
        'dados_bancarios' => data_get($resultadoProposta, 'dados.dados_bancarios', []),
        'responsavel' => data_get($resultadoProposta, 'dados.responsavel'),
        'locais' => array_values(array_unique(array_merge(
            data_get($resultadoProposta, 'dados.locais', []),
            data_get($resultadoPlanilha, 'dados.locais', [])
        ))),
        'itens' => data_get($resultadoPlanilha, 'dados.itens', []),
        'texto_base_resumo' => trim(
            (data_get($resultadoProposta, 'dados.texto_base_resumo', '') ?: '') .
            "\n\n" .
            (data_get($resultadoPlanilha, 'dados.texto_base_resumo', '') ?: '')
        ),
    ];

    $resultadoFinal = [
        'sucesso' => true,
        'arquivo' => [
            'nome_original' => 'Proposta + Planilha',
            'mime_type' => 'multiple',
            'extensao' => 'mixed',
        ],
        'dados' => $dadosMesclados,
        'metadados' => [
            'confianca' => 0.85,
            'fonte_texto' => 'pdf+excel',
            'observacoes' => [
                'Dados consolidados da proposta e da planilha.',
                'Os dados devem ser conferidos antes do uso definitivo.',
            ],
        ],
    ];

    if (!empty($dadosMesclados['itens']) && $planilha) {
        $processoContratacao->itens()->delete();
        $itemImportService->importarItensExtraidos($processoContratacao, $planilha);
    }

    $processoContratacao->update([
        'dados_extraidos_json' => $resultadoFinal,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Leitura da proposta e planilha concluída com sucesso.',
        'resultado' => $resultadoFinal,
        'html' => view('admin.processos_contratacao.processos._extracao_preview', [
            'resultado' => $resultadoFinal,
        ])->render(),
    ]);
}
}
