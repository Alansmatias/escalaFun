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
<h1>Gerar Escala com Inteligência Artificial</h1>
<p class="text-muted">Forneça as diretrizes para que a IA possa criar a melhor escala possível.</p>
<hr>

<form action="{{ route('escala.ia.gerar') }}" method="POST">
    @csrf

    {{-- SEÇÃO 1: PERÍODO E FUNCIONÁRIOS --}}
    <h5 class="mt-4">1. Período e Funcionários</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label for="data_inicio" class="form-label">Data de Início</label>
            <input type="date" class="form-control" name="data_inicio" id="data_inicio" value="{{ $periodo_sugerido->dataIni ?? '' }}" required>
        </div>
        <div class="col-md-6">
            <label for="data_fim" class="form-label">Data de Fim</label>
            <input type="date" class="form-control" name="data_fim" id="data_fim" value="{{ $periodo_sugerido->dataFim ?? '' }}" required>
        </div>
        <div class="col-12">
            <label for="filtroFuncionario" class="form-label">Selecione os Funcionários (deixe em branco para todos)</label>
            <div style="max-height: 200px; overflow-y: auto;" class="border p-2 rounded">
                <table class="table table-sm">
                    <tbody>
                        @foreach($funcionarios as $funcionario)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="funcionarios[]" id="funcionario_{{ $funcionario->id }}" value="{{ $funcionario->id }}" checked>
                                    <label class="form-check-label" for="funcionario_{{ $funcionario->id }}">
                                        {{ $funcionario->nome }}
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SEÇÃO 2: REGRAS E PRIORIDADES --}}
    <h5 class="mt-4">2. Regras e Prioridades</h5>
    <p class="text-muted">Marque as regras que a IA deve seguir obrigatoriamente.</p>
    <div class="row">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="regras[]" value="Respeitar dias de folga cadastrados" id="regra_folga" checked>
                <label class="form-check-label" for="regra_folga">Respeitar dias de folga cadastrados</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="regras[]" value="Respeitar o número de domingos de trabalho cadastrado" id="regra_domingo" checked>
                <label class="form-check-label" for="regra_domingo">Respeitar o número de domingos de trabalho cadastrado</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="regras[]" value="Distribuir turnos de fim de semana de forma justa" id="regra_fds" checked>
                <label class="form-check-label" for="regra_fds">Distribuir turnos de fim de semana de forma justa</label>
            </div>
             <div class="form-check">
                <input class="form-check-input" type="checkbox" name="regras[]" value="Evitar turnos dobrados no mesmo dia para o mesmo funcionário" id="regra_dobra" checked>
                <label class="form-check-label" for="regra_dobra">Evitar turnos dobrados no mesmo dia</label>
            </div>
        </div>
    </div>

    {{-- SEÇÃO 3: INSTRUÇÕES ADICIONAIS --}}
    <h5 class="mt-4">3. Instruções Adicionais</h5>
    <p class="text-muted">Escreva em linguagem natural qualquer outra restrição, preferência ou objetivo. A IA entenderá.</p>
    <div class="col-12">
        <textarea class="form-control" name="instrucoes_adicionais" id="instrucoes_adicionais" rows="5" placeholder="Exemplos:
- Priorize escalar a Maria no setor de Cozinha.
- O João não pode trabalhar nas manhãs de segunda-feira.
- Tente dar pelo menos 2 dias de folga seguidos para cada um.
- Funcionários com contrato intermitente devem ter preferência para cobrir folgas."></textarea>
    </div>

    <button type="submit" class="mt-4 btn btn-primary btn-lg"><i class="fa-solid fa-robot"></i> Gerar Escala com IA</button>
</form>
@endsection