<x-layouts.app :title="'Nueva receta'">
  <h1 class="text-2xl font-bold mb-4">Nueva receta para {{ $patient->full_name }}</h1>

  <form method="POST" action="{{ route('prescriptions.store', $patient->uuid) }}" class="space-y-6">
    @csrf

    {{-- Datos generales --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Diagnóstico</label>
        <textarea name="diagnosis" rows="2" class="w-full border p-2 rounded">{{ old('diagnosis') }}</textarea>
        @error('diagnosis') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Fecha de emisión</label>
        <input type="date" name="issued_at"
               value="{{ old('issued_at', now()->format('Y-m-d')) }}"
               class="w-full border p-2 rounded">
        @error('issued_at') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
@can('admin-only')
  @if(isset($doctores) && $doctores->count())
    <div class="mb-3">
      <label class="form-label">Doctor que firma</label>
      <select name="doctor_id" class="form-select">
        <option value="">— Selecciona doctor —</option>
        @foreach($doctores as $d)
          <option value="{{ $d->id }}">{{ $d->user->name }} @if($d->especialidad) ({{ $d->especialidad }}) @endif</option>
        @endforeach
      </select>
    </div>
  @endif
@endcan

      <div>
        <label class="block text-sm font-medium mb-1">Médico</label>
        <input name="doctor_name" value="{{ old('doctor_name', auth()->user()->name ?? '') }}"
               class="w-full border p-2 rounded">
        @error('doctor_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">Notas al farmacéutico / indicaciones generales</label>
        <textarea name="notes" rows="2" class="w-full border p-2 rounded">{{ old('notes') }}</textarea>
        @error('notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Medicamentos --}}
    <div class="border rounded">
      <div class="flex items-center justify-between p-3">
        <h2 class="font-semibold">Medicamentos</h2>
        <button type="button" id="btnAdd" class="px-3 py-1 rounded bg-emerald-600 text-white">+ Agregar</button>
      </div>

      <div class="p-3 space-y-3" id="items">
        @php
          $oldItems = old('items', [['drug'=>'','dose'=>'','frequency'=>'','duration'=>'','instructions'=>'']]);
        @endphp

        @foreach($oldItems as $i => $it)
          <div class="grid md:grid-cols-5 gap-2 item-row">
            <input name="items[{{ $i }}][drug]" class="border p-2 rounded" placeholder="Medicamento" required value="{{ $it['drug'] ?? '' }}">
            <input name="items[{{ $i }}][dose]" class="border p-2 rounded" placeholder="Dosis (p. ej. 500 mg)" required value="{{ $it['dose'] ?? '' }}">
            <input name="items[{{ $i }}][frequency]" class="border p-2 rounded" placeholder="Frecuencia (p. ej. cada 8h)" required value="{{ $it['frequency'] ?? '' }}">
            <input name="items[{{ $i }}][duration]" class="border p-2 rounded" placeholder="Duración (p. ej. 7 días)" required value="{{ $it['duration'] ?? '' }}">
            <div class="flex gap-2">
              <input name="items[{{ $i }}][instructions]" class="border p-2 rounded flex-1" placeholder="Instrucciones" value="{{ $it['instructions'] ?? '' }}">
              <button type="button" class="px-3 rounded bg-red-600 text-white btn-remove" title="Eliminar">✕</button>
            </div>
          </div>
        @endforeach

        @error('items') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @foreach (['drug','dose','frequency','duration','instructions'] as $f)
          @error("items.*.$f") <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        @endforeach
      </div>
    </div>

    <div class="flex gap-3">
      <a href="{{ route('patients.show', $patient->uuid) }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 bg-slate-900 text-white rounded">Guardar receta</button>
    </div>
  </form>

  <template id="rowTpl">
    <div class="grid md:grid-cols-5 gap-2 item-row">
      <input name="__name__[drug]" class="border p-2 rounded" placeholder="Medicamento" required>
      <input name="__name__[dose]" class="border p-2 rounded" placeholder="Dosis (p. ej. 500 mg)" required>
      <input name="__name__[frequency]" class="border p-2 rounded" placeholder="Frecuencia (p. ej. cada 8h)" required>
      <input name="__name__[duration]" class="border p-2 rounded" placeholder="Duración (p. ej. 7 días)" required>
      <div class="flex gap-2">
        <input name="__name__[instructions]" class="border p-2 rounded flex-1" placeholder="Instrucciones">
        <button type="button" class="px-3 rounded bg-red-600 text-white btn-remove" title="Eliminar">✕</button>
      </div>
    </div>
  </template>

  <script>
    (function () {
      const items = document.getElementById('items');
      const btnAdd = document.getElementById('btnAdd');
      const tpl = document.getElementById('rowTpl').innerHTML;

      function reindex() {
        const rows = items.querySelectorAll('.item-row');
        rows.forEach((row, i) => {
          row.querySelectorAll('input[name]').forEach(inp => {
            inp.name = inp.name.replace(/items\[\d+\]/, `items[${i}]`).replace('__name__', `items[${i}]`);
          });
        });
      }

      btnAdd.addEventListener('click', () => {
        const div = document.createElement('div');
        div.innerHTML = tpl.replaceAll('__name__', `items[${items.querySelectorAll('.item-row').length}]`);
        const node = div.firstElementChild;
        items.appendChild(node);
        reindex();
      });

      items.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-remove')) {
          const rows = items.querySelectorAll('.item-row');
          if (rows.length > 1) {
            e.target.closest('.item-row')?.remove();
            reindex();
          }
        }
      });
    })();
  </script>
</x-layouts.app>