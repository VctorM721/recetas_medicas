<x-layouts.app :title="'Pacientes'">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Pacientes</h1>
    <a href="{{ route('patients.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded">Nuevo</a>
  </div>

  @if($patients->count() === 0)
    <div class="text-slate-600">Aún no hay pacientes. Cree el primero con el botón <b>Nuevo</b>.</div>
  @else
    <div class="grid md:grid-cols-2 gap-4">
      @foreach($patients as $p)
      <a class="block border rounded p-4 hover:bg-slate-100" href="{{ route('patients.show',$p->uuid) }}">
        <div class="font-semibold">{{ $p->full_name }}</div>
        <div class="text-sm text-slate-600">{{ $p->dpi }} · {{ $p->phone }}</div>
      </a>
      @endforeach
    </div>
    <div class="mt-4">{{ $patients->links() }}</div>
  @endif
</x-layouts.app>