<?php

namespace App\Http\Controllers;

use App\Models\DashboardSavedFilter;
use App\Models\DashboardWidget;
use App\Models\DashboardUserLayout;
use App\Services\Dashboard\DashboardMetricsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:view dashboard']);
    }

    public function index(Request $request, DashboardMetricsService $service)
    {
        $data = $service->build($request->user(), $request);

        return view('admin.dashboard.index', $data);
    }

    public function storeWidget(Request $request): RedirectResponse
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'metric_key' => ['required', 'string', 'max:120'],
            'cor' => ['nullable', 'string', 'max:30'],
            'icone' => ['nullable', 'string', 'max:80'],
            'visivel_para_todos' => ['nullable', 'boolean'],
        ]);

        abort_unless($request->user()->can('create dashboard personalizado'), 403);

        DashboardWidget::create([
            'user_id' => $request->user()->id,
            'titulo' => $request->titulo,
            'tipo' => 'card',
            'metric_key' => $request->metric_key,
            'cor' => $request->cor,
            'icone' => $request->icone,
            'visivel_para_todos' => $request->user()->can('share dashboard personalizado') ? (bool) $request->boolean('visivel_para_todos') : false,
            'ordem' => ((int) DashboardWidget::where('user_id', $request->user()->id)->max('ordem')) + 1,
            'ativo' => true,
        ]);

        return back()->with('success', 'Widget salvo com sucesso.');
    }

    public function destroyWidget(Request $request, DashboardWidget $widget): RedirectResponse
    {
        abort_unless(
            $request->user()->can('delete dashboard personalizado')
            && ($widget->user_id === $request->user()->id || $request->user()->can('manage dashboard')),
            403
        );

        $widget->delete();

        return back()->with('success', 'Widget removido com sucesso.');
    }

    public function saveLayout(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'layout_json' => ['nullable', 'array'],
            'filtros_json' => ['nullable', 'array'],
            'padrao' => ['nullable', 'boolean'],
        ]);

        abort_unless($request->user()->can('edit dashboard personalizado'), 403);

        if ($request->boolean('padrao')) {
            DashboardUserLayout::where('user_id', $request->user()->id)->update(['padrao' => false]);
        }

        DashboardUserLayout::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'nome' => $request->nome,
            ],
            [
                'layout_json' => $request->input('layout_json', []),
                'filtros_json' => $request->input('filtros_json', []),
                'padrao' => $request->boolean('padrao'),
                'ativo' => true,
            ]
        );

        return back()->with('success', 'Layout salvo com sucesso.');
    }

    public function storeFilter(Request $request): RedirectResponse
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'publico' => ['nullable', 'boolean'],
        ]);

        abort_unless($request->user()->can('edit dashboard personalizado'), 403);

        DashboardSavedFilter::create([
            'user_id' => $request->user()->id,
            'nome' => $request->nome,
            'filtros_json' => Arr::except($request->all(), ['_token', 'nome', 'publico']),
            'publico' => $request->user()->can('share dashboard personalizado') ? (bool) $request->boolean('publico') : false,
            'ativo' => true,
        ]);

        return back()->with('success', 'Filtro salvo com sucesso.');
    }

    public function destroyFilter(Request $request, DashboardSavedFilter $filter): RedirectResponse
    {
        abort_unless(
            $request->user()->can('delete dashboard personalizado')
            && ($filter->user_id === $request->user()->id || $request->user()->can('manage dashboard')),
            403
        );

        $filter->delete();

        return back()->with('success', 'Filtro removido com sucesso.');
    }
}
