<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetorRequest;
use App\Http\Requests\UpdateSetorRequest;
use App\Models\Setor;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SetorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view setores')->only(['index', 'data']);
        $this->middleware('permission:create setores')->only(['create', 'store']);
        $this->middleware('permission:edit setores')->only(['edit', 'update']);
        $this->middleware('permission:delete setores')->only(['destroy']);
    }

    public function index()
    {
        return view('admin.processos_contratacao.setores.index');
    }

    public function data(Request $request)
    {
        $query = Setor::query()->select('setores.*');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        return DataTables::of($query)
            ->addColumn('usuarios_count', function ($setor) {
                return \DB::table('setor_user')
                    ->where('setor_id', $setor->id)
                    ->count();
            })
            ->addColumn('ativo_badge', function ($setor) {
                return $setor->ativo
                    ? '<span class="badge badge-success">Ativo</span>'
                    : '<span class="badge badge-secondary">Inativo</span>';
            })
            ->addColumn('acoes', function ($setor) {
                return view('admin.processos_contratacao.setores._actions', compact('setor'))->render();
            })
            ->rawColumns(['ativo_badge', 'acoes'])
            ->make(true);
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get(['id', 'name']);
        return view('admin.processos_contratacao.setores.create', compact('usuarios'));
    }

    public function store(StoreSetorRequest $request)
    {
        $data = $request->validated();
        $data['nome'] = mb_strtoupper(trim($data['nome']), 'UTF-8');
        $data['ativo'] = $request->boolean('ativo', true);

        $setor = Setor::create($data);
        $setor->usuarios()->sync($this->montarPermissoesPivot($request));

        return redirect()->route('setores.index')->with('success', 'Setor cadastrado com sucesso.');
    }

    public function edit(Setor $setor)
    {
        $usuarios = User::orderBy('name')->get(['id', 'name']);
        $setor->load('usuarios');
        return view('admin.processos_contratacao.setores.edit', compact('setor', 'usuarios'));
    }

    public function update(UpdateSetorRequest $request, Setor $setor)
    {
        $data = $request->validated();
        $data['nome'] = mb_strtoupper(trim($data['nome']), 'UTF-8');
        $data['ativo'] = $request->boolean('ativo', false);

        $setor->update($data);
        $setor->usuarios()->sync($this->montarPermissoesPivot($request));

        return redirect()->route('setores.index')->with('success', 'Setor atualizado com sucesso.');
    }

    public function destroy(Setor $setor)
    {
        $setor->usuarios()->detach();
        $setor->delete();

        return redirect()->route('setores.index')->with('success', 'Setor excluído com sucesso.');
    }

    protected function montarPermissoesPivot(Request $request): array
    {
        $itens = $request->input('usuarios_permissoes', []);
        $sync = [];

        foreach ($itens as $item) {
            $userId = $item['user_id'] ?? null;
            if (!$userId) {
                continue;
            }

            $visualizar = (bool) ($item['pode_visualizar'] ?? false);
            $editar = (bool) ($item['pode_editar'] ?? false);
            $aprovar = (bool) ($item['pode_aprovar'] ?? false);
            $reprovar = (bool) ($item['pode_reprovar'] ?? false);

            if ($editar || $aprovar || $reprovar) {
                $visualizar = true;
            }

            $sync[$userId] = [
                'pode_visualizar' => $visualizar,
                'pode_editar' => $editar,
                'pode_aprovar' => $aprovar,
                'pode_reprovar' => $reprovar,
                'ativo' => (bool) ($item['ativo'] ?? true),
            ];
        }

        return $sync;
    }
}
