<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'RecetasQR' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
  <nav class="bg-slate-900 text-white px-6 py-3 flex justify-between">
    <a href="{{ route('patients.index') }}" class="font-semibold">RecetasQR</a>
    <div>
      @auth
        <span class="mr-3">{{ auth()->user()->name }} ({{ auth()->user()->role ?? 'user' }})</span>
        <form class="inline" method="POST" action="{{ route('logout') }}">@csrf<button class="underline">Salir</button></form>
      @endauth
      @guest <a class="underline" href="{{ route('login') }}">Ingresar</a> @endguest
    </div>
  </nav>
  <main class="max-w-5xl mx-auto p-6">
    @if(session('ok'))<div class="mb-4 p-3 bg-green-100 border border-green-300 rounded">{{ session('ok') }}</div>@endif
    {{ $slot }}
  </main>
</body>
</html>