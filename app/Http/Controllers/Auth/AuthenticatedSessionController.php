<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required','string','email'],
            'password' => ['required','string'],
        ]);

        if (! Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // 🔒 Limpia la URL "intended" para no volver a /patients si el usuario venía de ahí
        $request->session()->forget('url.intended');

        // 🚦 Redirige según el rol
        $user = $request->user();
        if ($user->role === 'admin') {
            return redirect()->route('doctores.index');
        }

        // doctor (u otro rol)
        return redirect()->route('patients.index');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}