@extends('site.layout')

@section('conteudo')
<h1>Funcionários Escalados No Período Por Setor e Turno<br><br></h1>

<!-- Filtro e Gerar Relatório -->
<form class="row g-3" method="GET" action="{{ route('funcionarios_escalados_setor_turno') }}">
    @csrf
    <div class="col-md-3 mb-3">
        <label for="setor" class="form-label">Setor</label>
        <select class="form-select" name="setor" id="setor">
            <option value="">Todos</option>
            @foreach($setores as $setor)
                <option value="{{ $setor->id }}" {{ request('setor') == $setor->id ? 'selected' : '' }}>
                    {{ $setor->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label for="turno" class="form-label">Turno</label>
        <select class="form-select" name="turno" id="turno">
            <option value="">Todos</option>
            @foreach($turnos as $turno)
                <option value="{{ $turno->id }}" {{ request('turno') == $turno->id ? 'selected' : '' }}>
                    {{ $turno->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label for="dataInicio" class="form-label">Data Inicial</label>
        <input type="date" class="form-control" name="dataInicio" id="dataInicio" value="{{ request('dataInicio') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="dataFim" class="form-label">Data Final</label>
        <input type="date" class="form-control" name="dataFim" id="dataFim" value="{{ request('dataFim') }}">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Gerar Relatório</button>
    </div>
</form>

<br>

<!-- Tabela do Relatório -->
@if(request()->has('setor') || request()->has('turno') || request()->has('dataInicio') || request()->has('dataFim'))
    @if(isset($escalas) && count($escalas) > 0)
        <div class="overflow-auto">
            <div class="d-flex">
                @foreach($escalas as $escala)
                    <div class="card me-3" style="min-width: 250px; max-width: 300px;">
                        <div class="card-header text-center">
                            <strong>{{ $escala->data }}</strong><br>
                            <span>{{ ucfirst($escala->dia_semana) }}</span>
                        </div>
                        <div class="card-body">
                            @foreach(explode(';', $escala->funcionarios) as $funcionario)
                                <p>{{ $funcionario }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="alert alert-warning">Nenhum funcionário escalado encontrado para o período selecionado.</p>
    @endif
@else
    <p class="alert alert-info">Utilize os filtros acima para gerar o relatório. Selecione pelo menos um filtro para visualizar os dados.</p>
@endif

@endsection
