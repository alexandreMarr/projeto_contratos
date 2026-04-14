<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProcessoAditivoRequest;
use App\Http\Requests\UpdateProcessoAditivoRequest;
use App\Models\EtapaTemplate;
use App\Models\ProcessoAditivo;
use App\Models\ProcessoContratacao;
use App\Models\ProcessoHistorico;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProcessoAditivoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:edit processos contratacao')->only(['store', 'update', 'destroy']);
    }

    public function store(StoreProcessoAditivoRequest $request, ProcessoContratacao $processoContratacao)
    {
        if (!$processoContratacao->contrato_etapas_concluidas) {
            return redirect()->route('processos-contratacao.show', $processoContratacao)
                ->with('error', 'Você só pode cadastrar aditivo após a conclusão de todas as etapas do contrato.');
        }

        $data = $this->buildPayload($request->validated(), $processoContratacao);
        $data['processo_contratacao_id'] = $processoContratacao->id;
        $data['numero_documento'] = $this->generateNumeroDocumento($processoContratacao);

        $aditivo = ProcessoAditivo::create($data);

        $this->gerarEtapasDoAditivo($processoContratacao, $aditivo);

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processoContratacao->id,
            'tipo_evento' => 'ADITIVO_CRIADO',
            'descricao' => "Aditivo '{$aditivo->numero_documento}' criado.",
            'dados_json' => $data,
            'user_id' => Auth::id(),
        ]);

        activity('processo_contratacao')
            ->performedOn($processoContratacao)
            ->causedBy(Auth::user())
            ->withProperties($data)
            ->log("Aditivo {$aditivo->numero_documento} criado");

        return redirect()->route('processos-contratacao.show', $processoContratacao)
            ->with('success', 'Aditivo cadastrado com sucesso.');
    }

    public function update(UpdateProcessoAditivoRequest $request, ProcessoAditivo $aditivo)
    {
        $data = $this->buildPayload($request->validated(), $aditivo->processo);
        $data['numero_documento'] = $aditivo->numero_documento;

        $antes = $aditivo->only([
            'titulo', 'numero_documento', 'vigencia_anterior_fim', 'vigencia_nova_fim', 'valor_anterior',
            'valor_executado_medicao', 'saldo_contrato_anterior', 'valor_aditivo', 'valor_novo', 'percentual_aditivo'
        ]);

        $aditivo->update($data);

        ProcessoHistorico::create([
            'processo_contratacao_id' => $aditivo->processo_contratacao_id,
            'tipo_evento' => 'ADITIVO_ATUALIZADO',
            'descricao' => "Aditivo '{$aditivo->numero_documento}' atualizado.",
            'dados_json' => ['antes' => $antes, 'depois' => $data],
            'user_id' => Auth::id(),
        ]);

        activity('processo_contratacao')
            ->performedOn($aditivo->processo)
            ->causedBy(Auth::user())
            ->withProperties(['antes' => $antes, 'depois' => $data])
            ->log("Aditivo {$aditivo->numero_documento} atualizado");

        return back()->with('success', 'Aditivo atualizado com sucesso.');
    }

    public function destroy(ProcessoAditivo $aditivo)
    {
        $processo = $aditivo->processo;
        $numero = $aditivo->numero_documento;

        $aditivo->delete();

        ProcessoHistorico::create([
            'processo_contratacao_id' => $processo->id,
            'tipo_evento' => 'ADITIVO_REMOVIDO',
            'descricao' => "Aditivo '{$numero}' removido.",
            'dados_json' => [],
            'user_id' => Auth::id(),
        ]);

        activity('processo_contratacao')
            ->performedOn($processo)
            ->causedBy(Auth::user())
            ->log("Aditivo {$numero} removido");

        return back()->with('success', 'Aditivo removido com sucesso.');
    }

    protected function buildPayload(array $validated, ProcessoContratacao $processo): array
    {
        $valorAnterior = $this->resolveValorContratoAtual($processo);
        $realizadoTotal = filter_var($validated['contrato_realizado_total'] ?? false, FILTER_VALIDATE_BOOL);
        $valorExecutado = $realizadoTotal
            ? $valorAnterior
            : $this->parseMoney($validated['valor_executado_medicao'] ?? null);

        if ($valorExecutado === null) {
            $valorExecutado = 0.0;
        }

        if ($valorExecutado > $valorAnterior) {
            $valorExecutado = $valorAnterior;
        }

        $saldoContratoAnterior = max($valorAnterior - $valorExecutado, 0);
        $valorAditivo = $this->parseMoney($validated['valor_aditivo'] ?? null) ?? 0.0;
        $novoValorContrato = $valorAnterior + $valorAditivo;
        $percentualAditivo = $valorAnterior > 0
            ? round(($valorAditivo / $valorAnterior) * 100, 6)
            : ($valorAditivo > 0 ? INF : 0.0);

        return [
            'tipo' => $validated['tipo'],
            'titulo' => $validated['titulo'],
            'descricao' => $validated['titulo'],
            'objeto' => $validated['objeto'],
            'escopo' => $validated['escopo'],
            'data_referencia' => $validated['data_referencia'] ?? null,
            'boletim_medicao' => $validated['boletim_medicao'] ?? null,
            'contrato_realizado_total' => $realizadoTotal,
            'valor_anterior' => $valorAnterior,
            'valor_executado_medicao' => $valorExecutado,
            'saldo_contrato_anterior' => $saldoContratoAnterior,
            'valor_aditivo' => $valorAditivo,
            'valor_novo' => $novoValorContrato,
            'diferenca_valor' => $valorAditivo,
            'percentual_aditivo' => is_infinite($percentualAditivo) ? null : $percentualAditivo,
            'exige_aprovacao_conselho' => is_infinite($percentualAditivo) ? true : $percentualAditivo > 30,
            'vigencia_anterior_fim' => $validated['vigencia_anterior_fim'] ?? null,
            'vigencia_nova_fim' => $validated['vigencia_nova_fim'] ?? null,
            'observacoes' => $validated['observacoes'] ?? null,
            'anexo_id' => $validated['anexo_id'] ?? null,
        ];
    }

    protected function resolveValorContratoAtual(ProcessoContratacao $processo): float
    {
        if ($processo->relationLoaded('aditivos') ? $processo->aditivos->isNotEmpty() : $processo->aditivos()->exists()) {
            $ultimo = $processo->relationLoaded('aditivos')
                ? $processo->aditivos->sortByDesc('created_at')->first()
                : $processo->aditivos()->latest()->first();

            if ($ultimo && $ultimo->valor_novo !== null) {
                return (float) $ultimo->valor_novo;
            }
        }

        return (float) ($processo->valor_aprovado_final ?: $processo->valor_proposto ?: $processo->valor_estimado ?: 0);
    }

    protected function parseMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return round((float) $value, 2);
        }

        $value = trim((string) $value);
        $value = str_replace(['R$', ' '], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? round((float) $value, 2) : null;
    }

    protected function generateNumeroDocumento(ProcessoContratacao $processo): string
    {
        $sequencia = $processo->aditivos()->count() + 1;
        $base = $processo->numero_contrato_assinado ?: $processo->numero_processo_interno ?: ('PC-' . str_pad((string) $processo->id, 6, '0', STR_PAD_LEFT));
        $base = strtoupper(Str::of($base)->replace([' ', '/'], '-')->__toString());

        return 'ADT-' . $base . '-' . str_pad((string) $sequencia, 3, '0', STR_PAD_LEFT);
    }

    protected function gerarEtapasDoAditivo(ProcessoContratacao $processo, ProcessoAditivo $aditivo): void
    {
        $ordensPermitidas = [1, 2, 4, 5, 7];

        $templates = EtapaTemplate::query()
            ->where('ativo', true)
            ->whereIn('ordem', $ordensPermitidas)
            ->orderBy('ordem')
            ->get();

        $dataBase = $aditivo->data_referencia
            ? $aditivo->data_referencia->toDateString()
            : now()->toDateString();

        foreach ($templates as $template) {
            $inicio = $template->ordem == $templates->min('ordem') ? $dataBase : null;
            $limite = $template->ordem == $templates->min('ordem')
                ? now()->parse($dataBase)->addDays((int) $template->prazo_limite_dias)->toDateString()
                : null;

            $processo->etapas()->create([
                'origem_tipo' => 'ADITIVO',
                'processo_aditivo_id' => $aditivo->id,
                'etapa_template_id' => $template->id,
                'nome_etapa' => $template->nome,
                'ordem' => $template->ordem,
                'setor_id' => $template->setor_id,
                'setor_responsavel' => $template->setor_responsavel,
                'prazo_limite_dias' => $template->prazo_limite_dias,
                'data_inicio' => $inicio,
                'data_limite' => $limite,
                'data_prazo_original' => $limite,
                'status' => $template->ordem == $templates->min('ordem') ? 'LIBERADA' : 'BLOQUEADA',
                'cor_badge' => $template->cor_badge,
                'permite_anexo' => $template->permite_anexo,
            ]);
        }
    }
}
