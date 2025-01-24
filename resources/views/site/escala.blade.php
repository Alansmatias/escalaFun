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
<h1>Gerenciar Escala de Funcionários<br><br></h1>

<form class="row g-3" method="POST" action="{{ route('escalar.atualizar') }}">
    @csrf
    <table class="table">
        <!-- Cabeçalho da tabela -->
        <thead>
            <tr>
                <th scope="col">Funcionário</th>
                <th scope="col">Setor</th>
                <th scope="col">Turno</th>
                @if($escalaHeaders)
                    @foreach($escalaHeaders as $header)
                        <th scope="col">{{ $header['dayName'] }}<br>{{ $header['diaDoMes'] }}</th>
                    @endforeach
                @else
                    <th scope="col" colspan="31">Nenhum período definido</th>
                @endif
                <th scope="col">Ações</th>
            </tr>
        </thead>

        <!-- Corpo da tabela -->
        <tbody>
        @foreach($escalas as $key => $escalasGrupo)
        <tr>
            <!-- Nome do Funcionário -->
            <td>
                {{ $escalasGrupo->first()->funcionario->nome }}
                <input type="hidden" name="funcionario[{{ $key }}]" value="{{ $escalasGrupo->first()->funcionario->id }}">
            </td>

            <!-- Nome do Setor -->
            <td>
                {{ $escalasGrupo->first()->setor->nome }}
                <input type="hidden" name="setor[{{ $key }}]" value="{{ $escalasGrupo->first()->setor->id }}">
            </td>

            <!-- Nome do Turno -->
            <td>
                {{ $escalasGrupo->first()->turno->nome }}
                <input type="hidden" name="turno[{{ $key }}]" value="{{ $escalasGrupo->first()->turno->id }}">
            </td>
            @dd($diasEscalados);
            <!-- Status por Dia -->
            @if($escalaHeaders)
                @foreach($escalaHeaders as $header)
                    <td>
                        @php
                            $escala = $escalasGrupo->firstWhere('dia', $header['day']);
                            $status = $escala ? $escala->status : '#';
                            $buttonClass = 'btn-secondary'; // Classe padrão

                            if ($status === 'E') {
                                $buttonClass = 'btn-success';
                            } elseif ($status === 'D') {
                                $buttonClass = 'btn-warning';
                            } elseif ($status === 'F') {
                                $buttonClass = 'btn-danger';
                            }
                        @endphp
                        <button type="button" class="btn {{ $buttonClass }} statusButton" data-status="{{ $status }}" onclick="toggleStatus(this)">
                            {{ $status }}
                        </button>
                        <input type="hidden" name="status[{{ $key }}][{{ $header['day'] }}]" value="{{ $status }}">
                    </td>
                @endforeach
            @else
                <td colspan="31">Nenhum período definido</td>
            @endif
        </tr>
        @endforeach
        </tbody>
    </table>

    <div class="col-12 mb-3">
        <button class="btn btn-primary" type="submit">Atualizar Escala</button>
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

        button.textContent = nextStatus;
        button.setAttribute("data-status", nextStatus);
        button.nextElementSibling.value = nextStatus;

        // Atualizar a classe do botão
        button.className = `btn ${classes[nextStatus]} statusButton btn-e`;
    }
</script>
@endsection
