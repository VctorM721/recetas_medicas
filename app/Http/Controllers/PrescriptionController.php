<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use App\Services\QrService;
use App\Services\PdfService;
use App\Models\Doctor;
use Illuminate\Support\Str;

class PrescriptionController extends Controller
{
    public function __construct(){
        $this->middleware(['auth'])->only(['create','store']);
    }

    public function create(\App\Models\Patient $patient)
{
    $this->authorize('crear-documentos');

    // Si es admin, puede elegir qué doctor firmará la receta
    $doctores = auth()->user()->isAdmin()
        ? Doctor::with('user:id,name')->orderByDesc('id')->get()
        : collect(); // vacío para doctores

    return view('prescriptions.create', compact('patient','doctores'));
}

public function store(Request $request, \App\Models\Patient $patient)
{
    $this->authorize('crear-documentos');

    // Validación (admin debe elegir doctor_id que exista en doctors)
    $rules = [
        'diagnosis'   => 'nullable|string|max:255',
        'notes'       => 'nullable|string|max:500',
        'issued_at'   => 'nullable|date',
        'doctor_name' => 'nullable|string|max:255',
    ];
    if (auth()->user()->isAdmin()) {
        $rules['doctor_id'] = 'required|exists:doctors,id';
    }

    $data = $request->validate($rules);

    // Obtener el ID del DOCTOR (tabla doctors)
    $doctorId = auth()->user()->isDoctor()
        ? optional(auth()->user()->doctor)->id
        : ($data['doctor_id'] ?? null);

    // Si por algún motivo no tenemos doctor (edge case)
    if (!$doctorId) {
        return back()
            ->withErrors(['doctor_id' => 'Selecciona el doctor que firma la receta.'])
            ->withInput();
    }

    // Crear la receta
    $prescription = Prescription::create([
        'doctor_id'   => $doctorId,
        'patient_id'  => $patient->id,
        'diagnosis'   => $data['diagnosis'] ?? null,
        'notes'       => $data['notes'] ?? null,
        'doctor_name' => $data['doctor_name'] ?? auth()->user()->name,
        'issued_at'   => $data['issued_at'] ?? now(),
        'uuid'        => Str::uuid()->toString(),
    ]);

    return redirect()
        ->route('prescriptions.show', $prescription->uuid)
        ->with('ok', 'Receta creada correctamente');
}

public function show(\App\Models\Prescription $prescription, \App\Services\QrService $qr)
{
    $prescription->load(['items','patient','doctor']);

    $rxUrl   = route('prescriptions.show', $prescription->uuid);

    $qrRxSvg = $qr->svg($rxUrl, 200); 

    return view('prescriptions.show', compact('prescription','rxUrl','qrRxSvg'));
}

    public function pdf(Prescription $prescription, QrService $qr, PdfService $pdfs){
        $rxUrl    = route('prescriptions.show', $prescription->uuid);
        $qrRx120  = $qr->dataUri($rxUrl, 120);

        $bytes = $pdfs->renderView('prescriptions.pdf', [
            'prescription' => $prescription,
            'rxUrl'        => $rxUrl,
            'qrDataUri'    => $qrRx120,
        ]);

        return response($bytes, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="RX_'.$prescription->uuid.'.pdf"',
        ]);
    }
}