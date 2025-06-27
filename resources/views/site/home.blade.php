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
                    <h6 class="mb-0">123</h6> {{-- Dado de exemplo --}}
                </div>
            </div>
        </div>

        <!-- Mensalistas -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-user-tie fa-3x text-success"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Mensalistas</p>
                    <h6 class="mb-0">85</h6> {{-- Dado de exemplo --}}
                </div>
            </div>
        </div>

        <!-- Intermitentes -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-user-clock fa-3x text-info"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Intermitentes</p>
                    <h6 class="mb-0">38</h6> {{-- Dado de exemplo --}}
                </div>
            </div>
        </div>

        <!-- Férias / Licença -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-plane-departure fa-3x text-warning"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Férias / Licença</p>
                    <h6 class="mb-0">5</h6> {{-- Dado de exemplo --}}
                </div>
            </div>
        </div>

        <!-- Atestados Hoje -->
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-light shadow-sm rounded d-flex flex-row align-items-center p-4 h-100">
                <i class="fa fa-file-medical fa-3x text-danger"></i>
                <div class="ms-3 text-end flex-grow-1">
                    <p class="mb-2">Atestados (Hoje)</p>
                    <h6 class="mb-0">2</h6> {{-- Dado de exemplo --}}
                </div>
            </div>
        </div>

        <!-- Force next columns to break to new line -->
        <div class="w-100"></div>

        <!-- Gráfico de Setores -->
        <div class="col-md-6">
            <div class="card bg-light shadow-sm rounded p-4 h-100 d-flex flex-column">
                <h6 class="mb-4">Funcionários por Setor (Exemplo)</h6>
                <div class="flex-grow-1 position-relative"><canvas id="setoresChart"></canvas></div>
            </div>
        </div>

        <!-- Gráfico de Contratos -->
        <div class="col-md-6">
            <div class="card bg-light shadow-sm rounded p-4 h-100 d-flex flex-column">
                <h6 class="mb-4">Tipo de Contrato (Exemplo)</h6>
                <div class="flex-grow-1 position-relative"><canvas id="contratosChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    // Gráfico 1: Funcionários por Setor (Gráfico de Pizza/Doughnut)
    const ctxSetores = document.getElementById('setoresChart');
    if (ctxSetores) {
        new Chart(ctxSetores, {
            type: 'doughnut',
            data: {
                labels: ['Cozinha', 'Limpeza', 'Recepção', 'Administrativo', 'Manutenção'],
                datasets: [{
                    label: 'Nº de Funcionários',
                    data: [45, 25, 18, 12, 8], // Dados de exemplo
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)', 'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)', 'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // Gráfico 2: Tipo de Contrato (Gráfico de Barras)
    const ctxContratos = document.getElementById('contratosChart');
    if (ctxContratos) {
        new Chart(ctxContratos, {
            type: 'bar',
            data: {
                labels: ['Mensalista', 'Intermitente'],
                datasets: [{
                    label: 'Total por Contrato',
                    data: [85, 38], // Dados de exemplo
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