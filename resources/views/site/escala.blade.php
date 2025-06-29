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

<form class="row g-3 border p-0" method="GET" action="{{ route('escala') }}">
    <div class="row row-cols-1 row-cols-md-4 mb-3 d-flex flex-wrap">
        <!-- Filtro Funcionário -->
        <div class="me-3">
            <label for="filtroFuncionario" class="form-label">Funcionário</label>
            <select class="form-select" name="funcionario" id="filtroFuncionario">
                <option value="">Todos</option>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}" {{ request('funcionario') == $funcionario->id ? 'selected' : '' }}>
                        {{ $funcionario->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filtro Setor -->
        <div class="me-3">
            <label for="filtroSetor" class="form-label">Setor</label>
            <select class="form-select" name="setor" id="filtroSetor">
                <option value="">Todos</option>
                @foreach($setores as $setor)
                    <option value="{{ $setor->id }}" {{ request('setor') == $setor->id ? 'selected' : '' }}>
                        {{ $setor->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Filtro Turno -->
        <div>
            <label for="filtroTurno" class="form-label">Turno</label>
            <select class="form-select" name="turno" id="filtroTurno">
                <option value="">Todos</option>
                @foreach($turnos as $turno)
                    <option value="{{ $turno->id }}" {{ request('turno') == $turno->id ? 'selected' : '' }}>
                        {{ $turno->nome }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Botão de Filtrar -->
    <div class="col-12 mb-3">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter"></i> Filtrar</button>
    </div>
</form>

<form class="row g-3" method="POST" action="{{ route('escalar.atualizar') }}">
    @csrf
    {{-- Adiciona o ID do período como um campo oculto para ser enviado com o formulário --}}
    @if($periodo)
        <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">
    @endif

    <div class="col-12 mt-5">
        <button class="btn btn-primary mt-2" type="submit"><i class="fa-solid fa-check"></i> Atualizar Escala</button>
        <a href="{{route('escalarfun')}}" class="btn btn-primary mt-2"><i class="fa-solid fa-plus"></i> Adicionar Funcionário</a>
    </div>

    <table class="table row overflow-auto">
        <!-- Corpo da tabela -->
        <tbody>
            <!-- Cabeçalho da tabela -->
            <tr>
                <th scope="col">Funcionário</th>
                <th scope="col">Setor</th>
                <th scope="col">Turno</th>
                @if($escalaHeaders)
                    @foreach($escalaHeaders as $header)
                        <th scope="col" class="text-center" data-header-day="{{ $header['day'] }}">
                            <span class="badge bg-success mb-1 d-none daily-count">0</span><br>
                            {{ $header['dayName'] }}<br>
                            {{ $header['diaDoMes'] }}
                        </th>
                    @endforeach
                @else
                    <th scope="col" colspan="31">Nenhum período definido</th>
                @endif
                <th scope="col">Ações</th>
            </tr>
        @foreach($escalas as $key => $escalasGrupo)
            <tr data-funcionario-id="{{ $escalasGrupo->first()->funcionario->id }}">
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

                <!-- Status por Dia -->
                @if($escalaHeaders)
                    @foreach($escalaHeaders as $header)
                        <td data-dia="{{ $header['day'] }}">
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
                                } elseif ($status === 'A') {
                                    $buttonClass = 'btn-info';
                                }
                            @endphp
                            @php
                                $funcionarioId = $escalasGrupo->first()->funcionario->id;
                                $setorTurno = "{$escalasGrupo->first()->setor->id}-{$escalasGrupo->first()->turno->id}";
                                $status = $escala ? $escala->status : '#';

                                // Bloqueia apenas se o status for '#' e o funcionário já estiver escalado em outro setor/turno
                                $bloqueado = $status === '#' &&
                                            isset($bloqueios[$funcionarioId][$header['day']]) &&
                                            !in_array($setorTurno, $bloqueios[$funcionarioId][$header['day']]);
                            @endphp

                            <button type="button" class="btn {{ $buttonClass }} statusButton"
                                data-status="{{ $status }}"
                                data-dia="{{ $header['day'] }}"
                                data-funcionario="{{ $funcionarioId }}"
                                data-setor-turno="{{ $setorTurno }}"
                                data-observacao="{{ $escala ? $escala->observacao : 'Nenhuma observação disponível' }}"
                                {{ $bloqueado ? 'disabled' : '' }}
                                onclick="toggleStatus(this)">
                            {{ $bloqueado ? 'X' : $status }}
                        </button>
                            <input type="hidden" name="status[{{ $escalasGrupo->first()->funcionario->id }}-{{ $escalasGrupo->first()->setor->id }}-{{ $escalasGrupo->first()->turno->id }}][{{ $header['day'] }}]" value="{{ $status }}">
                        </td>
                    @endforeach
                @else
                    <td colspan="31">Nenhum período definido</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</form>

<script>
    // Função para atualizar a contagem de um dia específico
    function updateCountForDay(day) {
        const header = document.querySelector(`th[data-header-day="${day}"]`);
        if (!header) return;

        const countSpan = header.querySelector('.daily-count');
        const buttonsForDay = document.querySelectorAll(`tbody tr button[data-dia="${day}"]`);
        let countE = 0;

        buttonsForDay.forEach(btn => {
            if (btn.getAttribute('data-status') === 'E') {
                countE++;
            }
        });

        countSpan.textContent = countE;
        if (countE > 0) {
            countSpan.classList.remove('d-none');
        } else {
            countSpan.classList.add('d-none');
        }
    }

    // Atualiza todos os contadores ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        const headers = document.querySelectorAll('th[data-header-day]');
        headers.forEach(header => {
            updateCountForDay(header.dataset.headerDay);
        });
    });

    function toggleStatus(button) {
        const statuses = ["E", "D", "F", "#"];
        const classes = {
            "E": "btn-success",
            "D": "btn-warning",
            "F": "btn-danger",
            "#": "btn-secondary"
        };

        let currentStatus = button.getAttribute("data-status");

        // Se for 'A', exibe o modal com a observação
        if (currentStatus === "A") {
            let observacao = button.getAttribute("data-observacao");
            document.getElementById("observacaoTexto").textContent = observacao;
            let modal = new bootstrap.Modal(document.getElementById("observacaoModal"));
            modal.show();
            return;
        }

        // Alternar status normalmente
        let nextIndex = (statuses.indexOf(currentStatus) + 1) % statuses.length;
        let nextStatus = statuses[nextIndex];

        // Atualiza o status e a aparência do botão
        button.textContent = nextStatus;
        button.setAttribute("data-status", nextStatus);
        button.nextElementSibling.value = nextStatus;
        button.className = `btn ${classes[nextStatus]} statusButton`;

        // Atualiza a contagem para o dia que foi alterado
        updateCountForDay(button.dataset.dia);
    }
</script>

<!-- Modal de Observação -->
<div class="modal fade" id="observacaoModal" tabindex="-1" aria-labelledby="observacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="observacaoModalLabel">Observação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p id="observacaoTexto">Nenhuma observação disponível.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
@endsection
