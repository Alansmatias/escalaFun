@extends('site.layout')

@section('conteudo')
<h1>Relatórios<br><br></h1>

<table class="table">
    <thead>
        <tr>
            <th scope="col">Relatório</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><a style="text-decoration: none;" href="{{route('funcionarios_escalados_setor_turno')}}">Funcionários Escalados Por Setor e Turno</a></td>
        </tr>
    </tbody>
</table>

@endsection