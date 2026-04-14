<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessoAnexoRequest;
use App\Models\ProcessoAnexo;
use App\Models\ProcessoContratacao;
use App\Models\ProcessoHistorico;
use App\Services\DocumentoExtracaoService;
use App\Services\ProcessoItemImportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\PlanilhaItensImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProcessoAnexoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view processos contratacao')->only(['download']);
        $this->middleware('permission:edit processos contratacao')->only(['store', 'destroy']);
    }

    public function store(
        StoreProcessoAnexoRequest $request,
        ProcessoContratacao $processoContratacao,
        DocumentoExtracaoService $extracaoService,
        ProcessoItemImportService $itemImportService
    ) {
        $data = $request->validate([
        'tipo_anexo' => 'required|string|max:50',
        'arquivo' => 'required|file|max:20480|mimes:pdf,xls,xlsx,csv,doc,docx',
        'observacoes' => 'nullable|string',
        'executar_extracao' => 'nullable|boolean',
        'importar_itens' => 'nullable|boolean',
    ]);

        $arquivo = $request->file('arquivo');
        $path = $arquivo->store("processos_contratacao/{$processoContratacao->id}", 'public');

        $resultadoExtracao = null;
        $extraido = false;
        $qtdItensImportados = 0;

        if ($request->boolean('executar_extracao', true)) {
            $resultadoExtracao = $extracaoService->extrair($arquivo);
            $extraido = (bool) ($resultadoExtracao['sucesso'] ?? false);
        }

        $anexo = ProcessoAnexo::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_anexo' => $data['tipo_anexo'],
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho_arquivo' => $path,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'hash_arquivo' => sha1_file($arquivo->getRealPath()),
            'versao' => 1,
            'extraido_com_sucesso' => $extraido,
            'observacoes' => $data['observacoes'] ?? null,
            'dados_extraidos_json' => $resultadoExtracao,
        ]);

        if ($request->boolean('importar_itens') && $extraido) {
            $qtdItensImportados = $itemImportService->importarItensExtraidos($processoContratacao, $anexo);
        }

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_evento' => 'ANEXO_ADICIONADO',
            'descricao' => "Anexo '{$anexo->nome_original}' adicionado.",
            'dados_json' => [
                'tipo_anexo' => $anexo->tipo_anexo,
                'extraido' => $extraido,
                'itens_importados' => $qtdItensImportados,
            ],
            'user_id' => Auth::id(),
        ]);

        $mensagem = 'Anexo enviado com sucesso.';
        if ($qtdItensImportados > 0) {
            $mensagem .= " {$qtdItensImportados} item(ns) importado(s) automaticamente.";
        }

        return redirect()->route('processos-contratacao.show', $processoContratacao)
            ->with('success', $mensagem);
    }

    public function download(ProcessoAnexo $anexo)
    {
        return Storage::disk('public')->download($anexo->caminho_arquivo, $anexo->nome_original);
    }

    public function destroy(ProcessoAnexo $anexo)
    {
        $processoId = $anexo->processo_contratacao_id;
        $nome = $anexo->nome_original;

        if ($anexo->caminho_arquivo && Storage::disk('public')->exists($anexo->caminho_arquivo)) {
            Storage::disk('public')->delete($anexo->caminho_arquivo);
        }

        $anexo->delete();

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoId,
            'tipo_evento' => 'ANEXO_REMOVIDO',
            'descricao' => "Anexo '{$nome}' removido.",
            'dados_json' => [],
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Anexo removido com sucesso.');
    }

    public function importarItens(Request $request, \App\Models\ProcessoContratacao $processoContratacao, PlanilhaItensImportService $service)
{
    $data = $request->validate([
        'origem_tipo' => 'required|in:CONTRATO,ADITIVO',
        'aditivo_id' => 'nullable|exists:processo_aditivos,id',
        'arquivo_planilha' => 'required|file|mimes:xls,xlsx,csv|max:20480',
    ]);

    if ($data['origem_tipo'] === 'ADITIVO' && empty($data['aditivo_id'])) {
        return back()->withErrors(['aditivo_id' => 'Selecione o aditivo para importar os itens.'])->withInput();
    }

    DB::beginTransaction();

    try {
        $arquivo = $request->file('arquivo_planilha');
        $path = $arquivo->store("processos_contratacao/{$processoContratacao->id}/itens", 'public');

        $anexo = \App\Models\ProcessoAnexo::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_anexo' => 'PLANILHA_SERVICOS',
            'nome_original' => $arquivo->getClientOriginalName(),
            'caminho_arquivo' => $path,
            'mime_type' => $arquivo->getMimeType(),
            'tamanho_bytes' => $arquivo->getSize(),
            'hash_arquivo' => sha1_file($arquivo->getRealPath()),
            'versao' => 1,
            'extraido_com_sucesso' => false,
            'observacoes' => 'Planilha importada pela aba Itens.',
        ]);

        if ($data['origem_tipo'] === 'CONTRATO') {
            $processoContratacao->itens()
                ->where('origem_tipo', 'CONTRATO')
                ->delete();
        } else {
            $processoContratacao->itens()
                ->where('origem_tipo', 'ADITIVO')
                ->where('aditivo_id', $data['aditivo_id'])
                ->delete();
        }

        $resultado = $service->importar(
            $processoContratacao,
            $anexo,
            $data['origem_tipo'],
            $data['origem_tipo'] === 'ADITIVO' ? $data['aditivo_id'] : null
        );

        $anexo->update([
            'extraido_com_sucesso' => true,
        ]);

        DB::commit();

        return redirect()
            ->route('processos-contratacao.show', $processoContratacao)
            ->with('success', "Itens importados com sucesso. Total: {$resultado['inserted']} linha(s).");
    } catch (\Throwable $e) {
        DB::rollBack();

        return back()->withErrors([
            'arquivo_planilha' => 'Erro ao importar planilha: ' . $e->getMessage()
        ])->withInput();
    }
}
}
