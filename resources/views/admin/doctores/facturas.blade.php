@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Facturas de {{ $doctor->user->name }}</h1>

  @if(!class_exists(\App\Models\Invoice::class))
    <div class="alert alert-warning">No existe el modelo <code>Invoice</code> en este proyecto.</div>
  @else
    <table class="table table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Paciente</th>
          <th>Monto</th>
          <th>Fecha</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($facturas as $f)
          <tr>
            <td>{{ $f->id }}</td>
            <td>{{ $f->patient->name ?? ('Paciente #'.$f->patient_id) }}</td>
            <td>{{ $f->amount ?? '-' }}</td>
            <td>{{ $f->created_at?->format('Y-m-d H:i') }}</td>
            <td>
              @if(Route::has('invoices.show'))
                <a href="{{ route('invoices.show', $f) }}" class="btn btn-sm btn-outline-primary">Ver</a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5">Sin facturas.</td></tr>
        @endforelse
      </tbody>
    </table>

    {{ $facturas->links() }}
  @endif

  <a class="btn btn-light mt-3" href="{{ route('doctores.show',$doctor) }}">Volver</a>
</div>
@endsection