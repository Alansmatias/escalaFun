@extends('site.layout')

@section('conteudo')

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

<h1>Escalar Novo Funcionário<br><br></h1>

<form class="row g-3 overflow-auto" method="POST" action="{{ route('escalar.salvar') }}">
    @csrf
    @if($periodo)
        <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">
    @endif

    <table class="table">
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
        <tbody id="escala-tbody">
            @for ($i = 0; $i < 1; $i++)
            <tr>
                <!-- Funcionário -->
                <th scope="row">
                    <div class="select-dynamic">
                        <select class="form-select" name="funcionario[{{ $i }}]" id="funcionario-{{ $i }}" required>
                            <option selected disabled value="">Selecione...</option>
                            @foreach($funcionarios as $funcionario)
                                <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </th>

                <!-- Setor -->
                <td>
                    <div class="select-dynamic">
                        <select class="form-select" name="setor[{{ $i }}]" id="setor-{{ $i }}" required>
                            <option selected disabled value="">Selecione...</option>
                            @foreach($setores as $setor)
                                <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>

                <!-- Turno -->
                <td>
                    <div class="select-dynamic">
                        <select class="form-select" name="turno[{{ $i }}]" id="turno-{{ $i }}" required>
                            <option selected disabled value="">Selecione...</option>
                            @foreach($turnos as $turno)
                                <option value="{{ $turno->id }}">{{ $turno->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>

                <!-- Dias -->
                @if($escalaHeaders)
                    @foreach($escalaHeaders as $header)
                    <td>
                        <button type="button" class="btn btn-secondary statusButton" data-status="#" onclick="toggleStatus(this)">#</button>
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
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-check"></i> Salvar</button>
        <button class="btn btn-success" type="button" id="add-row-btn"><i class="fa-solid fa-plus"></i> Adicionar Linha</button>
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
        button.className = `btn ${classes[nextStatus]} statusButton`;
    }

    document.getElementById('add-row-btn').addEventListener('click', function () {
        const tbody = document.getElementById('escala-tbody');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        const newIndex = tbody.rows.length;

        newRow.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });

        newRow.querySelectorAll('.statusButton').forEach(button => {
            button.textContent = '#';
            button.setAttribute('data-status', '#');
            button.className = 'btn btn-secondary statusButton';
            button.nextElementSibling.value = '#';
        });

        newRow.querySelectorAll('[name]').forEach(element => {
            let name = element.getAttribute('name');
            if (name) {
                element.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
            }
        });

        newRow.querySelectorAll('[id]').forEach(element => {
            let id = element.getAttribute('id');
            if (id) {
                element.setAttribute('id', id.replace(/-\d+$/, `-${newIndex}`));
            }
        });

        tbody.appendChild(newRow);
    });
    function ajustarLarguraSelect(select) {
        const span = document.createElement("span");
        span.style.visibility = "hidden";
        span.style.position = "absolute";
        span.style.whiteSpace = "nowrap";
        span.style.font = window.getComputedStyle(select).font;
        span.innerText = select.options[select.selectedIndex]?.text || select.options[0]?.text;

        document.body.appendChild(span);
        const largura = span.offsetWidth + 40; // margem de segurança
        document.body.removeChild(span);

        select.style.width = `${largura}px`;
    }

    // Aplica nos selects iniciais
    document.querySelectorAll("select").forEach(select => {
        ajustarLarguraSelect(select);
        select.addEventListener("change", () => ajustarLarguraSelect(select));
    });

    // Quando adicionar nova linha
    document.getElementById('add-row-btn').addEventListener('click', function () {
        // ... código existente ...

        // Depois que a nova linha for adicionada:
        const novosSelects = tbody.querySelectorAll('tr:last-child select');
        novosSelects.forEach(select => {
            ajustarLarguraSelect(select);
            select.addEventListener("change", () => ajustarLarguraSelect(select));
        });
    });
</script>

@endsection
