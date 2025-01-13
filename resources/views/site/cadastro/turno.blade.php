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
<form class="" action="{{ $turno ? route('turno.update', $turno->id) : route('turno.store') }}" method="POST">
  @csrf
  @if($turno)
    @method('PUT')
  @endif
    <div class="row g-3">
        <div class="col-md-4 mb-3">
            <label for="nomeTurno" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome_do_turno" id="nomeTurno" placeholder="Nome do Turno" required value="{{ old('nome', $turno ? $turno->nome : '' ) }}">
            @error('nome')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', isset($turno) ? ($turno->ativo == 1 ? 'checked' : '') : 'checked') }}>
                <label class="form-check-label" for="ativo">Ativo</label>
                @error('ativo')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <button class="btn btn-primary" type="submit">{{$turno ? 'Atualizar' : 'Salvar'}}</button>
    </div>

</form>
@endsection 