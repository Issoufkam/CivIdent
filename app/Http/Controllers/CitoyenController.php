<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;

class CitoyenController extends Controller
{
    /**
     * Affiche le tableau de bord du citoyen.
     */
    public function dashboard()
{
    $hour = now()->hour;

    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Bonjour';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Bon après-midi';
    } elseif ($hour >= 17 && $hour < 21) {
        $greeting = 'Bonsoir';
    } else {
        $greeting = 'Bonne nuit';
    }

    return view('citoyen.dashboard', compact('greeting'));
}


    /**
     * Affiche la liste des demandes du citoyen.
     */
     public function index()
     {
         $citoyen = Auth::user()->citoyen;

         if (!$citoyen) {
             abort(403, 'Accès non autorisé.');
         }

         $demandes = $citoyen->demandes()->latest()->get();

         return view('citoyen.demandes.index', compact('demandes'));
     }

    /**
     * Affiche le formulaire de création d'une nouvelle demande.
     */
    public function create()
    {
        return view('citoyen.demandes.create');
    }

    /**
     * Enregistre une nouvelle demande.
     */
    public function store(Request $request)
    {
        $request->validate([
            'acte_id' => 'required|exists:actes,id',
            'moyen_retrait' => 'required|string|max:255',
        ]);

        $citoyen = Auth::user()->citoyen;

        if (!$citoyen) {
            abort(403, 'Accès non autorisé.');
        }

        $demande = new Demande([
            'acte_id' => $request->acte_id,
            'moyen_retrait' => $request->moyen_retrait,
            'statut' => 'en_attente',
        ]);

        $citoyen->demandes()->save($demande);

        return redirect()->route('citoyen.demandes.index')
                         ->with('success', 'Demande créée avec succès.');
    }

    /**
     * Affiche les détails d'une demande spécifique.
     */
    public function show($id)
    {
        $demande = Demande::findOrFail($id);

        $this->authorize('view', $demande);

        return view('citoyen.demandes.show', compact('demande'));
    }

    /**
     * Affiche la liste des paiements du citoyen.
     */
    public function paiements()
    {
        $citoyen = Auth::user()->citoyen;

        if (!$citoyen) {
            abort(403, 'Accès non autorisé.');
        }

        $paiements = Paiement::whereHas('demande', function ($query) use ($citoyen) {
            $query->where('citoyen_id', $citoyen->id);
        })->latest()->get();

        return view('citoyen.paiements.index', compact('paiements'));
    }
}
