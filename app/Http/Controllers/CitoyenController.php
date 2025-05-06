<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Acte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function index()
    {
        $citoyen = Auth::user()->citoyen;

        if (!$citoyen) {
            abort(403, 'Accès non autorisé.');
        }

        $demandes = $citoyen->demandes()
            ->with('acte')
            ->latest()
            ->get()
            ->groupBy(function ($demande) {
                return $demande->acte->type ?? 'autre';
            });

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

    public function paiements()
    {
        $citoyen = Auth::user()->citoyen;

        if (!$citoyen) {
            abort(403, 'Accès non autorisé.');
        }

        $paiements = $citoyen->paiements()->latest()->get();

        return view('citoyen.paiements.index', compact('paiements'));
    }

    /**
     * Traite la demande d'extrait d'acte de naissance .
     */
    public function storeBirthRequest(Request $request)
    {
        $request->validate([
            'lastName'        => 'required|string|max:255',
            'firstName'       => 'required|string|max:255',
            'email'           => 'required|email',
            'phone'           => 'required|string|max:20',
            'dateOfBirth'     => 'required|date',
            'placeOfBirth'    => 'required|string|max:255',
            'subPrefecture'   => 'required|string|max:255',
            'gender'          => 'required|in:M,F',
            'fatherName'      => 'required|string|max:255',
            'motherName'      => 'required|string|max:255',
            'registerNumber'  => 'required|integer',
            'purpose'         => 'required|string',
            'copies'          => 'required|integer|min:1|max:5',
            'idFront'         => 'required|file|mimes:jpg,jpeg,png,pdf',
            'idBack'          => 'required|file|mimes:jpg,jpeg,png,pdf',
            'birthCopy'       => 'required|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $citoyen = Auth::user()->citoyen;

        if (!$citoyen) {
            abort(403, 'Accès non autorisé.');
        }

        // Stocker les fichiers
        $idFrontPath  = $request->file('idFront')->store('documents/ids', 'public');
        $idBackPath   = $request->file('idBack')->store('documents/ids', 'public');
        $birthCopyPath= $request->file('birthCopy')->store('documents/birth_copies', 'public');

        // Création de l’acte
        $acte = Acte::create([
            'type'             => 'naissance',
            'nom'              => $request->lastName,
            'prenoms'          => $request->firstName,
            'email'            => $request->email,
            'telephone'        => $request->phone,
            'date_naissance'   => $request->dateOfBirth,
            'lieu_naissance'   => $request->placeOfBirth,
            'sous_prefecture'  => $request->subPrefecture,
            'genre'            => $request->gender,
            'nom_pere'         => $request->fatherName,
            'nom_mere'         => $request->motherName,
            'numero_registre'  => $request->registerNumber,
            'motif_demande'    => $request->purpose,
            'nombre_copies'    => $request->copies,
            'fichier_id_recto' => $idFrontPath,
            'fichier_id_verso' => $idBackPath,
            'copie_extrait'    => $birthCopyPath,
        ]);

        // Création de la demande liée
        $demande = new Demande([
            'acte_id' => $acte->id,
            'moyen_retrait' => 'à préciser',
            'statut' => 'en_attente',
        ]);

        $citoyen->demandes()->save($demande);

        return response()->json(['message' => 'Demande soumise avec succès.'], 200);
    }

    /**
     * Affiche le formulaire de demande d'acte de naissance.
     */
    public function formNaissance()
    {
        return view('citoyen.demandes.acteNaissance');
    }

    /**
     * Affiche le formulaire de demande d'acte de mariage.
     */
    public function formMariage()
    {
        return view('citoyen.demandes.acteMariage');
    }
    /**
     * Affiche le formulaire de demande d'acte de décès.
     */
    public function formDeces()
    {
        return view('citoyen.demandes.acteDeces');
    }
    /**
     * Affiche le formulaire de demande d'acte de divorce.
     */
    public function formDivorce()
    {
        return view('citoyen.demandes.acteDivorce');
    }

    /**
     * Affiche le formulaire de demande d'acte de celibat.
     */
    public function formCelibat()
    {
        return view('citoyen.demandes.acteCelibat');
    }

    /**
     * Affiche le formulaire de demande d'acte de vie.
     */
    public function formVie()
    {
        return view('citoyen.demandes.acteVie');
    }

    /**
     * Affiche le formulaire de demande d'acte de résidence.
     */
    public function formResidence()
    {
        return view('citoyen.demandes.acteResidence');
    }
}
