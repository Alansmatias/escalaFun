<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
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
        return view('site/cadastro/turno', ['turno' => null]);//no cadastro atribui null para a variavel $turno
    }

    /**
     * Lista todos os turnos
     */
    public function lista()
    {
        $turnos = Turno::all();

        return view('site/lista/turno', compact('turnos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        {
            // Validação dos dados
            $validatedData = $request->validate([
                'nome_do_turno' => 'required|string|max:255',
                'ativo' => 'required|boolean',
            ]);
    
            // Inserção dos dados na tabela
            $funcionario = Turno::create([
                'nome' => $validatedData['nome_do_turno'],
                'ativo' => $validatedData['ativo'],
            ]);
    
            return redirect()->route('home.lista.turno')->with('success', 'Turno cadastrado com sucesso!');
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Turno $turno)
    {
        //
    }

    /**
     * edição do turno
     */
    public function edit($id)
    {
        $turno = Turno::findOrFail($id);
        return view('site.cadastro.turno', compact('turno'));
    }
    
    /**
     * Atualizar turno existente
     */
    public function update(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        $turno->nome = $request->input('nome_do_turno');
        $turno->ativo = $request->has('ativo') ? 1 : 0;
        $turno->save();
    
        return redirect()->route('home.lista.turno')->with('success', 'Turno atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Turno $turno)
    {
        //
    }
}
