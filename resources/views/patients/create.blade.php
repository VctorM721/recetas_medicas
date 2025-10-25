<x-layouts.app :title="'Nuevo paciente'">
  <div class="mb-3">
    <a href="{{ route('patients.index') }}" class="px-3 py-1.5 border rounded hover:bg-slate-50">← Volver al listado</a>
  </div>

  <h1 class="text-2xl font-bold mb-4">Nuevo paciente</h1>
@can('admin-only')
  <div class="mb-3">
    <label class="form-label">Doctor que firma</label>
    <select name="doctor_id" class="form-select">
      <option value="">— Selecciona doctor —</option>
      @foreach($doctores as $d)
        <option value="{{ $d->id }}">{{ $d->user->name }} @if($d->especialidad) ({{ $d->especialidad }}) @endif</option>
      @endforeach
    </select>
  </div>
@endcan
  <form method="POST" action="{{ route('patients.store') }}" class="space-y-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Nombre completo *</label>
        <input name="full_name" class="w-full border p-2 rounded" value="{{ old('full_name') }}" required>
        @error('full_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">DPI</label>
        <input name="dpi" class="w-full border p-2 rounded" value="{{ old('dpi') }}">
        @error('dpi') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Fecha de nacimiento</label>
        <input type="date" name="birthdate" class="w-full border p-2 rounded" value="{{ old('birthdate') }}">
        @error('birthdate') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Sexo</label>
        <select name="sex" class="w-full border p-2 rounded">
          <option value="" @selected(old('sex')==='')>—</option>
          <option value="M" @selected(old('sex')==='M')>M</option>
          <option value="F" @selected(old('sex')==='F')>F</option>
          <option value="X" @selected(old('sex')==='X')>X</option>
        </select>
        @error('sex') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Teléfono</label>
        <input name="phone" class="w-full border p-2 rounded" value="{{ old('phone') }}">
        @error('phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Dirección</label>
        <input name="address" class="w-full border p-2 rounded" value="{{ old('address') }}">
        @error('address') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex gap-3">
      <a href="{{ route('patients.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 bg-slate-900 text-white rounded">Guardar</button>
    </div>
  </form>
</x-layouts.app>