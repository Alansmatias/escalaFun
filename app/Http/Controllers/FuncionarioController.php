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
    public function edit($id)
    {
        $funcionario = Funcionario::findOrFail($id);
        $setores = Setor::all();
        $turnos = Turno::all();

        // Buscar as folgas diretamente no banco
        $folgasSelecionadas = DB::table('funfolga')
                                ->where('id_funcionario', $id)
                                ->pluck('folga')
                                ->toArray();
    
        return view('site.cadastro.funcionario', compact('funcionario', 'setores', 'turnos', 'folgasSelecionadas'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Buscar o funcionário
        $funcionario = Funcionario::findOrFail($id);
    
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
            'turno' => 'required|array|min:1'
        ]);
    
        // Atualizar os dados do funcionário
        $funcionario->update([
            'nome' => $validatedData['nome'],
            'telefone' => $validatedData['telefone'],
            'contrato' => $validatedData['contrato'],
            'domingo' => $validatedData['domingo'] ?? null,
            'ativo' => $validatedData['ativo'],
        ]);
    
        // Atualizar as folgas - remover antigas e inserir novas
        DB::table('funfolga')->where('id_funcionario', $id)->delete();
    
        if (!empty($validatedData['folga'])) {
            $novasFolgas = array_map(fn($folga) => [
                'id_funcionario' => $id,
                'folga' => $folga
            ], $validatedData['folga']);
    
            DB::table('funfolga')->insert($novasFolgas);
        }
    
        // Atualizar setores - remover antigos e inserir novos
        DB::table('funsetor')->where('id_funcionario', $id)->delete();
        $novosSetores = array_map(fn($setor) => [
            'id_funcionario' => $id,
            'id_setor' => $setor
        ], $validatedData['setor']);
    
        DB::table('funsetor')->insert($novosSetores);
    
        // Atualizar turnos - remover antigos e inserir novos
        DB::table('funturno')->where('id_funcionario', $id)->delete();
        $novosTurnos = array_map(fn($turno) => [
            'id_funcionario' => $id,
            'id_turno' => $turno
        ], $validatedData['turno']);
    
        DB::table('funturno')->insert($novosTurnos);
    
        return redirect()->route('home.lista.funcionario')->with('success', 'Funcionário atualizado com sucesso!');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        //
    }
}
