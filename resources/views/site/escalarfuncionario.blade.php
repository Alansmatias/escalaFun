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
    {{-- Adiciona o ID do período como um campo oculto para ser enviado com o formulário --}}
    @if($periodo)
        <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">
    @endif@extends('site.layout')
    
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
        {{-- Adiciona o ID do período como um campo oculto para ser enviado com o formulário --}}
        @if($periodo)
            <input type="hidden" name="periodo_id" value="{{ $periodo->id }}">
        @endif
    
        <table class="table">
            <!-- Cabeçalho da tabela -->
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
            <!-- Corpo da tabela -->
            <tbody id="escala-tbody">
            <!-- Loop para os funcionários -->
            {{-- @for ($i = 0; $i < count($funcionarios); $i++) <!-- Exemplo iterando todos os funcionários --> --}}
            @for ($i = 0; $i < 1; $i++) <!-- Inicia com uma linha -->
            <tr>
                <!-- Select para Funcionário -->
                <th scope="row">
                    <select class="form-select" name="funcionario[{{ $i }}]" id="funcionario-{{ $i }}" required>
                        <option selected disabled value="">Selecione...</option>
                        @foreach($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                        @endforeach
                    </select>
                </th>
    
                <!-- Select para Setor -->
                <td>
                    <select class="form-select" name="setor[{{ $i }}]" id="setor-{{ $i }}" required>
                        <option selected disabled value="">Selecione...</option>
                        @foreach($setores as $setor)
                            <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                </td>
    
                <!-- Select para Turno -->
                <td>
                    <select class="form-select" name="turno[{{ $i }}]" id="turno-{{ $i }}" required>
                        <option selected disabled value="">Selecione...</option>
                        @foreach($turnos as $turno)
                            <option value="{{ $turno->id }}">{{ $turno->nome }}</option>
                        @endforeach
                    </select>
                </td>
    
                <!-- Botões para os Dias -->
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
    
            // Atualiza o status e a aparência do botão
            button.textContent = nextStatus;
            button.setAttribute("data-status", nextStatus);
            button.nextElementSibling.value = nextStatus;
            button.className = `btn ${classes[nextStatus]} statusButton`;
        }
    
        document.getElementById('add-row-btn').addEventListener('click', function() {
            const tbody = document.getElementById('escala-tbody');
            const firstRow = tbody.querySelector('tr');
            if (!firstRow) {
                console.error('Não foi possível encontrar uma linha modelo para clonar.');
                return;
            }
            const newRow = firstRow.cloneNode(true);
            const newIndex = tbody.rows.length;
    
            // Limpa os valores dos selects e botões na nova linha
            newRow.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0; // Reseta para "Selecione..."
            });
    
            newRow.querySelectorAll('.statusButton').forEach(button => {
                button.textContent = '#';
                button.setAttribute('data-status', '#');
                button.className = 'btn btn-secondary statusButton'; // Remove classes de cor
                if (button.nextElementSibling) {
                    button.nextElementSibling.value = '#';
                }
            });
    
            // Atualiza os nomes e IDs dos inputs e selects para o novo índice
            newRow.querySelectorAll('[name]').forEach(element => {
                let name = element.getAttribute('name');
                if (name) {
                    // Regex para substituir o primeiro número entre colchetes
                    element.setAttribute('name', name.replace(/\[\d+\]/, `[${newIndex}]`));
                }
                let id = element.getAttribute('id');
                if (id) {
                    // Regex para substituir o número no final do ID
                    element.setAttribute('id', id.replace(/-\d+$/, `-${newIndex}`));
                }
            });
    
            tbody.appendChild(newRow);
        });
    </script>
    @endsection
    

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
                <select class="form-select" name="funcionario[{{ $i }}]" id="funcionario-{{ $i }}" required>
                    <option selected disabled value="">Selecione...</option>
                    @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                    @endforeach
                </select>
            </th>

            <!-- Select para Setor -->
            <td>
                <select class="form-select" name="setor[{{ $i }}]" id="setor-{{ $i }}" required>
                    <option selected disabled value="">Selecione...</option>
                    @foreach($setores as $setor)
                        <option value="{{ $setor->id }}">{{ $setor->nome }}</option>
                    @endforeach
                </select>
            </td>

            <!-- Select para Turno -->
            <td>
                <select class="form-select" name="turno[{{ $i }}]" id="turno-{{ $i }}" required>
                    <option selected disabled value="">Selecione...</option>
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
