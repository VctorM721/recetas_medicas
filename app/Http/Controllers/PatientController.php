<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient; 
use App\Services\QrService;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index()
{
    $q = \App\Models\Patient::query();

    if (auth()->user()->role === 'doctor') {
        $doctorId = auth()->user()->doctor->id ?? null;
        $q->whereHas('prescriptions', fn($x) => $x->where('doctor_id', $doctorId));
    }

    $patients = $q->latest()->paginate(15);
    return view('patients.index', compact('patients'));
}

public function create()
{
    // Solo muestra el formulario
    return view('patients.create');
}

    public function store(Request $r){
        $data = $r->validate([
            'full_name'=>'required|string|max:255',
            'dpi'=>'nullable|string|max:45',
            'birthdate'=>'nullable|date',
            'sex'=>'nullable|in:M,F,X',
            'phone'=>'nullable|string|max:45',
            'address'=>'nullable|string|max:255',
        ]);
        $patient = Patient::create($data);
        return redirect()->route('patients.show', $patient->uuid)->with('ok','Paciente creado');
    }

    public function show(\App\Models\Patient $patient, \App\Services\QrService $qr)
{
    $prescriptions = $patient->prescriptions()->latest()->get();
    $perfilUrl     = route('patients.show', $patient->uuid);

    $qrPerfilRaw = $qr->svg($perfilUrl, 200);

    return view('patients.show', compact('patient','prescriptions','perfilUrl','qrPerfilRaw'));
}
}