@extends('site.layout')

@section('conteudo')

<div class="container-fluid pt-4 px-4">
    <h1 class="mb-4">Dashboard</h1>
    <div class="row g-4">
        <!-- Total de Funcionários (ATIVOS)-->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-users fa-3x text-primary"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Funcionários Ativos</p>
                    <h6 class="mb-0">{{ $funcionariosAtivos ?? 0 }}</h6>
                </div>
            </div>
        </div>

        <!-- Mensalistas -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-user-tie fa-3x text-success"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Mensalistas</p>
                    <h6 class="mb-0">{{ $mensalistas ?? 0 }}</h6>
                </div>
            </div>
        </div>

        <!-- Intermitentes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-user-clock fa-3x text-info"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Intermitentes</p>
                    <h6 class="mb-0">{{ $intermitentes ?? 0 }}</h6>
                </div>
            </div>
        </div>

        <!-- Férias / Licença -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-plane-departure fa-3x text-warning"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Férias / Licença</p>
                    <h6 class="mb-0">{{ $feriasLicenca ?? 0 }}</h6>
                </div>
            </div>
        </div>

        <!-- Atestados Hoje -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-file-medical fa-3x text-danger"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Atestados (Hoje)</p>
                    <h6 class="mb-0">{{ $atestadosHoje ?? 0 }}</h6>
                </div>
            </div>
        </div>

        <!-- Force next columns to break to new line -->
        <div class="w-100"></div>

        <!-- Gráfico de Contratos -->
        <div class="col-12">
            <div class="card bg-light shadow-sm rounded p-4 h-100 d-flex flex-column">
                <h6 class="mb-4">Tipo de Contrato (Ativos)</h6>
                <div class="flex-grow-1 position-relative" style="height: 300px;"><canvas id="contratosChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    // Gráfico 2: Tipo de Contrato (Gráfico de Barras)
    const ctxContratos = document.getElementById('contratosChart');
    if (ctxContratos) {
        new Chart(ctxContratos, {
            type: 'bar',
            data: {
                labels: {!! json_encode($contratosLabels ?? []) !!},
                datasets: [{
                    label: 'Total por Contrato',
                    data: {!! json_encode($contratosData ?? []) !!},
                    backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(255, 99, 132, 0.8)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

@endsection