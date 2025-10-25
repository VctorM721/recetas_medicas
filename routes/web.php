<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\ImpersonationController;

Route::get('/', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('doctores.index');
    }
    return redirect()->route('patients.index');
});

// --- Área protegida (usuarios autenticados) ---
Route::middleware('auth')->group(function () {

    // Pacientes (index/create/store)
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');

    // Crear/guardar receta para un paciente (por UUID del paciente)
    // Protegido por Gate: doctor o admin
    Route::get('/patients/{patient:uuid}/prescriptions/create', [PrescriptionController::class, 'create'])
        ->middleware('can:crear-documentos')
        ->name('prescriptions.create');

    Route::post('/patients/{patient:uuid}/prescriptions', [PrescriptionController::class, 'store'])
        ->middleware('can:crear-documentos')
        ->name('prescriptions.store');

    // --- Gestión de Doctores (SOLO ADMIN) ---
    Route::middleware('can:admin-only')->group(function () {
        // CRUD completo (incluye show/perfil)
        Route::resource('doctores', AdminDoctorController::class);

        // Estadísticas rápidas
        Route::get('doctores/{doctor}/stats', [AdminDoctorController::class, 'stats'])
            ->name('doctores.stats');

        // Perfil extendido: clientes y facturas del doctor
        Route::get('doctores/{doctor}/clientes', [AdminDoctorController::class, 'clientes'])
            ->name('doctores.clientes');

        Route::get('doctores/{doctor}/facturas', [AdminDoctorController::class, 'facturas'])
            ->name('doctores.facturas');

        // Impersonación (entrar como doctor / volver a admin)
        Route::post('impersonate/{doctor}', [ImpersonationController::class, 'start'])
            ->name('impersonate.start');
        Route::post('impersonate/leave', [ImpersonationController::class, 'leave'])
            ->name('impersonate.leave');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

Route::get('/dashboard', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('doctores.index');
    }
    return redirect()->route('patients.index');
})->middleware('auth')->name('dashboard');
});

// --- Vistas públicas (ver paciente y ver receta por UUID) ---
Route::get('/patients/{patient:uuid}', [PatientController::class, 'show'])->name('patients.show');

// PDF y detalle públicos
Route::get('/p/{prescription:uuid}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
Route::get('/p/{prescription:uuid}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');

// Auth scaffolding (Laravel Breeze/Jetstream/Fortify, etc.)
require __DIR__ . '/auth.php';

// --- Ruta de diagnóstico (puedes borrarla cuando todo esté ok) ---
Route::get('/_debug_role', function () {
    $u = Auth::user();
    return response()->json([
        'logged_in'      => (bool) $u,
        'id'             => $u?->id,
        'email'          => $u?->email,
        'role'           => $u?->role,
        'can_crear_docs' => Gate::allows('crear-documentos'),
        'can_admin'      => Gate::allows('admin-only'),
    ]);
})->middleware('auth');