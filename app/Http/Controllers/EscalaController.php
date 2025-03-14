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
        $periodoId = 2; // Altere este valor conforme necessário

        // Buscar o período específico pelo ID
        $periodo = Periodo::find($periodoId);

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
        $periodoId = 2; // Ajuste conforme necessário
    
        // Obter o período correspondente para cálculo das datas
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
        $periodoId = 2; // Ajuste conforme necessário
        $periodo = Periodo::find($periodoId);
    
        // Captura os filtros do request
        $funcionarioId = $request->input('funcionario');
        $setorId = $request->input('setor');
        $turnoId = $request->input('turno');
    
        $escalaHeaders = [];
    
        Carbon::setLocale('pt_BR');

        // Definir datas do período
        $dataInicio = Carbon::parse($periodo->dataIni);
        $dataFim = Carbon::parse($periodo->dataFim);        
    
        if ($periodo) {
            for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addDay()) {
                $escalaHeaders[] = [
                    'day' => $data->format('Y-m-d'),
                    'dayName' => $data->translatedFormat('D'),
                    'diaDoMes' => $data->day,
                ];
            }
        }
    
        if (empty($escalaHeaders)) {
            $escalaHeaders = null;
        }
    
        // 🔹 Obtem todas as escalas (SEM FILTRO) para o bloqueio
        $todasEscalas = Escala::with(['setor', 'turno', 'funcionario'])
        ->whereBetween('dia', [$dataInicio, $dataFim])
        ->get();
    
        // 🔹 Query com filtros aplicados para exibição
        $query = Escala::with(['setor', 'turno', 'funcionario'])
            ->whereBetween('dia', [$dataInicio, $dataFim]);
    
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
    
        $funcionarios = Funcionario::all();
        $setores = Setor::all();
        $turnos = Turno::all();
    
        return view('site.escala', compact('escalaHeaders', 'escalas', 'bloqueios', 'funcionarios', 'setores', 'turnos'));
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
        ]);
    
        // Obter o ID do período (fixo ou enviado pelo formulário)
        $periodoId = 2; // Ajuste conforme necessário
    
        // Obter o período correspondente para cálculo das datas
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
     * Página de escala automática.
     */
    public function escalaautomatica()
    {
        $funcionarios = Funcionario::orderBy('nome')->get();

        return view('site.escalaautomatica', compact('funcionarios'));
    }

    /**
     *  Gera a escala automática.
     */
    public function gerarEscalaAutomatica($funcionarioId = null)
    {
        $periodoId = 2; // Ajuste conforme necessário
        $periodo = Periodo::find($periodoId);
        if (!$periodo) {
            return response()->json(['erro' => 'Nenhum período cadastrado'], 400);
        }

        $dataInicio = Carbon::parse($periodo->dataIni);
        $dataFim = Carbon::parse($periodo->dataFim);

        // Obtém os setores e turnos do funcionário
        $setores = DB::table('funSetor')->where('id_funcionario', $funcionarioId)->pluck('id_setor')->toArray();
        $turnos = DB::table('funTurno')->where('id_funcionario', $funcionarioId)->pluck('id_turno')->toArray();

        // Obtém os dias de folga cadastrados na tabela funFolga
        $diasFolga = DB::table('funFolga')->where('id_funcionario', $funcionarioId)->pluck('folga')->toArray();
        
        // Array para converter os dias da semana para os nomes armazenados na tabela funFolga
        $diasSemana = [
            'Sunday'    => 'dom',
            'Monday'    => 'seg',
            'Tuesday'   => 'ter',
            'Wednesday' => 'qua',
            'Thursday'  => 'qui',
            'Friday'    => 'sex',
            'Saturday'  => 'sab',
        ];

        // Percorre os dias do período
        for ($data = clone $dataInicio; $data->lte($dataFim); $data->addDay()) {
            $diaSemana = $data->format('l'); // Nome do dia em inglês (Sunday, Monday, ...)
            $diaEscala = $diasSemana[$diaSemana] ?? null;

            if (in_array($diaEscala, $diasFolga)) {
                // Se for um dia de folga, registra como descanso
                Escala::updateOrCreate(
                    [
                        'dia'            => $data->format('Y-m-d'),
                        'id_funcionario' => $funcionarioId,
                    ],
                    [
                        'id_setor' => $setor,
                        'id_turno' => $turno,
                        'status'   => 'D', // Descanso
                    ]
                );
            } else {
                // Define setor e turno aleatórios para o funcionário
                $setor = $setores[array_rand($setores)];
                $turno = $turnos[array_rand($turnos)];

                // Salva a escala no banco de dados
                Escala::updateOrCreate(
                    [
                        'dia'            => $data->format('Y-m-d'),
                        'id_funcionario' => $funcionarioId,
                    ],
                    [
                        'id_setor' => $setor,
                        'id_turno' => $turno,
                        'status'   => 'E', // Escalado
                    ]
                );
            }
        }
    }
    
    /**
     *  Gera a escala automática.
     */
    public function gerarEscala(Request $request)
    {
        // Obtém os funcionários selecionados no formulário
        $funcionariosSelecionados = $request->input('escalados', []);

        if (empty($funcionariosSelecionados)) {
            return back()->withErrors(['msg' => 'Selecione pelo menos um funcionário para gerar a escala.']);
        }

        // Obtém o período atual (último cadastrado)
        $periodo = Periodo::latest('id')->first();
        
        if (!$periodo) {
            return back()->withErrors(['msg' => 'Nenhum período encontrado.']);
        }

        $dataInicio = Carbon::parse($periodo->dataIni);
        $dataFim = Carbon::parse($periodo->dataFim);

        // Gera a escala para cada funcionário selecionado
        foreach ($funcionariosSelecionados as $funcionarioId) {
            $this->gerarEscalaAutomatica($funcionarioId, $dataInicio, $dataFim);
        }

        return back()->with('success', 'Escala gerada com sucesso!');
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
