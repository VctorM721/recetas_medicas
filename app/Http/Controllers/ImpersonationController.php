<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Doctor $doctor)
    {
        // Guardamos el admin actual
        session(['admin_original_user_id' => Auth::id()]);

        // Cerramos la sesión actual y logueamos al doctor
        Auth::logout();
        Auth::loginUsingId($doctor->user_id);

        // Bandera para el banner
        session(['impersonating' => true]);

        // 👇 importantísimo: como doctor NO puedes ver /doctores, te mando a Pacientes
        return redirect()->route('patients.index')->with('ok', 'Ahora navegas como ' . Auth::user()->name);
    }

    public function leave()
    {
        $adminId = session()->pull('admin_original_user_id');

        Auth::logout();
        if ($adminId) {
            Auth::loginUsingId($adminId);
        }
        session()->forget('impersonating');

        // Ahora sí vuelve al módulo de doctores (solo admin)
        return redirect()->route('doctores.index')->with('ok', 'Has vuelto a tu sesión de administrador');
    }
}