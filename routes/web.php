<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ProcessoContratacaoController;
use App\Http\Controllers\ProcessoEtapaController;
use App\Http\Controllers\ProcessoAnexoController;
use App\Http\Controllers\ProcessoAditivoController;
use App\Http\Controllers\DocumentoAnaliseController;
use App\Http\Controllers\EtapaTemplateController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;



/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('auth/login'));
Route::get('/password/reset', fn () => view('auth/forgot-password'));

Route::get('/dashmetabase', [DashController::class, 'showDashboard'])
    ->middleware('allow.metabase.csp')
    ->name('dashmetabase');

Route::get('/home', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('home');

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::post('/widgets', [DashboardController::class, 'storeWidget'])->name('widgets.store');
    Route::delete('/widgets/{widget}', [DashboardController::class, 'destroyWidget'])->name('widgets.destroy');
    Route::post('/layouts', [DashboardController::class, 'saveLayout'])->name('layouts.store');
    Route::post('/filters', [DashboardController::class, 'storeFilter'])->name('filters.store');
    Route::delete('/filters/{filter}', [DashboardController::class, 'destroyFilter'])->name('filters.destroy');
});

Route::get('/empresas/buscar-cnpj/{cnpj}', [EmpresaController::class, 'buscarcnpj']) ->name('empresas.buscarcnpj');
/*
|--------------------------------------------------------------------------
| EMPRESAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('empresas')->name('empresas.')->group(function () {
    Route::get('/', [EmpresaController::class, 'index'])->name('index');
    Route::get('/data', [EmpresaController::class, 'data'])->name('data');
    Route::get('/stats', [EmpresaController::class, 'stats'])->name('stats');
    Route::get('/create', [EmpresaController::class, 'create'])->name('create');
    Route::post('/', [EmpresaController::class, 'store'])->name('store');
    Route::get('/{empresa}', [EmpresaController::class, 'show'])->name('show');
    Route::get('/{empresa}/edit', [EmpresaController::class, 'edit'])->name('edit');
    Route::put('/{empresa}', [EmpresaController::class, 'update'])->name('update');
    Route::delete('/{empresa}', [EmpresaController::class, 'destroy'])->name('destroy');


});

/*
|--------------------------------------------------------------------------
| PROCESSOS DE CONTRATAÇÃO
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('processos-contratacao')->name('processos-contratacao.')->group(function () {
    Route::get('/', [ProcessoContratacaoController::class, 'index'])->name('index');
    Route::get('/data', [ProcessoContratacaoController::class, 'data'])->name('data');
    Route::get('/stats', [ProcessoContratacaoController::class, 'stats'])->name('stats');
    Route::get('/create', [ProcessoContratacaoController::class, 'create'])->name('create');
    Route::post('/', [ProcessoContratacaoController::class, 'store'])->name('store');
    Route::get('/{processoContratacao}', [ProcessoContratacaoController::class, 'show'])->name('show');
    Route::get('/{processoContratacao}/edit', [ProcessoContratacaoController::class, 'edit'])->name('edit');
    Route::put('/{processoContratacao}', [ProcessoContratacaoController::class, 'update'])->name('update');
    Route::patch('/{processoContratacao}/resumo-geral', [ProcessoContratacaoController::class, 'updateResumoGeral'])->name('resumo-geral.update');
    Route::delete('/{processoContratacao}', [ProcessoContratacaoController::class, 'destroy'])->name('destroy');

    Route::post('/documentos/preview-extracao', [DocumentoAnaliseController::class, 'preview'])->name('documentos.preview-extracao');

    Route::post('/{processoContratacao}/anexos', [ProcessoAnexoController::class, 'store'])->name('anexos.store');
    Route::get('/anexos/{anexo}/download', [ProcessoAnexoController::class, 'download'])->name('anexos.download');
    Route::delete('/anexos/{anexo}', [ProcessoAnexoController::class, 'destroy'])->name('anexos.destroy');

    Route::post('/{processoContratacao}/aditivos', [ProcessoAditivoController::class, 'store'])->name('aditivos.store');
    Route::put('/aditivos/{aditivo}', [ProcessoAditivoController::class, 'update'])->name('aditivos.update');
    Route::delete('/aditivos/{aditivo}', [ProcessoAditivoController::class, 'destroy'])->name('aditivos.destroy');

    Route::put('/etapas/{etapa}', [ProcessoEtapaController::class, 'update'])->name('etapas.update');
    Route::post('/etapas-extra', [ProcessoEtapaController::class, 'storeExtra'])->name('etapas-extra.store');
    Route::post('/{processoContratacao}/extrair-dados', [DocumentoAnaliseController::class, 'extrairDadosProcesso'])
    ->name('extrair-dados');

    Route::post('/{processoContratacao}/itens/importar', [ProcessoAnexoController::class, 'importarItens'])
    ->name('itens.importar');

    Route::put('/{processoContratacao}/resumo-geral',  [ProcessoContratacaoController::class, 'updateResumoGeral'])->name('processos-contratacao.resumo-geral.update');

});


Route::middleware(['auth'])->prefix('etapas-padrao')->name('etapas-padrao.')->group(function () {
    Route::get('/', [EtapaTemplateController::class, 'index'])->name('index');
    Route::get('/data', [EtapaTemplateController::class, 'data'])->name('data');
    Route::get('/create', [EtapaTemplateController::class, 'create'])->name('create');
    Route::post('/', [EtapaTemplateController::class, 'store'])->name('store');
    Route::get('/{etapaTemplate}/edit', [EtapaTemplateController::class, 'edit'])->name('edit');
    Route::put('/{etapaTemplate}', [EtapaTemplateController::class, 'update'])->name('update');
    Route::delete('/{etapaTemplate}', [EtapaTemplateController::class, 'destroy'])->name('destroy');
    Route::post('/{etapaTemplate}/toggle', [EtapaTemplateController::class, 'toggle'])->name('toggle');
    Route::post('/{etapaTemplate}/duplicar', [EtapaTemplateController::class, 'duplicar'])->name('duplicar');
});




Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assignPermissions');

    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::post('users/{user}/roles', [AdminUserController::class, 'assignRoles'])->name('users.assignRoles');
    Route::post('users/{user}/permissions', [AdminUserController::class, 'assignDirectPermissions'])->name('users.assignDirectPermissions');
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

});

Route::middleware(['auth'])->prefix('setores')->name('setores.')->group(function () {
    Route::get('/', [SetorController::class, 'index'])->name('index');
    Route::get('/data', [SetorController::class, 'data'])->name('data');
    Route::get('/create', [SetorController::class, 'create'])->name('create');
    Route::post('/', [SetorController::class, 'store'])->name('store');
    Route::get('/{setor}/edit', [SetorController::class, 'edit'])->name('edit');
    Route::put('/{setor}', [SetorController::class, 'update'])->name('update');
    Route::delete('/{setor}', [SetorController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

require __DIR__.'/auth.php';
