<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEtapaTemplateRequest;
use App\Http\Requests\UpdateEtapaTemplateRequest;
use App\Models\EtapaTemplate;
use App\Models\Setor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EtapaTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view etapas padrao')->only(['index', 'data']);
        $this->middleware('permission:create etapas padrao')->only(['create', 'store', 'duplicar']);
        $this->middleware('permission:edit etapas padrao')->only(['edit', 'update', 'toggle']);
        $this->middleware('permission:delete etapas padrao')->only(['destroy']);
    }

    public function index()
    {
        return view('admin.processos_contratacao.etapas_padrao.index');
    }

    public function data(Request $request)
    {
        $query = EtapaTemplate::with('setor')->select('etapa_templates.*');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        return DataTables::of($query)
            ->addColumn('setor_nome', fn($etapa) => $etapa->setor->nome ?? $etapa->setor_responsavel ?? '-')
            ->addColumn('ativo_badge', function ($etapa) {
                return $etapa->ativo
                    ? '<span class="badge badge-success">Ativa</span>'
                    : '<span class="badge badge-secondary">Inativa</span>';
            })
            ->addColumn('obrigatoria_badge', function ($etapa) {
                return $etapa->obrigatoria
                    ? '<span class="badge badge-primary">Sim</span>'
                    : '<span class="badge badge-light">Não</span>';
            })
            ->addColumn('acoes', function ($etapa) {
                return view('admin.processos_contratacao.etapas_padrao._actions', compact('etapa'))->render();
            })
            ->rawColumns(['ativo_badge', 'obrigatoria_badge', 'acoes'])
            ->make(true);
    }

    public function create()
    {
        $setores = \App\Models\Setor::where('ativo', true)->orderBy('nome')->get();

        return view('admin.processos_contratacao.etapas_padrao.create', compact('setores'));
    }

    public function store(StoreEtapaTemplateRequest $request)
    {
        $data = $request->validated();
        $data = $this->normalizarBooleans($request, $data);

        if (!empty($data['setor_id'])) {
            $setor = \App\Models\Setor::find($data['setor_id']);
            $data['setor_responsavel'] = $setor?->nome;
        }

        EtapaTemplate::create($data);

        return redirect()
            ->route('etapas-padrao.index')
            ->with('success', 'Etapa padrão cadastrada com sucesso.');
    }

    public function edit(EtapaTemplate $etapaTemplate)
    {
        $setores = \App\Models\Setor::where('ativo', true)->orderBy('nome')->get();

        return view('admin.processos_contratacao.etapas_padrao.edit', compact('etapaTemplate', 'setores'));
    }

    public function update(UpdateEtapaTemplateRequest $request, EtapaTemplate $etapaTemplate)
    {
        $data = $request->validated();
        $data = $this->normalizarBooleans($request, $data);

        if (!empty($data['setor_id'])) {
            $setor = \App\Models\Setor::find($data['setor_id']);
            $data['setor_responsavel'] = $setor?->nome;
        } else {
            $data['setor_responsavel'] = null;
        }

        $etapaTemplate->update($data);

        return redirect()
            ->route('etapas-padrao.index')
            ->with('success', 'Etapa padrão atualizada com sucesso.');
    }

    public function destroy(EtapaTemplate $etapaTemplate)
    {
        $etapaTemplate->delete();

        return redirect()
            ->route('etapas-padrao.index')
            ->with('success', 'Etapa padrão excluída com sucesso.');
    }

    public function toggle(EtapaTemplate $etapaTemplate)
    {
        $etapaTemplate->update([
            'ativo' => !$etapaTemplate->ativo,
        ]);

        return redirect()
            ->route('etapas-padrao.index')
            ->with('success', 'Status da etapa atualizado com sucesso.');
    }

    public function duplicar(EtapaTemplate $etapaTemplate)
    {
        $nova = $etapaTemplate->replicate();
        $nova->nome = $etapaTemplate->nome . ' (Cópia)';
        $nova->ordem = ((int) EtapaTemplate::max('ordem')) + 1;
        $nova->ativo = false;
        $nova->save();

        return redirect()
            ->route('etapas-padrao.edit', $nova)
            ->with('success', 'Etapa duplicada com sucesso.');
    }

    protected function normalizarBooleans(Request $request, array $data): array
    {
        $data['obrigatoria'] = $request->boolean('obrigatoria');
        $data['permite_anexo'] = $request->boolean('permite_anexo');
        $data['exige_parecer'] = $request->boolean('exige_parecer');
        $data['exige_aprovacao'] = $request->boolean('exige_aprovacao');
        $data['ativo'] = $request->boolean('ativo', true);

        return $data;
    }
}
