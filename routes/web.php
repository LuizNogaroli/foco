<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProcessoController;

Route::get('/', [ProcessoController::class, 'index'])->middleware(['auth', 'verified'])->name('processos.index');
Route::get('/processos/{processo}', [ProcessoController::class, 'show'])->middleware(['auth', 'verified'])->name('processos.show');
Route::post('/processos/{processo}/tramitar', [ProcessoController::class, 'tramitar'])->middleware(['auth', 'verified'])->name('processos.tramitar');
Route::post('/processos/{processo}/abrir', [ProcessoController::class, 'abrir'])->middleware(['auth', 'verified'])->name('processos.abrir');
Route::post('/processos/{processo}/devolver', [ProcessoController::class, 'devolver'])->middleware(['auth', 'verified'])->name('processos.devolver');
Route::post('/processos/{processo}/receber-devolucao', [ProcessoController::class, 'receberDevolucao'])->middleware(['auth', 'verified'])->name('processos.receber-devolucao');
Route::get('/processos/{processo}/historico', [ProcessoController::class, 'historico'])->middleware(['auth', 'verified'])->name('processos.historico');
Route::get('/processos/{processo}/historico/escolha', [ProcessoController::class, 'historicoEscolha'])->middleware(['auth', 'verified'])->name('processos.historico.escolha');
Route::get('/processos/{processo}/historico/modelo-b', [ProcessoController::class, 'historicoModeloB'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-b');
Route::get('/processos/{processo}/historico/modelo-c', [ProcessoController::class, 'historicoModeloC'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-c');
Route::get('/processos/{processo}/historico/modelo-d', [ProcessoController::class, 'historicoModeloD'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-d');
Route::get('/processos/{processo}/historico/modelo-e', [ProcessoController::class, 'historicoModeloE'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-e');
Route::get('/processos/{processo}/historico/modelo-f', [ProcessoController::class, 'historicoModeloF'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-f');
Route::get('/processos/{processo}/historico/modelo-g', [ProcessoController::class, 'historicoModeloG'])->middleware(['auth', 'verified'])->name('processos.historico.modelo-g');
Route::get('/api/vocacoes', [ProcessoController::class, 'getVocacoes'])->middleware(['auth', 'verified'])->name('api.vocacoes');

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\ConfiguracoesController;
Route::get('/configuracoes', [ConfiguracoesController::class, 'index'])->middleware(['auth', 'verified'])->name('configuracoes');

Route::post('/configuracoes/salvar', [ConfiguracoesController::class, 'salvar'])->name('configuracoes.salvar');
Route::post('/configuracoes/atualizar-status-processo', [ConfiguracoesController::class, 'atualizarStatusProcesso'])->name('configuracoes.atualizarStatusProcesso');

use App\Http\Controllers\EquipeController;
Route::get('/equipe', [EquipeController::class, 'index'])->middleware(['auth', 'verified'])->name('equipe.index');
Route::post('/equipe', [EquipeController::class, 'store'])->middleware(['auth', 'verified'])->name('equipe.store');
Route::delete('/equipe/{equipeServidor}', [EquipeController::class, 'destroy'])->middleware(['auth', 'verified'])->name('equipe.destroy');
Route::post('/equipe/importar', [EquipeController::class, 'importar'])->middleware(['auth', 'verified'])->name('equipe.importar');

use App\Http\Controllers\ServidorController;
Route::get('/servidores', [ServidorController::class, 'index'])->middleware(['auth', 'verified'])->name('servidores.index');
Route::post('/servidores', [ServidorController::class, 'store'])->middleware(['auth', 'verified'])->name('servidores.store');
Route::get('/servidores/{user}', [ServidorController::class, 'edit'])->middleware(['auth', 'verified'])->name('servidores.edit');
Route::put('/servidores/{user}', [ServidorController::class, 'update'])->middleware(['auth', 'verified'])->name('servidores.update');
Route::delete('/servidores/{user}', [ServidorController::class, 'destroy'])->middleware(['auth', 'verified'])->name('servidores.destroy');

use App\Http\Controllers\DraftController;
Route::post('/draft/save', [DraftController::class, 'save'])->middleware(['auth'])->name('draft.save');
Route::get('/draft/load', [DraftController::class, 'load'])->middleware(['auth'])->name('draft.load');
Route::post('/draft/clear', [DraftController::class, 'clear'])->middleware(['auth'])->name('draft.clear');
Route::post('/draft/clean-old', [DraftController::class, 'cleanOld'])->middleware(['auth'])->name('draft.cleanOld');
