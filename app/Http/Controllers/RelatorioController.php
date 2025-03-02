<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setor;
use App\Models\Turno;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    /**
     * Funcionarios escalados por setor e turno.
     */
    public function funcionarios_escalados_setor_turno(Request $request)
    {
        Carbon::setLocale('pt_BR'); // Define o locale para português
    
        // Recebendo os filtros do formulário
        $setorId = $request->input('setor');
        $turnoId = $request->input('turno');
        $dataInicio = $request->input('dataInicio');
        $dataFim = $request->input('dataFim');
    
        // Busca os setores e turnos para exibir nos filtros
        $setores = Setor::all();
        $turnos = Turno::all();
    
        // Consulta os funcionários escalados
        $escalas = DB::table('escalas as a')
            ->join('funcionarios as b', 'a.id_funcionario', '=', 'b.id')
            ->join('setors as c', 'a.id_setor', '=', 'c.id')
            ->join('turnos as d', 'a.id_turno', '=', 'd.id')
            ->select(
                DB::raw("DATE_FORMAT(a.dia, '%d-%m-%Y') AS data"), // Formata a data como dd-mm-yyyy
                DB::raw("DAYNAME(a.dia) AS dia_semana"),
                DB::raw("GROUP_CONCAT(b.nomecompleto ORDER BY b.nome SEPARATOR '; ') AS funcionarios")
            )
            ->whereBetween('a.dia', [$dataInicio, $dataFim]) // Filtro direto na coluna a.dia
            ->where('a.status', '=', 'E')
            ->when($setorId, function ($query) use ($setorId) {
                return $query->where('a.id_setor', $setorId);
            })
            ->when($turnoId, function ($query) use ($turnoId) {
                return $query->where('a.id_turno', $turnoId);
            })
            ->groupBy('a.dia')
            ->orderBy('a.dia', 'asc')
            ->get();
    
        // Ajusta o nome do dia da semana para PT-BR
        foreach ($escalas as $escala) {
            $escala->dia_semana = ucfirst(Carbon::parse($escala->data)->translatedFormat('l'));
            $escala->data = Carbon::parse($escala->data)->format('d/m/Y');
        }
    
        return view('site/relatorios/funcionarios_escalados_por_setor_e_turno', compact('setores', 'turnos', 'escalas'));
    }

    /**
     * Escala da semana
     */
    public function escala_da_semana(Request $request)
    {
        Carbon::setLocale('pt_BR');
    
        $setorId = $request->input('setor');
        $turnoId = $request->input('turno');
        $dataInicio = $request->input('dataInicio');
        $dataFim = $request->input('dataFim');
    
        $setores = Setor::all();
        $turnos = Turno::all();

        $escalas = [];
        $escalaHeaders = [];
        
        if (!$dataInicio || !$dataFim) {
            return view('site/relatorios/escala_da_semana', compact('setores', 'turnos', 'escalas', 'escalaHeaders'));
        }
    
        $dataInicio = Carbon::parse($dataInicio);
        $dataFim = Carbon::parse($dataFim);
    
        // Criar os cabeçalhos de dias
        $escalaHeaders = [];
        for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addDay()) {
            $escalaHeaders[] = [
                'day' => $data->format('Y-m-d'),
                'dayName' => ucfirst($data->translatedFormat('D')),
            ];
        }
    
        // Buscar as escalas
        $escalasDB = DB::table('escalas as e')
            ->join('funcionarios as f', 'e.id_funcionario', '=', 'f.id')
            ->select('e.dia', 'f.nome as funcionario')
            ->whereBetween('e.dia', [$dataInicio, $dataFim]);
    
        if ($setorId) {
            $escalasDB->where('e.id_setor', $setorId);
        }
    
        if ($turnoId) {
            $escalasDB->where('e.id_turno', $turnoId);
        }
    
        $escalasDB = $escalasDB->get();
    
        // Organizar os funcionários por dia
        $escalas = [];
        foreach ($escalasDB as $escala) {
            $escalas[$escala->dia][] = $escala->funcionario;
        }
    
        return view('site/relatorios/escala_da_semana', compact('setores', 'turnos', 'escalas', 'escalaHeaders'));
    }
}
