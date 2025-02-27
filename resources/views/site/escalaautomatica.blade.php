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
<h1>Escala Automática<br><br></h1>

<form action="{{ route('escala.gerar') }}" method="POST">
    @csrf  {{-- Proteção contra ataques CSRF --}}
    
    <label for="filtroFuncionario" class="form-label">Selecione os Funcionários</label>
    <div style="max-height: 200px; overflow-y: auto;" class="border p-0">
        <table class="table">
            <tbody>
                @foreach($funcionarios as $funcionario)
                <tr>
                    <td>
                        <input type="checkbox" name="escalados[]" id="escalado{{ $funcionario->id }}" value="{{ $funcionario->id }}">
                        {{ $funcionario->nome }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="mt-3 btn btn-primary">Gerar Escala</button>
</form>
@endsection
