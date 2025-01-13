<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Turno;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $setores = Setor::all();
        $turnos = Turno::all();

        /* para teste
        $setores = collect([
            (object) ['id' => 1, 'nome' => 'Financeiro'],
            (object) ['id' => 2, 'nome' => 'RH'],
            (object) ['id' => 3, 'nome' => 'TI'],
        ]);
        */

        return view('site/cadastro/funcionario', compact('setores', 'turnos'));
        
    }

    public function lista() //listar funcionários e mostrar na lista
    {
        $funcionarios = Funcionario::all();

        return view('site/lista/funcionario', compact('funcionarios'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|min:10|max:11',
            'contrato' => 'required|in:mensalista,intermitente',
            'domingo' => 'nullable|integer|min:1|max:4',
            'ativo' => 'required|boolean',
            'folga' => 'nullable|array',
            'folga.*' => 'in:dom,seg,ter,qua,qui,sex,sab',
            'setor' => 'required|array|min:1',
            'turno' => 'required|array|min:1'//
        ]);

        // Verificar se 'domingo' está definido
        $domingo = $validatedData['domingo'] ?? null;

        // Inserção dos dados na tabela
        $funcionario = Funcionario::create([
            'nome' => $validatedData['nome'],
            'telefone' => $validatedData['telefone'],
            'contrato' => $validatedData['contrato'],
            'domingo' => $domingo,
            'ativo' => $validatedData['ativo'],
        ]);

        // Inserção dos dias de folga na tabela 'funFolga'
        if (isset($validatedData['folga'])) {
            foreach ($validatedData['folga'] as $diaFolga) {
                DB::table('funFolga')->insert([
                    'id_funcionario' => $funcionario->id,
                    'folga' => $diaFolga
                ]);
            }
        }

        // Inserção na tabela 'funsetor' tabela que relaciona funcionario e setor
        if (isset($validatedData['setor'])) {
            foreach ($validatedData['setor'] as $iSetor) {
                DB::table('funsetor')->insert([
                    'id_funcionario' => $funcionario->id,
                    'id_setor' => $iSetor
                ]); 
            }
        }

        // Inserção na tabela 'funturno' tabela que relaciona funcionario e turno
        if (isset($validatedData['turno'])) {
            foreach ($validatedData['turno'] as $iTurno) {
                DB::table('funturno')->insert([
                    'id_funcionario' => $funcionario->id,
                    'id_turno' => $iTurno
                ]); 
            }
        }

        return redirect()->route('home.lista.funcionario')->with('success', 'Funcionário cadastrado com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(Funcionario $funcionario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funcionario $funcionario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        //
    }
}
