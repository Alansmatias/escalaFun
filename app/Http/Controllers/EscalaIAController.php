<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcionario;
use App\Models\Periodo;

class EscalaIAController extends Controller
{
    /**
     * Exibe o formulário para gerar a escala com IA.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Busca o período ativo da sessão ou o mais recente como sugestão
        $periodoId = session('periodo_id');
        $periodo_sugerido = $periodoId ? Periodo::find($periodoId) : Periodo::latest('id')->first();

        $funcionarios = Funcionario::where('ativo', 1)->orderBy('nome')->get();

        return view('site.escala_ia', compact('funcionarios', 'periodo_sugerido'));
    }

    /**
     * Processa o formulário e (futuramente) envia os dados para a API do Gemini.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function gerar(Request $request)
    {
        $validated = $request->validate([
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'funcionarios' => 'nullable|array',
            'funcionarios.*' => 'exists:funcionarios,id',
            'regras' => 'nullable|array',
            'regras.*' => 'string',
            'instrucoes_adicionais' => 'nullable|string|max:5000',
        ]);

        // --- LÓGICA FUTURA PARA A API DO GEMINI ---
        // 1. Coletar todos os dados relevantes do banco:
        //    - Perfis dos funcionários selecionados (contrato, folgas, setores, turnos preferenciais).
        //    - Detalhes dos setores e turnos.
        // 2. Montar um prompt detalhado para a API, incluindo:
        //    - O "banco de dados" em formato de texto/JSON.
        //    - As regras selecionadas no formulário.
        //    - As instruções em linguagem natural.
        // 3. Enviar a requisição para a API do Gemini.
        // 4. Receber a resposta (provavelmente um JSON com a escala).
        // 5. Processar a resposta e salvar os dados na tabela 'escalas'.

        return back()->with('success', 'Instruções para a IA recebidas! A integração com a API será o próximo passo.');
    }
}