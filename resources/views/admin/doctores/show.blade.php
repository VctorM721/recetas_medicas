@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Perfil de {{ $doctor->user->name }}</h1>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="p-3 border rounded">
        <div class="text-sm text-gray-500">Pacientes atendidos</div>
        <div class="text-2xl fw-bold">{{ $totalPacientes }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded">
        <div class="text-sm text-gray-500">Recetas emitidas</div>
        <div class="text-2xl fw-bold">{{ $totalRecetas }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="p-3 border rounded">
        <div class="text-sm text-gray-500">Facturas emitidas</div>
        <div class="text-2xl fw-bold">{{ $totalFacturas }}</div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2">
    <a href="{{ route('doctores.clientes',$doctor) }}" class="btn btn-primary">Ver clientes</a>
    <a href="{{ route('doctores.facturas',$doctor) }}" class="btn btn-outline-dark">Ver facturas</a>
    <a href="{{ route('doctores.index') }}" class="btn btn-light">Volver</a>
  </div>
</div>
@endsection