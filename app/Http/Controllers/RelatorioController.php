<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setor;
use App\Models\Turno;
use App\Models\Funcionario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function funcionarios_escalados_setor_turno(Request $request)
    {
        // Recebendo os filtros do formulário
        $setorId = $request->input('setor');
        $turnoId = $request->input('turno');
        $dataInicio = $request->input('dataInicio');
        $dataFim = $request->input('dataFim');

        // Converte as datas para formato correto
        $dataInicio = Carbon::parse($dataInicio)->format('Y-m-d');
        $dataFim = Carbon::parse($dataFim)->format('Y-m-d');

        // Busca os setores e turnos para exibir nos filtros
        $setores = Setor::all();
        $turnos = Turno::all();

        // Consulta os funcionários escalados
        $escalas = DB::table('escalas as a')
            ->join('funcionarios as b', 'a.id_funcionario', '=', 'b.id')
            ->join('setors as c', 'a.id_setor', '=', 'c.id')
            ->join('turnos as d', 'a.id_turno', '=', 'd.id')
            ->join('periodos as e', 'a.id_periodo', '=', 'e.id')
            ->select(
                DB::raw("DATE_FORMAT(a.dia, '%d/%m/%Y') AS data"),
                DB::raw("DAYNAME(a.dia) AS dia_semana"),
                DB::raw("GROUP_CONCAT(b.nome ORDER BY b.nome SEPARATOR '; ') AS funcionarios")
            )
            ->where('e.dataIni', '=', $dataInicio)
            ->where('e.dataFim', '=', $dataFim)
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
            $escala->dia_semana = ucfirst(Carbon::parse($escala->data)->translatedFormat('l')); // Traduz dia da semana
        }

        return view('site/relatorios/funcionarios_escalados_por_setor_e_turno', compact('setores', 'turnos', 'escalas'));
    }
}
