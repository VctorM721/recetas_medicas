@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4">Editar doctor</h1>

  <form method="POST" action="{{ route('doctores.update',$doctor) }}" class="mt-3">
    @csrf @method('PUT')

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input name="name" class="form-control" value="{{ old('name', $doctor->user->name) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="{{ old('email', $doctor->user->email) }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña (opcional)</label>
      <input name="password" type="password" class="form-control">
      <div class="form-text">Déjala vacía para no cambiarla.</div>
    </div>

    <div class="mb-3">
      <label class="form-label">CMP/Registro</label>
      <input name="cmp" class="form-control" value="{{ old('cmp', $doctor->cmp) }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Especialidad</label>
      <input name="especialidad" class="form-control" value="{{ old('especialidad', $doctor->especialidad) }}">
    </div>

    <button class="btn btn-primary">Actualizar</button>
    <a class="btn btn-light" href="{{ route('doctores.index') }}">Volver</a>
  </form>
</div>
@endsection