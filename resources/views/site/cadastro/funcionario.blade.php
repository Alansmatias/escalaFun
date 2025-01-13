<!-- views\site\cadastro\funcionario.blade.php -->
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
<form class="" action="{{ route('funcionario.store') }}" method="POST">
  @csrf
  <div class="row g-3">
    <div class="col-md-4 mb-3">
      <label for="nomeFun" class="form-label">Nome</label>
      <input type="text" class="form-control" name="nome" id="nomeFun" placeholder="Nome do Funcionário" required value="{{ old('nome') }}">
      @error('nome')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4 mb-3">
      <label for="telFun" class="form-label">Telefone</label>
      <input type="tel" class="form-control" name="telefone" id="telFun" placeholder="Digite o número de Telefone" pattern="\d{11}" required value="{{ old('telefone') }}">
      @error('telefone')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-3 mb-3">
      <label for="contrato" class="form-label">Contrato</label>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="contrato" id="flexRadioDefault1" value="mensalista" {{ old('contrato') == 'mensalista' ? 'checked' : '' }}>
        <label class="form-check-label" for="flexRadioDefault1">Mensalista</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="contrato" id="flexRadioDefault2" value="intermitente" {{ old('contrato') == 'intermitente' ? 'checked' : '' }} checked>
        <label class="form-check-label" for="flexRadioDefault2">Intermitente</label>
      @error('contrato')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      </div>
    </div>
  </div>

  <div class>
  <label for="folga" class="form-label">Dia de Folga</label>
  <div class="mb-3">
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="dom" value="dom" {{ is_array(old('folga')) && in_array('dom', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="dom">Domingo</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="seg" value="seg" {{ is_array(old('folga')) && in_array('seg', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="seg">Segunda-feira</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="ter" value="ter" {{ is_array(old('folga')) && in_array('ter', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="ter">Terça-feira</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="qua" value="qua" {{ is_array(old('folga')) && in_array('qua', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="qua">Quarta-feira</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="qui" value="qui" {{ is_array(old('folga')) && in_array('qui', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="qui">Quinta-feira</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="sex" value="sex" {{ is_array(old('folga')) && in_array('sex', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="sex">Sexta-feira</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="folga[]" id="sab" value="sab" {{ is_array(old('folga')) && in_array('sab', old('folga')) ? 'checked' : '' }}>
      <label class="form-check-label" for="sab">Sábado</label>
    </div>
    @error('folga')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>


  <div class="row g-3">
    <div class="col-md-3 mb-3">
      <label for="domingo" class="form-label">Domingo</label>
      <select class="form-select" name="domingo" id="domingo">
        <option selected disabled value="">Escolha...</option>
        <option value="1" {{ old('domingo') == '1' ? 'selected' : '' }}>1</option>
        <option value="2" {{ old('domingo') == '2' ? 'selected' : '' }}>2</option>
        <option value="3" {{ old('domingo') == '3' ? 'selected' : '' }}>3</option>
        <option value="4" {{ old('domingo') == '4' ? 'selected' : '' }}>4</option>
      </select>
      @error('domingo')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <!-- Puchar do banco os setores -->
  <div class>
    <label for="folga" class="form-label">Setor</label>
    <div class="mb-3">
      @foreach($setores as $setor)
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="setor[]" id="setor{{ $setor->id }}" value="{{ $setor->id }}"
          @if(is_array(old('setor')) && in_array($setor->id, old('setor')))
            checked
          @endif>
          <label class="form-check-label" for="setor{{ $setor->id }}">{{ $setor->nome }}</label>
        </div>
      @endforeach

      <!-- validatedData do controller -->
      @error('setor')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <!-- Puchar do banco os turno -->
  <div class>
    <label for="turno" class="form-label">Turno</label>
    <div class="mb-3">
      @foreach($turnos as $turno)
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="turno[]" id="turno{{ $turno->id }}" value="{{ $turno->id }}"
          @if(is_array(old('turno')) && in_array($setor->id, old('turno')))
            checked
          @endif>
          <label class="form-check-label" for="turno{{ $turno->id }}">{{ $turno->nome }}</label>
        </div>
      @endforeach

      <!-- validatedData do controller -->
      @error('turno')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" {{ old('ativo') ? 'checked' : '' }} checked>
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