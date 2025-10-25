@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4">Estadísticas: {{ $doctor->user->name }}</h1>

  <ul class="list-group my-4">
    <li class="list-group-item">Pacientes atendidos: <strong>{{ $totalPacientes }}</strong></li>
    <li class="list-group-item">Recetas emitidas: <strong>{{ $totalRecetas }}</strong></li> 
  </ul>
<div class="mt-4">
  <x-secondary-button as="a" href="{{ route('doctores.index') }}">
      Volver
  </x-secondary-button>
</div>
</div>
@endsection