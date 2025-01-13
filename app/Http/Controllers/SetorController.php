<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use Illuminate\Http\Request;

class SetorController extends Controller
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
        return view('site/cadastro/setor', ['setor' => null]);
    }

    public function lista() //listar setor e mostrar na lista
    {
        $setores = Setor::all();

        return view('site/lista/setor', compact('setores'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validatedData = $request->validate([
            'nome_do_setor' => 'required|string|max:255',
            'ativo' => 'required|boolean',
        ]);

        // Inserção dos dados na tabela
        $funcionario = Setor::create([
            'nome' => $validatedData['nome_do_setor'],
            'ativo' => $validatedData['ativo'],
        ]);

        return redirect()->route('home.lista.setor')->with('success', 'Setor cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Setor $setor)
    {
        //
    }

    /**
     * Edição do setor
     */
    public function edit($id)
    {
        $setor = Setor::findOrFail($id);
        return view('site.cadastro.setor', compact('setor'));
    }

    /**
     * Atualizar Setor Existente
     */
    public function update(Request $request, $id)
    {
        $setor = Setor::findOrFail($id);
        $setor->nome = $request->input('nome_do_setor');
        $setor->ativo = $request->has('ativo') ? 1 : 0;
        $setor->save();
    
        return redirect()->route('home.lista.setor')->with('success', 'Setor atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setor $setor)
    {
        //
    }
}
