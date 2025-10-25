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
use Illuminate\Support\Facades\DB; 

class PrescriptionController extends Controller
{
    public function __construct(){
        $this->middleware(['auth'])->only(['create','store']);
    }

    public function create(\App\Models\Patient $patient)
{
    $this->authorize('crear-documentos');

   
    $doctores = auth()->user()->isAdmin()
        ? Doctor::with('user:id,name')->orderByDesc('id')->get()
        : collect(); 

    return view('prescriptions.create', compact('patient','doctores'));
}

public function store(Request $request, \App\Models\Patient $patient)
{
    $this->authorize('crear-documentos');

    
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

    
    $doctorId = auth()->user()->isDoctor()
        ? optional(auth()->user()->doctor)->id
        : ($data['doctor_id'] ?? null);

    if (!$doctorId) {
        return back()
            ->withErrors(['doctor_id' => 'Selecciona el doctor que firma la receta.'])
            ->withInput();
    }

    
    $items = [];

// Formato A: items[0][campo]
if (is_array($request->input('items'))) {
    foreach ($request->input('items') as $row) {
        if (empty($row)) continue;
        $items[] = [
            // Mapea a 'drug' desde medicine/medicamento/drug
            'drug'         => trim($row['drug'] ?? $row['medicine'] ?? $row['medicamento'] ?? ''),
            'dose'         => trim($row['dose'] ?? $row['dosis'] ?? ''),
            'frequency'    => trim($row['frequency'] ?? $row['frecuencia'] ?? ''),
            'duration'     => trim($row['duration'] ?? $row['duracion'] ?? ''),
            'instructions' => trim($row['instructions'] ?? $row['instrucciones'] ?? ''),
        ];
    }
}

// Formato B: arreglos paralelos
if (empty($items)) {
    $drugs = $request->input('drug',
              $request->input('medicine',
              $request->input('medicamento', [])));
    $doses = $request->input('dose',        $request->input('dosis', []));
    $freqs = $request->input('frequency',   $request->input('frecuencia', []));
    $durs  = $request->input('duration',    $request->input('duracion', []));
    $insts = $request->input('instructions',$request->input('instrucciones', []));

    $n = max(count($drugs), count($doses), count($freqs), count($durs), count($insts));
    for ($i = 0; $i < $n; $i++) {
        $items[] = [
            'drug'         => trim($drugs[$i] ?? ''),
            'dose'         => trim($doses[$i] ?? ''),
            'frequency'    => trim($freqs[$i] ?? ''),
            'duration'     => trim($durs[$i] ?? ''),
            'instructions' => trim($insts[$i] ?? ''),
        ];
    }
}

 $items = array_values(array_filter($items, fn($r) =>
    implode('', array_map('strval', $r)) !== ''
));

// ---------- Guardado ----------
$prescription = \Illuminate\Support\Facades\DB::transaction(function () use ($patient, $data, $doctorId, $items) {
    $prescription = \App\Models\Prescription::create([
        'doctor_id'   => $doctorId,
        'patient_id'  => $patient->id,
        'diagnosis'   => $data['diagnosis'] ?? null,
        'notes'       => $data['notes'] ?? null,
        'doctor_name' => $data['doctor_name'] ?? auth()->user()->name,
        'issued_at'   => $data['issued_at'] ?? now(),
        'uuid'        => \Illuminate\Support\Str::uuid()->toString(),
    ]);

    if (!empty($items)) {
        $prescription->items()->createMany($items); // usa 'drug', etc.
    }
    return $prescription;
});

    return redirect()
        ->route('prescriptions.show', $prescription->uuid)
        ->with('success', 'Receta médica creada correctamente.');
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