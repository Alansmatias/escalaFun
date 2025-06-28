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
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Cadastrar Novo Período</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('periodo.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="dataIni" class="form-label">Data de Início</label>
                            <input type="date" class="form-control" id="dataIni" name="dataIni" value="{{ old('dataIni') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="dataFim" class="form-label">Data de Fim</label>
                            <input type="date" class="form-control" id="dataFim" name="dataFim" value="{{ old('dataFim') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar Período</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection