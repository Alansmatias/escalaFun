@extends('site.layout')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


@section('conteudo')

<nav class="navbar bg-body-tertiary">
  <div class="container-fluid">
  <button type="button" class="btn btn-success"onclick="window.location.href='{{ route('home.cadastro.funcionario') }}'">Novo</button>
    <form class="d-flex" role="search">
      <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Search">
      <button class="btn btn-outline-success" type="submit">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
      </svg>
      </button>
    </form>
  </div>
</nav>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Id</th>
      <th scope="col">Nome</th>
      <th scope="col">Telefone</th>
      <th scope="col">Contrato</th>
    </tr>
  </thead>
  <tbody>
    @foreach($funcionarios as $funcionario)
      <tr>
        <th scope="row">{{ $funcionario->id }}</th>
        <td>{{ $funcionario->nome }}</td>
        <td>{{ $funcionario->telefone }}</td>
        <td>{{ $funcionario->contrato }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

@endsection