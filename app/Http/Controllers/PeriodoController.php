<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeriodoController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('site.periodo.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dataIni' => 'required|date',
            'dataFim' => 'required|date|after_or_equal:dataIni',
        ]);

        // Check for overlapping periods
        $dataIni = Carbon::parse($validatedData['dataIni']);
        $dataFim = Carbon::parse($validatedData['dataFim']);

        $overlappingPeriod = Periodo::where(function ($query) use ($dataIni, $dataFim) {
            $query->whereBetween('dataIni', [$dataIni, $dataFim])
                  ->orWhereBetween('dataFim', [$dataIni, $dataFim]);
        })->orWhere(function ($query) use ($dataIni, $dataFim) {
            $query->where('dataIni', '<=', $dataIni)
                  ->where('dataFim', '>=', $dataFim);
        })->exists();

        if ($overlappingPeriod) {
            return redirect()->back()->withInput()->withErrors(['error' => 'O período informado se sobrepõe a um período já existente.']);
        }

        $periodo = Periodo::create($validatedData);

        // Set the new period as the active one in the session
        session(['periodo_id' => $periodo->id]);

        return redirect()->route('escala')->with('success', 'Novo período criado e selecionado com sucesso!');
    }

    /**
     * Handle the selection of a period and store it in the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selecionarPeriodo(Request $request)
    {
        $request->validate([
            'periodo_id' => 'required|exists:periodos,id',
        ]);

        session(['periodo_id' => $request->periodo_id]);

        return redirect()->back();
    }
}