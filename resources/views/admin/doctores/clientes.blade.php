@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Clientes de {{ $doctor->user->name }}</h1>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Paciente</th>
        <th>Recetas de este doctor</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($clientes as $p)
        <tr>
          <td>{{ $p->name ?? $p->full_name ?? ('Paciente #'.$p->id) }}</td>
          <td>{{ $p->recetas_del_doctor_count }}</td>
          <td>
            <a href="{{ route('patients.show', $p->uuid) }}" class="btn btn-sm btn-outline-primary">Ver paciente</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="3">Sin clientes registrados.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $clientes->links() }}

  <a class="btn btn-light mt-3" href="{{ route('doctores.show',$doctor) }}">Volver</a>
</div>
@endsection