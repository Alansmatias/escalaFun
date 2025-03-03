@extends('site.layout')

@section('conteudo')
<h1>Escala da Semana<br><br></h1>

<!-- Filtro e Gerar Relatório -->
<form class="row g-3" method="GET" action="{{ route('escala_da_semana') }}">

    <div class="col-md-3 mb-3">
        <label for="setor" class="form-label">Setor</label>
        <select class="form-select" name="setor" id="setor">
            <option value="">Todos</option>
            @foreach($setores as $setor)
                <option value="{{ $setor->id }}" {{ request('setor') == $setor->id ? 'selected' : '' }}>
                    {{ $setor->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label for="turno" class="form-label">Turno</label>
        <select class="form-select" name="turno" id="turno">
            <option value="">Todos</option>
            @foreach($turnos as $turno)
                <option value="{{ $turno->id }}" {{ request('turno') == $turno->id ? 'selected' : '' }}>
                    {{ $turno->nome }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label for="dataInicio" class="form-label">Data Inicial</label>
        <input type="date" class="form-control" name="dataInicio" id="dataInicio" value="{{ request('dataInicio') }}" required>
    </div>

    <div class="col-md-3 mb-3">
        <label for="dataFim" class="form-label">Data Final</label>
        <input type="date" class="form-control" name="dataFim" id="dataFim" value="{{ request('dataFim') }}" required>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Gerar Relatório</button>
    </div>
</form>

<br>

<!-- Tabela do Relatório -->
<div id="tabela-escalas"></div>
<div>
    <button class="btn btn-outline-secondary mt-2" id="print-table">Imprimir</button>
    <button class="btn btn-outline-secondary mt-2" id="download-pdf">Salvar PDF</button>
</div>

<script>
    var escalasData = {!! json_encode($escalas ?? []) !!};
    var escalaHeaders = {!! json_encode($escalaHeaders ?? []) !!};
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Tabulator iniciado!");

        // Criar estrutura de dados para a tabela
        var formattedData = [];
        var maxFuncionarios = Math.max(...Object.values(escalasData).map(arr => arr.length));

        for (var i = 0; i < maxFuncionarios; i++) {
            let row = {};
            escalaHeaders.forEach(header => {
                row[header.day] = escalasData[header.day]?.[i] || ""; // Se não tiver funcionário, fica vazio
            });
            formattedData.push(row);
        }

        // Criar colunas dinâmicas para cada dia
        var columns = escalaHeaders.map(header => ({
            title: `${header.dayName}<br>(${header.day.split('-').reverse().join('-')})`, // Exibe "Seg (01-03-2025)"
            //title: header.dayName, // Nome do dia ex: "Seg", "Ter"
            field: header.day, // Data no formato YYYY-MM-DD
            hozAlign:"center",
            headerHozAlign:"center",
        }));

        // Inicializa a tabela
        var tabela = new Tabulator("#tabela-escalas", {
            data: formattedData,
            layout: "fitColumns",
            printAsHtml: true,
            printHeader: `<h3>Escala da Semana Turno: {{ $turnos->where('id', request('turno'))->first()->nome ?? 'Todos' }} Setor: {{ $setores->where('id', request('setor'))->first()->nome ?? 'Todos' }}</h3>`,
            columns: columns
        });

        console.log("Tabela carregada com sucesso!");

        // Botão de impressão
        document.getElementById("print-table").addEventListener("click", function () {
            tabela.print(false, true);
        });

        // Botão de download em PDF
        document.getElementById("download-pdf").addEventListener("click", function () {
            tabela.download("pdf", "escala-semanal.pdf", {
                orientation: "portrait",
                title: "Escala da Semana Turno: {{ $turnos->where('id', request('turno'))->first()->nome ?? 'Todos' }} Setor: {{ $setores->where('id', request('setor'))->first()->nome ?? 'Todos' }}",
            });
        });
    });
</script>


@endsection
