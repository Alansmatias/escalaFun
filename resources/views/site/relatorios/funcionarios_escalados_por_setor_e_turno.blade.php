@extends('site.layout')

@section('conteudo')
<h1>Funcionários Escalados No Período Por Setor e Turno<br><br></h1>

<!-- Filtro e Gerar Relatório -->
<form class="row g-3" method="GET" action="{{ route('funcionarios_escalados_setor_turno') }}">

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
        <input type="date" class="form-control" name="dataInicio" id="dataInicio" value="{{ request('dataInicio') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="dataFim" class="form-label">Data Final</label>
        <input type="date" class="form-control" name="dataFim" id="dataFim" value="{{ request('dataFim') }}">
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
    var escalasData = {!! json_encode($escalas) !!};
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("Tabulator iniciado!");

        // Verifica se os dados estão sendo carregados corretamente
        console.log("Dados carregados:", escalasData);

        // Converte os dados do Laravel para o formato esperado pelo Tabulator
        var formattedData = escalasData.map(escala => ({
            data: escala.data,
            dia_semana: escala.dia_semana,
            funcionarios: escala.funcionarios.replace(/;/g, ', ') // Substitui ";" por ", "
        }));

        // Inicializa a tabela
        var tabela = new Tabulator("#tabela-escalas", {
            data: formattedData,
            layout: "fitColumns",
            printAsHtml: true,
            printHeader:"<h3>Escalados no periodo setor: {{ $setores->where('id', request('setor'))->first()->nome ?? 'Todos' }} turno: {{ $turnos->where('id', request('turno'))->first()->nome ?? 'Todos' }}<h3>",
            // printFooter:"<h2>Example Table Footer<h2>",
            columns: [
                { title: "Data", field: "data", hozAlign: "center" },
                { title: "Dia da Semana", field: "dia_semana", hozAlign: "center" },
                { title: "Funcionários", field: "funcionarios", hozAlign: "left" }
            ]
        });

        console.log("Tabela carregada com sucesso!");

        //print button
        document.getElementById("print-table").addEventListener("click", function(){
            tabela.print(false, true);
        });

        document.getElementById("download-pdf").addEventListener("click", function(){
            tabela.download("pdf", "data.pdf", {
                orientation:"portrait", //set page orientation to portrait
                title:"Example Report", //add title to report
            });
        });
    });
</script>


@endsection
