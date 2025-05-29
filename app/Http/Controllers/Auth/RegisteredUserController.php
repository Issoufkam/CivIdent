<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:25',
            'prenom' => 'required|string|max:25',
            'telephone' => 'required|string|max:15|min:10|unique:users,telephone',
            'email' => 'required|string|email|max:40|unique:users,email',
            'password' => ['required', 'confirmed'],
            'adresse' => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string|max:100',
        ]);

        $utilisateur = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
        ]);

        // Événement Laravel
        event(new Registered($utilisateur));

        // Connexion automatique
        Auth::login($utilisateur);

        // Redirection selon le rôle
        return $this->redirectByRole($utilisateur);
    }

    /**
     * Redirige l'utilisateur selon son rôle.
     */
    protected function redirectByRole(User $user)
    {
        return match ($user->role) {
            'citoyen' => redirect()->route('citoyen.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect('/'),
        };
    }
}
