<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1 { font-size: 16px; margin: 0 0 6px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #ddd; padding: 6px; }
    th { background: #f4f4f4; }
    .muted { color: #666; }
  </style>
</head>
<body>
  <h1>Receta #{{ $prescription->uuid }}</h1>
  <p class="muted">
    <b>Fecha:</b> {{ optional($prescription->issued_at)->format('d/m/Y') ?? $prescription->created_at->format('d/m/Y') }}
    · <b>Paciente:</b> {{ $prescription->patient->full_name }}
    · <b>Médico:</b> {{ optional($prescription->doctor)->name ?? ($prescription->doctor_name ?? '—') }}
  </p>

  @if($prescription->diagnosis)
    <p><b>Diagnóstico:</b> {{ $prescription->diagnosis }}</p>
  @endif

  @if($prescription->notes)
    <p><b>Notas:</b> {{ $prescription->notes }}</p>
  @endif

  <table>
    <thead>
      <tr>
        <th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Duración</th><th>Instrucciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($prescription->items as $it)
        <tr>
          <td>{{ $it->drug }}</td>
          <td>{{ $it->dose }}</td>
          <td>{{ $it->frequency }}</td>
          <td>{{ $it->duration }}</td>
          <td>{{ $it->instructions }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p style="margin-top: 14px;">
    <img src="{{ $qrDataUri }}" alt="QR" width="120" height="120"><br>
    <span class="muted">{{ $rxUrl }}</span>
  </p>
</body>
</html>