@extends('site.layout')

<!-- Exibir Alertas de Validação -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('conteudo')
<form class="" action="{{ $setor ? route('setor.update', $setor->id) : route('setor.store') }}" method="POST">
    @csrf
    @if($setor)
        @method('PUT')
    @endif
    <div class="row g-3">
        <div class="col-md-4 mb-3">
            <label for="nomeSetor" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome_do_setor" id="nomeSetor" placeholder="Nome do Setor" required value="{{ old('nome', $setor ? $setor->nome : '') }}">
            @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo') ? 'checked' : '' }} checked>
                <label class="form-check-label" for="ativo">Ativo</label>
                @error('ativo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <button class="btn btn-primary" type="submit">Salvar</button>
    </div>

</form>
@endsection 