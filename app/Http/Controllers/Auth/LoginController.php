<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('telephone', 'password');

        $request->validate([
            'telephone' => 'required|string|min:10',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['telephone' => $request->telephone, 'password' => $request->password])) {
            $request->session()->regenerate();

            $role = Auth::user()->role ?? 'citoyen';

            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'agent' => redirect()->route('agent.dashboard'),
                'citoyen' => redirect()->route('citoyen.dashboard'),
                default => redirect('/'),
            };
        }

        return back()->withErrors([
            'telephone' => 'Numéro de téléphone ou mot de passe incorrect.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
