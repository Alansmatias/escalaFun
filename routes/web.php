<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\EscalaController;
use App\Http\Controllers\RelatorioController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Rota Inicial - Página Home
 */
Route::get('/', function () {
    return view('site/home');
})->name('home');

/**
 * Rotas Relacionadas a Escala
 */
Route::get('/escala', [EscalaController::class, 'listaEscala'])->name('escala'); //Página principal da Rota
Route::get('/escalarFuncionario', [EscalaController::class, 'escalar'])->name('escalarfun'); //Página principal da Rota
Route::post('/escalar/salvar', [EscalaController::class, 'store'])->name('escalar.salvar'); //Salvar registro na tabela do banco
Route::post('/escalar/atualizar', [EscalaController::class, 'update'])->name('escalar.atualizar');
Route::post('#', [EscalaController::class, '#'])->name('escalar.remover');
Route::get('/escala/automatica', [EscalaController::class, 'escalaautomatica'])->name('escala.automatica'); //Escala automatica
Route::post('/gerar-escala', [EscalaController::class, 'gerarEscala'])->name('escala.gerar'); //salva Escala automatica
Route::get('/escala/ausencia', [EscalaController::class, 'registrarAusencia'])->name('escala.ausencia');



/**
 * Rotas Relacionadas a Funcionário
 */
Route::get('/Funcionario', [FuncionarioController::class, 'lista'])->name('home.lista.funcionario'); //Página onde lista os funcionários
Route::get('/cadastroFuncionario', [FuncionarioController::class, 'create'])->name('home.cadastro.funcionario'); //Página para cadastro do funcionário
Route::post('/funcionarios', [FuncionarioController::class, 'store'])->name('funcionario.store'); //Inserção dos dados na tabela
Route::get('/funcionario/{id}/edit', [FuncionarioController::class, 'edit'])->name('funcionario.edit'); // Página de edição do funcionário
Route::put('/funcionario/{id}', [FuncionarioController::class, 'update'])->name('funcionario.update'); // Atualizar funcionário existente

/**
 * Rotas Relacionadas a Setor
 */
Route::get('/setor', [SetorController::class, 'lista'])->name('home.lista.setor'); //Listagem de setor
Route::get('/cadastroSetor', [SetorController::class, 'create'])->name('home.cadastro.setor'); //Página para cadastro do setor
Route::post('/setor', [SetorController::class, 'store'])->name('setor.store'); //Inserção dos dados na tabela
Route::get('/setor/{id}/edit', [SetorController::class, 'edit'])->name('setor.edit'); // Página de edição do setor //Pendente FAZER O CONTROLADOR
Route::put('/setor/{id}', [SetorController::class, 'update'])->name('setor.update'); // Atualizar setor existente //Pendente FAZER O CONTROLADOR

/**
 * Rotas Relacionadas a Turno
 */
Route::get('/turno', [TurnoController::class, 'lista'])->name('home.lista.turno');//Listagem de turno
Route::get('/cadastroTurno', [TurnoController::class, 'create'])->name('home.cadastro.turno');//Página para cadastro do turno
Route::post('/turno', [TurnoController::class, 'store'])->name('turno.store');// Armazenar novo turno
Route::get('/turno/{id}/edit', [TurnoController::class, 'edit'])->name('turno.edit'); // Página de edição do turno
Route::put('/turno/{id}', [TurnoController::class, 'update'])->name('turno.update'); // Atualizar turno existente

/**
 * Rotas Relacionadas a Relatório
 */
Route::get('/funcionarios_escalados_setor_turno', [RelatorioController::class, 'funcionarios_escalados_setor_turno'])->name('funcionarios_escalados_setor_turno');
