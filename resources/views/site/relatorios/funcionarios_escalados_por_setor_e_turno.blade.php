@extends('site.layout')

@section('conteudo')
<h1>Funcionários Escalados No Período Por Setor e Turno<br><br></h1>

<!-- Filtro e Gerar Relatório -->
<form class="row g-3" method="GET" action="#">
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
@if(isset($escalas) && count($escalas) > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Data</th>
                <th>Dia da Semana</th>
                <th>Funcionários Escalados</th>
            </tr>
        </thead>
        <tbody>
            @foreach($escalas as $escala)
                <tr>
                    <td>{{ $escala->data }}</td>
                    <td>{{ ucfirst($escala->dia_semana) }}</td>
                    <td>{{ $escala->funcionarios }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="alert alert-warning">Nenhum funcionário escalado encontrado para o período selecionado.</p>
@endif

@endsection