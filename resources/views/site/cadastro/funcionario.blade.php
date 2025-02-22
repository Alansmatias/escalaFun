<!-- views\site\cadastro\funcionario.blade.php -->
@extends('site.layout')

@section('conteudo')

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

<form action="{{ isset($funcionario) ? route('funcionario.update', $funcionario->id) : route('funcionario.store') }}" method="POST">
  @csrf
  @if(isset($funcionario))
    @method('PUT')
  @endif

  <div class="row g-3">
    <div class="col-md-4 mb-3">
      <label for="nomeFun" class="form-label">Nome</label>
      <input type="text" class="form-control" name="nome" id="nomeFun" placeholder="Nome do Funcionário" required value="{{ old('nome', $funcionario->nome ?? '') }}">
      @error('nome')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4 mb-3">
      <label for="telFun" class="form-label">Telefone</label>
      <input type="tel" class="form-control" name="telefone" id="telFun" placeholder="Digite o número de Telefone" pattern="\d{11}" required value="{{ old('telefone', $funcionario->telefone ?? '') }}">
      @error('telefone')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-3 mb-3">
      <label for="contrato" class="form-label">Contrato</label>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="contrato" id="flexRadioDefault1" value="mensalista" {{ old('contrato', $funcionario->contrato ?? '') == 'mensalista' ? 'checked' : '' }}>
        <label class="form-check-label" for="flexRadioDefault1">Mensalista</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="contrato" id="flexRadioDefault2" value="intermitente" {{ old('contrato', $funcionario->contrato ?? '') == 'intermitente' ? 'checked' : '' }}>
        <label class="form-check-label" for="flexRadioDefault2">Intermitente</label>
      </div>
      @error('contrato')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div>
    <label for="folga" class="form-label">Folga(s)</label>
    <div class="mb-3">
      @php $folgasSelecionadas = old('folga', $folgasSelecionadas ?? []); @endphp
      @foreach(['dom' => 'Domingo', 'seg' => 'Segunda-feira', 'ter' => 'Terça-feira', 'qua' => 'Quarta-feira', 'qui' => 'Quinta-feira', 'sex' => 'Sexta-feira', 'sab' => 'Sábado'] as $key => $dia)
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="folga[]" id="{{ $key }}" value="{{ $key }}" {{ in_array($key, $folgasSelecionadas) ? 'checked' : '' }}>
          <label class="form-check-label" for="{{ $key }}">{{ $dia }}</label>
        </div>
      @endforeach
      @error('folga')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-3 mb-3">
      <label for="domingo" class="form-label">Domingo</label>
      <select class="form-select" name="domingo" id="domingo">
        <option selected disabled value="">Escolha...</option>
        @foreach([1,2,3,4] as $value)
          <option value="{{ $value }}" {{ old('domingo', $funcionario->domingo ?? '') == $value ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
      </select>
      @error('domingo')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <!-- Puxar do banco os setores -->
  <div>
    <label for="setor" class="form-label">Setor</label>
    <div class="mb-3">
      @php $setoresSelecionados = old('setor', isset($funcionario) ? $funcionario->setores->pluck('id')->toArray() : []); @endphp
      @foreach($setores as $setor)
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="setor[]" id="setor{{ $setor->id }}" value="{{ $setor->id }}" {{ in_array($setor->id, $setoresSelecionados) ? 'checked' : '' }}>
          <label class="form-check-label" for="setor{{ $setor->id }}">{{ $setor->nome }}</label>
        </div>
      @endforeach
      @error('setor')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <!-- Puxar do banco os turnos -->
  <div>
    <label for="turno" class="form-label">Turno</label>
    <div class="mb-3">
      @php $turnosSelecionados = old('turno', isset($funcionario) ? $funcionario->turnos->pluck('id')->toArray() : []); @endphp
      @foreach($turnos as $turno)
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="turno[]" id="turno{{ $turno->id }}" value="{{ $turno->id }}" {{ in_array($turno->id, $turnosSelecionados) ? 'checked' : '' }}>
          <label class="form-check-label" for="turno{{ $turno->id }}">{{ $turno->nome }}</label>
        </div>
      @endforeach
      @error('turno')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo', $funcionario->ativo ?? 1) ? 'checked' : '' }}>
    <label class="form-check-label" for="ativo">Ativo</label>
    @error('ativo')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-12 mb-3">
    <button class="btn btn-primary" type="submit">Salvar</button>
  </div>

</form>
@endsection
