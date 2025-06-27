<?php

namespace App\Http\Controllers;

use App\Models\FunFolga;
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
     * Busca o período ativo da sessão ou o mais recente cadastrado.
     *
     * @return \App\Models\Periodo|null
     */
    private function getPeriodoAtivo()
    {
        $periodoId = session('periodo_id');

        if ($periodoId) {
            return Periodo::find($periodoId);
        }

        // Fallback: se nenhum período estiver na sessão, pega o mais recente.
        return Periodo::latest('id')->first();
    }

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
        // Busca o período ativo (da sessão ou o mais recente)
        $periodo = $this->getPeriodoAtivo();
        $escalaHeaders = [];

        Carbon::setLocale('pt_BR'); // Define a linguagem para português

        if ($periodo) {
            $dataInicio = Carbon::parse($periodo->dataIni);
            $dataFim = Carbon::parse($periodo->dataFim);

            for ($data = $dataInicio; $data->lte($dataFim); $data->addDay()) {
                $escalaHeaders[] = [
                    'day' => $data->day,
                    'dayName' => $data->translatedFormat('D'),
                ];
            }
        }

        // Caso nenhum período seja encontrado ou não existam datas
        if (empty($escalaHeaders)) {
            $escalaHeaders = null; // Nenhum período definido
        }

    
        // Buscar todos os funcionários
        $funcionarios = Funcionario::orderBy('nome')->get();
    
        // Buscar todos os setores
        $setores = Setor::orderBy('nome')->get();
    
        // Buscar todos os turnos
        $turnos = Turno::orderBy('nome')->get();
    
        // Passar os dados para a view
        return view('site.escalarfuncionario', compact('periodo', 'escalaHeaders', 'funcionarios', 'setores', 'turnos'));
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
            'periodo_id' => 'required|exists:periodos,id', // Validar o período
        ]);
    
        // Obter o ID do período do formulário
        $periodoId = $validatedData['periodo_id'];
    
        // Obter o período correspondente
        $periodo = DB::table('periodos')->where('id', $periodoId)->first();
        if (!$periodo) {
            return redirect()->back()->withErrors(['error' => 'Período não encontrado.']);
        }
    
        $dataInicio = Carbon::parse($periodo->dataIni);
    
        $erros = []; // Array para armazenar mensagens de erro
    
        // Processar os dados do formulário
        foreach ($validatedData['funcionario'] as $index => $funcionarioId) {
            $setorId = $validatedData['setor'][$index];
            $turnoId = $validatedData['turno'][$index];
    
            foreach ($validatedData['status'][$index] as $day => $status) {
                // Verifica se o status é '#' e pula a inserção
                if ($status === '#') {
                    continue; // Pula
                }
    
                // Converter o dia para uma data no formato correto
                $date = $dataInicio->copy()->addDays($day - 1)->format('Y-m-d');
    
                // Verificar se já existe uma escala para este funcionário no mesmo dia
                $existeEscala = DB::table('escalas')
                    ->where('id_funcionario', $funcionarioId)
                    ->where('dia', $date)
                    ->whereBetween('dia', [$periodo->dataIni, $periodo->dataFim])
                    ->exists();
    
                if ($existeEscala) {
                    // Adicionar mensagem de erro ao array
                    $erros[] = "Funcionário ID {$funcionarioId} já escalado para o dia {$date}.";
                    continue;
                }
    
                // Inserir ou atualizar a escala
                DB::table('escalas')->updateOrInsert(
                    [
                        'id_funcionario' => $funcionarioId,
                        'id_setor' => $setorId,
                        'id_turno' => $turnoId,
                        'dia' => $date,
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }
        }
    
        // Verificar se houve erros
        if (!empty($erros)) {
            return redirect()->back()->withErrors(['error' => $erros]);
        }
    
        return redirect()->route('escala')->with('success', 'Escala salva com sucesso!');
    }

    /**
     * Página que vai listar todos permitindo edição.
     */
    public function listaEscala(Request $request)
    {
        // Busca o período ativo (da sessão ou o mais recente)
        $periodo = $this->getPeriodoAtivo();

        // Captura os filtros do request
        $funcionarioId = $request->input('funcionario');
        $setorId = $request->input('setor');
        $turnoId = $request->input('turno');
    
        $escalaHeaders = [];
    
        Carbon::setLocale('pt_BR');

        if ($periodo) {
            // Definir datas do período
            $dataInicio = Carbon::parse($periodo->dataIni);
            $dataFim = Carbon::parse($periodo->dataFim);

            for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addDay()) {
                $escalaHeaders[] = [
                    'day' => $data->format('Y-m-d'),
                    'dayName' => $data->translatedFormat('D'),
                    'diaDoMes' => $data->day,
                ];
            }
        } else {
            // Tratar o caso de não haver nenhum período cadastrado
            return view('site.escala', [
                'periodo' => null, 'escalaHeaders' => null, 'escalas' => collect(), 'bloqueios' => [],
                'funcionarios' => Funcionario::orderBy('nome')->get(), 'setores' => Setor::orderBy('nome')->get(), 'turnos' => Turno::orderBy('nome')->get()
            ])->withErrors(['error' => 'Nenhum período de escala cadastrado. Por favor, cadastre um antes de continuar.']);
        }
    
        if (empty($escalaHeaders)) {
            $escalaHeaders = null;
        }
    
        // 🔹 Obtem todas as escalas (SEM FILTRO) para o bloqueio
        $todasEscalas = Escala::with(['setor', 'turno', 'funcionario'])
        ->whereBetween('dia', [$periodo->dataIni, $periodo->dataFim])
        ->get();
    
        // 🔹 Query com filtros aplicados para exibição
        $query = Escala::with(['setor', 'turno', 'funcionario'])
            ->whereBetween('dia', [$periodo->dataIni, $periodo->dataFim]);
    
        if (!empty($funcionarioId)) {
            $query->where('id_funcionario', $funcionarioId);
        }
    
        if (!empty($setorId)) {
            $query->where('id_setor', $setorId);
        }
    
        if (!empty($turnoId)) {
            $query->where('id_turno', $turnoId);
        }
    
        $escalas = $query->get()->groupBy(function ($item) {
            return $item->id_funcionario . '-' . $item->id_setor . '-' . $item->id_turno;
        });
    
        // 🔹 Calcula bloqueios considerando TODAS as escalas
        $bloqueios = [];
    
        foreach ($todasEscalas as $escala) { 
            $dia = $escala->dia;
            $funcionarioId = $escala->id_funcionario;
            $setorTurno = "{$escala->id_setor}-{$escala->id_turno}";
    
            if ($escala->status !== '#') {
                if (!isset($bloqueios[$funcionarioId][$dia])) {
                    $bloqueios[$funcionarioId][$dia] = [];
                }
                $bloqueios[$funcionarioId][$dia][] = $setorTurno;
            }
        }
    
        $funcionarios = Funcionario::orderBy('nome')->get();
        $setores = Setor::orderBy('nome')->get();
        $turnos = Turno::orderBy('nome')->get();
    
        return view('site.escala', compact('periodo', 'escalaHeaders', 'escalas', 'bloqueios', 'funcionarios', 'setores', 'turnos'));
    }

    /**
     * Atualiza os dados da escala da pagina regenciar escalas.
     */
    public function update(Request $request)
    {
        $idiomas = Carbon::getAvailableLocales();

        // Validar os dados recebidos
        $validatedData = $request->validate([
            'funcionario.*' => 'required|exists:funcionarios,id',
            'setor.*' => 'required|exists:setors,id',
            'turno.*' => 'required|exists:turnos,id',
            'status.*.*' => 'required|in:E,D,F,#,A',
            'periodo_id' => 'required|exists:periodos,id', // Validar o período
        ]);
    
        // Obter o ID do período do formulário
        $periodoId = $validatedData['periodo_id'];
    
        // Obter o período correspondente
        $periodo = DB::table('periodos')->where('id', $periodoId)->first();
        if (!$periodo) {
            return redirect()->back()->withErrors(['error' => 'Período não encontrado.']);
        }

        $erros = []; // Array para armazenar mensagens de erro

        // Processar os dados do formulário
        foreach ($validatedData['status'] as $key => $statusData) {
            $chavePartes = explode('-', $key);

            if (count($chavePartes) === 3) {
                list($funcionarioId, $setorId, $turnoId) = $chavePartes;
            } else {
                \Log::error("Erro ao processar chave de status: $key"); // Log para depuração
                continue; // Pula este item e evita erro
            }
        
            $excluirIds = [];
        
            foreach ($statusData as $date => $status) {
                if ($status === '#') {
                    $escala = DB::table('escalas')
                        ->where('id_funcionario', $funcionarioId)
                        ->where('dia', $date)
                        ->whereBetween('dia', [$periodo->dataIni, $periodo->dataFim])
                        ->where('id_setor', $setorId)
                        ->where('id_turno', $turnoId)
                        ->first();
        
                    if ($escala) {
                        $excluirIds[] = $escala->id;
                    }
                    continue;
                }
        
                // Busca o registro existente
                $escala = DB::table('escalas')
                    ->where('id_funcionario', $funcionarioId)
                    ->where('dia', $date)
                    ->whereBetween('dia', [$periodo->dataIni, $periodo->dataFim])
                    ->first();
                
                // Se o funcionário já estiver com status 'A' (Ausente), não atualiza
                if ($escala && $escala->status === 'A') {
                    continue;
                }
        
                if ($escala) {
                    DB::table('escalas')
                        ->where('id', $escala->id)
                        ->update([
                            'id_setor' => $setorId,
                            'id_turno' => $turnoId,
                            'status' => $status,
                        ]);
                } else {
                    DB::table('escalas')->insert([
                        'id_funcionario' => $funcionarioId,
                        'dia' => $date,
                        'id_setor' => $setorId,
                        'id_turno' => $turnoId,
                        'status' => $status,
                    ]);
                }
            }
        
            if (!empty($excluirIds)) {
                DB::table('escalas')
                    ->whereIn('id', $excluirIds)
                    ->delete();
            }
        }
    
        // Verificar se houve erros
        if (!empty($erros)) {
            return redirect()->back()->withErrors(['error' => $erros]);
        }
    
        return redirect()->back()->with('success', 'Escala atualizada com sucesso!');
    }    

    /**
     * Página para registrar ausencia do funcionário.
     */
    public function registrarAusencia()
    {
        $funcionarios = Funcionario::orderBy('nome')->get();
        $setores = Setor::orderBy('nome')->get();
        $turnos = Turno::orderBy('nome')->get();

        return view('site.registrarAusencia', compact('funcionarios', 'setores', 'turnos'));
    }

    /**
     * Salva a ausência do funcionário.
     */
    public function salvarAusencia(Request $request)
    {
        $request->validate([
            'funcionario' => 'required|exists:funcionarios,id',
            'setor' => 'required|exists:setors,id',
            'turno' => 'required|exists:turnos,id',
            'dataInicio' => 'required|date',
            'dataFim' => 'required|date|after_or_equal:dataInicio',
            'motivo' => 'required|string|max:255',
            'tipoAuse' => 'required|in:FERIAS,LISENCA,ATESTADO,OUTROS',
        ]);

        $dataInicio = Carbon::parse($request->dataInicio);
        $dataFim = Carbon::parse($request->dataFim);

        DB::beginTransaction();
        try {
            for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
                // Remover qualquer escala existente do funcionário para este dia
                Escala::where('dia', $data->format('Y-m-d'))
                    ->where('id_funcionario', $request->funcionario)
                    ->delete();

                // Criar a nova entrada da ausência
                Escala::create([
                    'dia' => $data->format('Y-m-d'),
                    'id_funcionario' => $request->funcionario,
                    'id_setor' => $request->setor,
                    'id_turno' => $request->turno,
                    'status' => 'A', // Ausente
                    'observacao' => $request->motivo,
                    'tipoAuse' => $request->tipoAuse,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Ausência registrada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['erro' => 'Erro ao registrar ausência: ' . $e->getMessage()]);
        }
    }
}
