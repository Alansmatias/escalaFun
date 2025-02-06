<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function funcionarios_escalados_setor_turno()
    {
        return view('site/relatorios/funcionarios_escalados_por_setor_e_turno');
    }
}
