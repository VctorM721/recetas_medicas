@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4">Nuevo doctor</h1>

  <form method="POST" action="{{ route('doctores.store') }}" class="mt-3">
    @csrf
    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
      @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input name="password" type="password" class="form-control" required>
      @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
      <label class="form-label">CMP/Registro</label>
      <input name="cmp" class="form-control" value="{{ old('cmp') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Especialidad</label>
      <input name="especialidad" class="form-control" value="{{ old('especialidad') }}">
    </div>

    <button class="btn btn-primary">Guardar</button>
    <a class="btn btn-light" href="{{ route('doctores.index') }}">Cancelar</a>
  </form>
</div>
@endsection