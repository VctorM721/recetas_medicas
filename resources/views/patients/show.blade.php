@php
  /** @var \App\Models\Patient $patient */
  /** @var \Illuminate\Support\Collection|\App\Models\Prescription[] $prescriptions */
  // Variables esperadas desde el controller:
  // $perfilUrl (string)   route('patients.show', $patient->uuid)
  // $qrPerfilSvg (string) markup <svg ...> generado por QrService::svg()
@endphp

<x-layouts.app :title="$patient->full_name">
  <div class="flex items-start justify-between gap-6">

    {{-- Columna izquierda: datos y recetas --}}
    <div class="flex-1">
      <h1 class="text-2xl font-bold mb-1">{{ $patient->full_name }}</h1>

      <div class="text-sm text-slate-600 mb-4">
        {{ $patient->dpi }}
        @if($patient->phone) · {{ $patient->phone }} @endif
        @if($patient->address) · {{ $patient->address }} @endif
      </div>

      @auth
        <a href="{{ route('prescriptions.create', $patient->uuid) }}"
           class="px-4 py-2 bg-emerald-600 text-white rounded">
          Nueva receta
        </a>
      @endauth

      <h2 class="mt-6 font-semibold">Recetas</h2>
      <div class="divide-y">
        @forelse($prescriptions as $rx)
          <a class="block py-2 hover:bg-slate-50" href="{{ route('prescriptions.show', $rx->uuid) }}">
            <div class="font-medium">RX {{ $rx->uuid }}</div>
            <div class="text-sm text-slate-600">
              {{ optional($rx->issued_at)->format('d/m/Y') }}
              —
              Dr. {{ optional($rx->doctor)->name ?? ($rx->doctor_name ?? '—') }}
            </div>
          </a>
        @empty
          <div class="text-slate-500 py-2">Sin recetas.</div>
        @endforelse
      </div>
    </div>

    <div class="w-56 sticky top-6">
  <div class="border rounded p-3 text-center">
    <div class="font-semibold mb-2">QR del Perfil</div>

    @php
      $qr = trim($qrPerfilRaw ?? '');
      $isSvgMarkup = \Illuminate\Support\Str::startsWith($qr, '<svg');
      $isDataUri   = \Illuminate\Support\Str::startsWith($qr, 'data:image');
    @endphp

    @if ($isSvgMarkup)
      {{-- Caso: SVG crudo --}}
      <div class="inline-block">{!! $qr !!}</div>
    @elseif ($isDataUri)
      
      <img src="{{ $qr }}" alt="QR del perfil" width="200" height="200" loading="lazy">
    @else
      
      <img src="data:image/svg+xml;base64,{{ base64_encode($qr) }}" alt="QR del perfil" width="200" height="200" loading="lazy">
    @endif

    <div class="text-xs mt-2 break-all">{{ $perfilUrl }}</div>
  </div>
</div>

  </div>
</x-layouts.app>