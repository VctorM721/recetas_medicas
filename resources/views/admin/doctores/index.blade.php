@extends('layouts.app')

@section('content')
<div class="container py-4">
  @if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">Doctores</h1>
    <a href="{{ route('doctores.create') }}" class="btn btn-primary">Nuevo doctor</a>
  </div>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Email</th>
        <th>Especialidad</th>
        <th style="width:280px">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($doctores as $d)
        <tr>
          <td>{{ $d->user->name }}</td>
          <td>{{ $d->user->email }}</td>
          <td>{{ $d->especialidad }}</td>
          <td class="d-flex gap-2">
            <a class="btn btn-secondary btn-sm" href="{{ route('doctores.edit',$d) }}">Editar</a>
            <a class="btn btn-outline-dark btn-sm" href="{{ route('doctores.stats',$d) }}">Estadísticas</a>

            <form action="{{ route('impersonate.start',$d) }}" method="POST" onsubmit="return confirm('¿Entrar como este doctor?')">
              @csrf
              <button class="btn btn-outline-warning btn-sm">Entrar como</button>
            </form>

            <form action="{{ route('doctores.destroy',$d) }}" method="POST" onsubmit="return confirm('¿Eliminar definitivamente?')">
              @csrf @method('DELETE')
              <button class="btn btn-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4">No hay doctores aún.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $doctores->links() }}
</div>
@endsection