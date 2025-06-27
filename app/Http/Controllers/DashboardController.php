<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\Escala;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Exibe o dashboard com dados dinâmicos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // --- DADOS PARA OS CARDS ---

        // Total de funcionários com status 'ATIVO'
        $funcionariosAtivos = Funcionario::where('ativo', true)->count();

        // Contagem por tipo de contrato para funcionários ativos
        $contagemContrato = Funcionario::where('ativo', true)
            ->select('contrato', DB::raw('count(*) as total'))
            ->groupBy('contrato')
            ->pluck('total', 'contrato');

        // Os valores no banco são 'mensalista' e 'intermitente' em minúsculas
        $mensalistas = $contagemContrato->get('mensalista', 0);
        $intermitentes = $contagemContrato->get('intermitente', 0);

        $hoje = Carbon::today();

        // Contagem de funcionários em Férias ou Licença hoje.
        // A tabela 'escalas' possui a coluna 'tipoAuse' para identificar o motivo da ausência.
        $feriasLicenca = Escala::where('status', 'A')
            ->whereDate('dia', $hoje) // A coluna de data é 'dia'
            ->whereIn('tipoAuse', ['FERIAS', 'LISENCA']) // Usando a coluna 'tipoAuse'
            ->distinct('id_funcionario') // A coluna de funcionário é 'id_funcionario'
            ->count();

        // Contagem de funcionários com atestado para hoje.
        $atestadosHoje = Escala::where('status', 'A')
            ->whereDate('dia', $hoje) // A coluna de data é 'dia'
            ->where('tipoAuse', 'ATESTADO') // Usando a coluna 'tipoAuse'
            ->distinct('id_funcionario') // A coluna de funcionário é 'id_funcionario'
            ->count();


        // --- DADOS PARA OS GRÁFICOS ---

        // Gráfico de Tipo de Contrato (reutilizando a contagem dos cards)
        // Os labels precisam ser capitalizados para exibição
        $contratosLabels = $contagemContrato->keys()->map(function ($item) {
            return ucfirst(strtolower($item));
        });
        $contratosData = $contagemContrato->values();


        return view('site.home', compact(
            'funcionariosAtivos',
            'mensalistas',
            'intermitentes',
            'feriasLicenca',
            'atestadosHoje',
            'contratosLabels',
            'contratosData'
        ));
    }
}
