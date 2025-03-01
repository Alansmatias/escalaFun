@extends('site.layout')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@section('conteudo')
    <h1>Registrar Ausência</h1><br>
    <form class="row g-3" method="POST" action="{{ route('ausencia.salvar') }}">
        @csrf
        <div class="col-md-4 mb-3">
            <label for="funcionario" class="form-label">Funcionario</label>
            <select class="form-select" name="funcionario" id="funcionario">
                <option selected disabled value="">Selecione o Funcionário</option>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}">
                        {{ $funcionario->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="setor" class="form-label">Setor</label>
            <select class="form-select" name="setor" id="setor">
                <option selected disabled value="">Selecione o Setor</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id }}">
                        {{ $setor->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="turno" class="form-label">Turno</label>
            <select class="form-select" name="turno" id="turno">
                <option selected disabled value="">Selecione o Turno</option>
                @foreach($turnos as $turno)
                    <option value="{{ $turno->id }}">
                        {{ $turno->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="dataInicio" class="form-label">Data Inicial</label>
            <input type="date" class="form-control" name="dataInicio" id="dataInicio" value="#">
        </div>

        <div class="col-md-3 mb-3">
            <label for="dataFim" class="form-label">Data Final</label>
            <input type="date" class="form-control" name="dataFim" id="dataFim" value="#">
        </div>

        <div class="col-md-12 mb-3">
            <label for="motivo" class="form-label">Motivo</label>
            <textarea class="form-control" name="motivo" id="motivo" rows="3"></textarea>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Registrar</button>
        </div>
    </form>
@endsection