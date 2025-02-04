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

@section('conteudo')
<h1>Escalar Novo Funcionário<br><br></h1>

<form class="row g-3 overflow-auto" method="POST" action="{{ route('escalar.salvar') }}">
    @csrf
    <table class="table">
        <!-- Código anterior -->
        <thead>
            <tr>
                <th scope="col">Funcionário</th>
                <th scope="col">Setor</th>
                <th scope="col">Turno</th>
                @if($escalaHeaders)
                    @foreach($escalaHeaders as $header)
                        <th scope="col">{{ $header['dayName'] }}<br>{{ $header['day'] }}</th>
                    @endforeach
                @else
                    <th scope="col" colspan="31">Nenhum período definido</th>
                @endif
            </tr>
        </thead>
        <!-- Código posterior -->
        <tbody>
        <!-- Loop para os funcionários -->
        {{-- @for ($i = 0; $i < count($funcionarios); $i++) <!-- Exemplo iterando todos os funcionários --> --}}
        @for ($i = 0; $i < 1; $i++) <!-- Exemplo loop 1 -->
        <tr>
            <!-- Select para Funcionário -->
            <th scope="row">
                <select class="form-select" name="funcionario[{{ $i }}]" id="funcionario-{{ $i }}">
                    <option selected disabled value="">Selecione o Funcionário</option>
                    @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                    @endforeach
                </select>
            </th>

            <!-- Select para Setor -->
            <td>
                <select class="form-select" name="setor[{{ $i }}]" id="setor-{{ $i }}">
                    <option selected disabled value="">Selecione o Setor</option>
                    @foreach($setores as $setor)
                        <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                    @endforeach
                </select>
            </td>

            <!-- Select para Turno -->
            <td>
                <select class="form-select" name="turno[{{ $i }}]" id="turno-{{ $i }}">
                    <option selected disabled value="">Selecione o Turno</option>
                    @foreach($turnos as $turno)
                        <option value="{{ $turno->id }}">{{ $turno->nome }}</option>
                    @endforeach
                </select>
            </td>

            <!-- Botões para os Dias -->
            @if($escalaHeaders)
                @foreach($escalaHeaders as $header)
                <td>
                    <button type="button" class="btn btn-secondary statusButton btn-e" data-status="#" onclick="toggleStatus(this)">#</button>
                    <input type="hidden" name="status[{{ $i }}][{{ $header['day'] }}]" value="#">
                </td>
                @endforeach
            @else
                <td colspan="31">Nenhum período definido</td>
            @endif
        </tr>
        @endfor
    </tbody>

    </table>

    <div class="col-12 mb-3">
        <button class="btn btn-primary" type="submit">Salvar</button>
    </div>
    
</form>


<script>
    function toggleStatus(button) {
        const statuses = ["E", "D", "F", "#"];
        const classes = {
            "E": "btn-success",
            "D": "btn-warning",
            "F": "btn-danger",
            "#": "btn-secondary"
        };

        let currentStatus = button.getAttribute("data-status");
        let nextIndex = (statuses.indexOf(currentStatus) + 1) % statuses.length;
        let nextStatus = statuses[nextIndex];

        // Atualiza o status e a aparência do botão
        button.textContent = nextStatus;
        button.setAttribute("data-status", nextStatus);
        button.nextElementSibling.value = nextStatus;
        button.className = `btn ${classes[nextStatus]} statusButton`;
    }
</script>
@endsection
