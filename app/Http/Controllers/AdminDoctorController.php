<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminDoctorController extends Controller
{
  public function index() {
    $doctores = Doctor::with('user')->latest()->paginate(15);
    return view('admin.doctores.index', compact('doctores'));
  }

  public function create() { return view('admin.doctores.create'); }

  public function store(Request $request) {
    $data = $request->validate([
      'name' => ['required','string','max:100'],
      'email' => ['required','email','max:150', Rule::unique('users','email')],
      'password' => ['required','min:8'],
      'cmp' => ['nullable','string','max:50'],
      'especialidad' => ['nullable','string','max:100'],
    ]);

    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
      'role' => 'doctor',
    ]);

    Doctor::create([
      'user_id' => $user->id,
      'cmp' => $data['cmp'] ?? null,
      'especialidad' => $data['especialidad'] ?? null,
    ]);

    return redirect()->route('doctores.index')->with('ok','Doctor creado');
  }

  public function edit(Doctor $doctor) {
    $doctor->load('user');
    return view('admin.doctores.edit', compact('doctor'));
  }

  public function update(Request $request, Doctor $doctor) {
    $data = $request->validate([
      'name' => ['required','string','max:100'],
      'email' => ['required','email','max:150', Rule::unique('users','email')->ignore($doctor->user_id)],
      'password' => ['nullable','min:8'],
      'cmp' => ['nullable','string','max:50'],
      'especialidad' => ['nullable','string','max:100'],
    ]);

    $doctor->user->update([
      'name' => $data['name'],
      'email' => $data['email'],
      ...(!empty($data['password']) ? ['password' => Hash::make($data['password'])] : []),
    ]);

    $doctor->update(['cmp'=>$data['cmp'] ?? null, 'especialidad'=>$data['especialidad'] ?? null]);

    return back()->with('ok','Doctor actualizado');
  }

  public function destroy(Doctor $doctor) {
    // borrar doctor elimina el user por cascade? Si no, explícito:
    $doctor->user()->delete(); // cascada eliminará doctor
    return redirect()->route('doctores.index')->with('ok','Doctor eliminado');
  }

  public function stats(\App\Models\Doctor $doctor)
{
    $doctor->load('user');

    $totalPacientes = \App\Models\Patient::whereHas('prescriptions', function ($q) use ($doctor) {
        $q->where('doctor_id', $doctor->id);
    })->distinct('id')->count('id');

    $totalRecetas = \App\Models\Prescription::where('doctor_id', $doctor->id)->count();



    return view('admin.doctores.stats', compact('doctor', 'totalPacientes', 'totalRecetas',));
}
  public function show(Doctor $doctor)
{
    $doctor->load('user');

    $totalRecetas  = Prescription::where('doctor_id', $doctor->id)->count();
    $totalPacientes = Patient::whereHas('prescriptions', fn($q) =>
        $q->where('doctor_id', $doctor->id)
    )->distinct('id')->count('id');

    $totalFacturas = class_exists(\App\Models\Invoice::class)
        ? \App\Models\Invoice::where('doctor_id', $doctor->id)->count()
        : 0;

    return view('admin.doctores.show', compact('doctor','totalRecetas','totalPacientes','totalFacturas'));
}

public function clientes(Doctor $doctor)
{
    // Clientes que tienen al menos 1 receta hecha por este doctor
    $clientes = Patient::withCount(['prescriptions as recetas_del_doctor_count' => function($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        }])
        ->whereHas('prescriptions', fn($q) => $q->where('doctor_id', $doctor->id))
        ->orderByDesc('id')
        ->paginate(15);

    return view('admin.doctores.clientes', compact('doctor','clientes'));
}

public function facturas(Doctor $doctor)
{
    $facturas = collect();
    if (class_exists(\App\Models\Invoice::class)) {
        $facturas = \App\Models\Invoice::with(['patient'])
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->paginate(15);
    }
    return view('admin.doctores.facturas', compact('doctor','facturas'));
}
}