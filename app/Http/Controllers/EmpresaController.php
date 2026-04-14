<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view empresas')->only(['index', 'data', 'show']);
        $this->middleware('permission:create empresas')->only(['create', 'store']);
        $this->middleware('permission:edit empresas')->only(['edit', 'update']);
        $this->middleware('permission:delete empresas')->only(['destroy']);
    }

    public function index()
    {
        return view('admin.processos_contratacao.empresas.index');
    }

    // public function data()
    // {
    //     $query = Empresa::query()->select('empresas.*');

    //     return DataTables::of($query)
    //         ->addColumn('ativo_badge', function ($empresa) {
    //             return $empresa->ativo
    //                 ? '<span class="badge badge-success">Ativa</span>'
    //                 : '<span class="badge badge-secondary">Inativa</span>';
    //         })
    //         ->addColumn('actions', function ($empresa) {
    //             return view('admin.processos_contratacao.empresas._actions', compact('empresa'))->render();
    //         })
    //         ->rawColumns(['ativo_badge', 'actions'])
    //         ->make(true);
    // }

    public function create()
    {
        return view('admin.processos_contratacao.empresas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_empresa' => 'required|string|max:20',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:empresas,cnpj',
            'inscricao_estadual' => 'nullable|string|max:50',
            'inscricao_municipal' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'contato_principal' => 'nullable|string|max:255',
            'cargo_contato' => 'nullable|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:30',
            'conta' => 'nullable|string|max:30',
            'chave_pix' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'nullable|boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', true);

        $empresa = Empresa::create($data);

        return redirect()->route('empresas.show', $empresa)->with('success', 'Empresa cadastrada com sucesso.');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['processosComoContratada', 'processosComoContratante']);
        return view('admin.processos_contratacao.empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        return view('admin.processos_contratacao.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'tipo_empresa' => 'required|string|max:20',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:empresas,cnpj,' . $empresa->id,
            'inscricao_estadual' => 'nullable|string|max:50',
            'inscricao_municipal' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:30',
            'celular' => 'nullable|string|max:30',
            'contato_principal' => 'nullable|string|max:255',
            'cargo_contato' => 'nullable|string|max:255',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:50',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:30',
            'conta' => 'nullable|string|max:30',
            'chave_pix' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'ativo' => 'nullable|boolean',
        ]);

        $data['ativo'] = $request->boolean('ativo', false);

        $empresa->update($data);

        return redirect()->route('empresas.show', $empresa)->with('success', 'Empresa atualizada com sucesso.');
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();

        return redirect()->route('empresas.index')->with('success', 'Empresa excluída com sucesso.');
    }

    public function stats(Request $request)
    {
        $query = Empresa::query();

        if ($request->razao_social) {
            $query->where('razao_social', 'like', '%' . $request->razao_social . '%');
        }

        if ($request->cnpj) {
            $query->where('cnpj', 'like', '%' . $request->cnpj . '%');
        }

        if ($request->tipo_empresa) {
            $query->where('tipo_empresa', $request->tipo_empresa);
        }

        if ($request->ativo !== null && $request->ativo !== '') {
            $query->where('ativo', $request->ativo);
        }

        $base = clone $query;

        return response()->json([
            'total_empresas' => (clone $base)->count(),
            'ativas' => (clone $base)->where('ativo', 1)->count(),
            'com_processos' => (clone $base)
                ->where(function ($q) {
                    $q->has('processosComoContratada')
                    ->orHas('processosComoContratante');
                })
                ->count(),
            'contratadas' => (clone $base)
                ->whereIn('tipo_empresa', ['CONTRATADA', 'AMBAS'])
                ->count(),
        ]);
    }

    public function data(Request $request)
    {
        $query = Empresa::query()->select('empresas.*');

        if ($request->filled('razao_social')) {
            $query->where('razao_social', 'like', '%' . $request->razao_social . '%');
        }

        if ($request->filled('cnpj')) {
            $query->where('cnpj', 'like', '%' . $request->cnpj . '%');
        }

        if ($request->filled('tipo_empresa')) {
            $query->where('tipo_empresa', $request->tipo_empresa);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        return DataTables::of($query)
            ->addColumn('cidade_uf', function ($empresa) {
                $cidade = $empresa->cidade ?? '';
                $uf = $empresa->uf ?? '';
                return trim($cidade . ($uf ? '/' . $uf : ''));
            })
            ->addColumn('ativo_badge', function ($empresa) {
                return $empresa->ativo
                    ? '<span class="badge badge-success">Ativa</span>'
                    : '<span class="badge badge-secondary">Inativa</span>';
            })
            ->addColumn('actions', function ($empresa) {
                return view('admin.processos_contratacao.empresas._actions', compact('empresa'))->render();
            })
            ->rawColumns(['ativo_badge', 'actions'])
            ->make(true);
    }

    public function buscarcnpj($cnpj)
{
    try {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json([
                'success' => false,
                'message' => 'CNPJ inválido.'
            ], 422);
        }

        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
            'verify' => false,
        ]);

        $response = $client->request('GET', 'https://brasilapi.com.br/api/cnpj/v1/' . $cnpj);

        if ($response->getStatusCode() !== 200) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível consultar o CNPJ.'
            ], $response->getStatusCode());
        }

        $dados = json_decode($response->getBody()->getContents(), true);

        return response()->json([
            'success' => true,
            'data' => [
                'cnpj' => $cnpj,
                'razao_social' => $dados['razao_social'] ?? null,
                'nome_fantasia' => $dados['nome_fantasia'] ?? null,
                'email' => $dados['email'] ?? null,
                'telefone' => $dados['ddd_telefone_1'] ?? null,
                'cep' => $dados['cep'] ?? null,
                'cidade' => $dados['municipio'] ?? null,
                'uf' => $dados['uf'] ?? null,
                'bairro' => $dados['bairro'] ?? null,
                'numero' => $dados['numero'] ?? null,
                'endereco' => trim(
                    (($dados['descricao_tipo_de_logradouro'] ?? '') ? ($dados['descricao_tipo_de_logradouro'] . ' ') : '') .
                    ($dados['logradouro'] ?? '')
                ),
                'complemento' => $dados['complemento'] ?? null,
            ]
        ]);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
        return response()->json([
            'success' => false,
            'message' => 'CNPJ não encontrado na API.'
        ], 404);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao consultar o CNPJ.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
}
