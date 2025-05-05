<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register'); // Ensure this view exists
    }

    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'nom' => 'required|string|max:25',
            'prenom' => 'required|string|max:25',
            'telephone' => 'required|string|max:15|min:10|unique:utilisateurs',
            'email' => 'required|string|email|max:40|unique:utilisateurs',
            'password' => ['required', 'confirmed'], 
            'adresse' => 'nullable|string|max:255',
        ]);

        // Ensure the "citoyen" role exists
        if (!Role::where('name', 'citoyen')->exists()) {
            Role::create(['name' => 'citoyen']);
        }

        // Create the user
        $utilisateur = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a related citoyen record
        $utilisateur->citoyen()->create([
            'adresse' => $request->adresse ?? 'non renseignée',
            'date_naissance' => $request->date_naissance ?? null,
            'lieu_naissance' => $request->lieu_naissance ?? null,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
        ]);

        // Assign the "citoyen" role
        $utilisateur->assignRole('citoyen');

        // Log role assignment
        if ($utilisateur->hasRole('citoyen')) {
            Log::info('Rôle citoyen correctement assigné à l’utilisateur : ' . $utilisateur->id);
        } else {
            Log::error('Échec de l’assignation du rôle citoyen.');
        }

        // Trigger the registered event
        event(new Registered($utilisateur));

        // Log in the user
        Auth::login($utilisateur);

        // Redirect to the citoyen dashboard
        return redirect()->route('citoyen.dashboard');
    }
}
