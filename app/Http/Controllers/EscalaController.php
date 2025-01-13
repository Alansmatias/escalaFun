<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Setor;
use App\Models\Turno;
use App\Models\Escala;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EscalaController extends Controller
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

        return view('site.escala'); // Retorne a view
    }

    /**
     * Página de escalar um novo funcionário !!!!!!!!!!!!!
     */
    public function escalar()
    {
        // Definir o ID do período manualmente
        $periodoId = 1; // Altere este valor conforme necessário

        // Buscar o período específico pelo ID
        $periodo = Periodo::find($periodoId);

        $escalaHeaders = [];

        if ($periodo) {
            $dataInicio = Carbon::parse($periodo->dataIni);
            $dataFim = Carbon::parse($periodo->dataFim);

            for ($data = $dataInicio; $data->lte($dataFim); $data->addDay()) {
                $escalaHeaders[] = [
                    'day' => $data->day,
                    'dayName' => $data->format('D'),
                ];
            }
        }

        // Caso nenhum período seja encontrado ou não existam datas
        if (empty($escalaHeaders)) {
            $escalaHeaders = null; // Nenhum período definido
        }

    
        // Buscar todos os funcionários
        $funcionarios = Funcionario::all();
    
        // Buscar todos os setores
        $setores = Setor::all();
    
        // Buscar todos os turnos
        $turnos = Turno::all();
    
        // Passar os dados para a view
        return view('site.escalarfuncionario', compact('escalaHeaders', 'funcionarios', 'setores', 'turnos'));
    }

    /**
     * Incluir novo funcionário na tabela.
     */
    public function store(Request $request)
    {
        // Validar os dados recebidos
        $validatedData = $request->validate([
            'funcionario.*' => 'required|exists:funcionarios,id',
            'setor.*' => 'required|exists:setors,id',
            'turno.*' => 'required|exists:turnos,id',
            'status.*.*' => 'required|in:E,D,F,#',
        ]);
    
        // Obter o ID do período (fixo ou enviado pelo formulário)
        $periodoId = 1; // Ajuste conforme necessário
    
        // Obter o período correspondente para cálculo das datas
        $periodo = DB::table('periodos')->where('id', $periodoId)->first();
        if (!$periodo) {
            return redirect()->back()->withErrors(['error' => 'Período não encontrado.']);
        }
    
        $dataInicio = Carbon::parse($periodo->dataIni);
    
        // Processar os dados do formulário
        foreach ($validatedData['funcionario'] as $index => $funcionarioId) {
            $setorId = $validatedData['setor'][$index];
            $turnoId = $validatedData['turno'][$index];
    
            foreach ($validatedData['status'][$index] as $day => $status) {
                // Verifica se o status é '#' e pula a inserção
                if ($status === '#'){
                    continue; //pula
                }

                // Converter o dia para uma data no formato correto
                $date = $dataInicio->copy()->addDays($day - 1)->format('Y-m-d');
    
                DB::table('escalas')->updateOrInsert(
                    [
                        'id_funcionario' => $funcionarioId,
                        'id_setor' => $setorId,
                        'id_turno' => $turnoId,
                        'id_periodo' => $periodoId,
                        'dia' => $date,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }
    
        return redirect()->route('escalarfun')->with('success', 'Escala salva com sucesso!');
    }

    /**
     * Pagina que vai listas todos permitindo edição!!!!!!!!!!!!!
     */
    public function listaEscala()
    {
        // Definir o ID do período manualmente
        $periodoId = 1; // Altere este valor conforme necessário
    
        // Buscar o período específico pelo ID
        $periodo = Periodo::find($periodoId);
    
        $escalaHeaders = [];
    
        if ($periodo) {
            $dataInicio = Carbon::parse($periodo->dataIni);
            $dataFim = Carbon::parse($periodo->dataFim);
    
            for ($data = $dataInicio; $data->lte($dataFim); $data->addDay()) {
                $escalaHeaders[] = [
                    'day' => $data->format('Y-m-d'), // Data completa como chave
                    'dayName' => $data->format('D'), // dia da semana ex: seg, ter...
                    'diaDoMes' => $data->day, // Dia do mes
                ];
            }
        }
    
        // Caso nenhum período seja encontrado ou não existam datas
        if (empty($escalaHeaders)) {
            $escalaHeaders = null; // Nenhum período definido
        }
    
        // Carregar escalas e incluir relações com setor, turno e funcionário
        $escalas = Escala::with(['setor', 'turno', 'funcionario'])
            ->where('id_periodo', $periodoId)
            ->get();
    
        // Passar os dados para a view
        return view('site.escala', compact('escalaHeaders', 'escalas'));
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Escala $escala)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Escala $escala)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Escala $escala)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Escala $escala)
    {
        //
    }
}
