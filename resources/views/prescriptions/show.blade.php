<x-layouts.app :title="'Receta'">
  <div class="flex items-start justify-between gap-6">
    <div class="flex-1">
      <h1 class="text-2xl font-bold mb-1">Receta #{{ $prescription->uuid }}</h1>

        <div class="mb-3 flex gap-2">
  <a href="{{ route('patients.show', $prescription->patient->uuid) }}"
     class="px-3 py-1.5 border rounded hover:bg-slate-50">← Volver al paciente</a>

  <a href="{{ route('patients.index') }}"
     class="px-3 py-1.5 border rounded hover:bg-slate-50">← Volver al inicio</a>

  <a href="{{ route('prescriptions.pdf', $prescription->uuid) }}"
     class="ml-auto px-3 py-1.5 rounded bg-slate-900 text-white">Descargar PDF</a>
</div>
      <div class="text-sm text-slate-600 mb-3">
        Fecha:
        {{
          optional($prescription->issued_at)->format('d/m/Y')
            ?? \Illuminate\Support\Carbon::parse($prescription->created_at)->format('d/m/Y')
        }}
        · Paciente: {{ optional($prescription->patient)->full_name ?? '—' }}
        · Dr. {{ optional($prescription->doctor)->name ?? ($prescription->doctor_name ?? '—') }}
      </div>

      @if($prescription->diagnosis)
        <div class="mb-3"><span class="font-semibold">Diagnóstico:</span> {{ $prescription->diagnosis }}</div>
      @endif

      @if($prescription->notes)
        <div class="mb-4"><span class="font-semibold">Notas:</span> {{ $prescription->notes }}</div>
      @endif

      <div class="border rounded">
        <table class="w-full text-left">
          <thead class="bg-slate-100">
            <tr>
              <th class="p-2">Medicamento</th>
              <th class="p-2">Dosis</th>
              <th class="p-2">Frecuencia</th>
              <th class="p-2">Duración</th>
              <th class="p-2">Instrucciones</th>
            </tr>
          </thead>
          <tbody>
            
  @forelse($prescription->items as $it)
<tr>
  <td>{{ $it->drug }}</td>
  <td>{{ $it->dose }}</td>
  <td>{{ $it->frequency }}</td>
  <td>{{ $it->duration }}</td>
  <td>{{ $it->instructions }}</td>
</tr>
@empty
<tr>
  <td colspan="5" class="text-gray-500">Sin medicamentos cargados.</td>
</tr>
@endforelse
</tbody>

        </table>
      </div>
    </div>

  <div class="w-56 sticky top-6">
  <div class="border rounded p-3 text-center">
    <div class="font-semibold mb-2">QR de esta receta</div>

    @php
      $qr = trim($qrRxSvg ?? '');
      $isSvgMarkup = \Illuminate\Support\Str::startsWith($qr, '<svg');
      $isDataUri   = \Illuminate\Support\Str::startsWith($qr, 'data:image');
    @endphp

    @if ($isSvgMarkup)
      {{-- Caso 1: el servicio devolvió SVG crudo --}}
      <div class="inline-block">{!! $qr !!}</div>
    @elseif ($isDataUri)
      {{-- Caso 2: el servicio devolvió data URI --}}
      <img src="{{ $qr }}" alt="QR de la receta" width="200" height="200" loading="lazy">
    @else
      {{-- Fallback (por si cambian la implementación del servicio) --}}
      <img src="data:image/svg+xml;base64,{{ base64_encode($qr) }}" alt="QR de la receta" width="200" height="200" loading="lazy">
    @endif

    <div class="text-xs mt-2 break-all">{{ $rxUrl }}</div>
  </div>
</div>

  </div>
</x-layouts.app>