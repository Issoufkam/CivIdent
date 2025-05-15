<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\DocumentStatut;
use Illuminate\Support\Facades\Auth;

class DocumentRequestController extends Controller
{
    public function dashboard()
{
    $user = Auth::user();

    if ($user->role !== 'citoyen') {
        abort(403, 'Accès réservé aux citoyens.');
    }

    // Statistiques des demandes
    $stats = [
        'en_attente' => $user->documents()->where('status', DocumentStatut::EN_ATTENTE)->count(),
        'approuvees' => $user->documents()->where('status', DocumentStatut::ACTIF)->count(),
        'rejetees'   => $user->documents()->where('status', DocumentStatut::INACTIF)->count(),
    ];

    // Dernière demande
    $lastDemande = $user->documents()
        ->latest()
        ->first();

    // Groupement des documents par type
    $demandesGroupees = $user->documents()
        ->with('commune') // OK si tu as une relation 'commune' dans Document
        ->latest()
        ->get()
        ->mapToGroups(function ($document) {
            return [
                $this->getTypeLabel($document->type) => $document
            ];
        });

    return view('citoyen.dashboard', [
        'greeting'          => $this->getGreeting(),
        'stats'             => $stats,
        'lastDemande'       => $lastDemande,
        'demandesGroupees'  => $demandesGroupees,
    ]);
}


private function getTypeLabel($type)
{
    return match($type) {
        'naissance' => 'Actes de Naissance',
        'mariage' => 'Actes de Mariage',
        'deces' => 'Actes de Décès',
        'vie' => 'Certificats de Vie',
        'revenu' => 'Certificats de non Revenu',
        'entretien' => 'Certificats de Entretien',
        default => 'Autres Demandes',
    };
}

    public function create($type)
    {
        if (!in_array($type, ['naissance', 'mariage', 'deces', 'vie', 'revenu', 'entretien'])) {
            abort(404, 'Type de document non reconnu.');
        }

        $communes = Commune::all();
        return view("citoyen.demandes.create_{$type}", compact('communes', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:naissance,mariage,deces',
            'commune_id' => 'required|exists:communes,id',
            'registry_number' => 'required|string|max:50|unique:documents,registry_number',
            'registry_page' => 'nullable|integer',
            'registry_volume' => 'nullable|string|max:20',
            'metadata' => 'required|json',
            'justificatif' => 'required|file|mimes:pdf,jpg,png|max:2048',
            'attachments' => 'required|array|max:5',
            'attachments.*' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Justificatif
            $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');

            // Création du document
            $document = auth()->user()->documents()->create([
                'type' => $validated['type'],
                'commune_id' => $validated['commune_id'],
                'registry_number' => $validated['registry_number'],
                'registry_page' => $validated['registry_page'] ?? null,
                'registry_volume' => $validated['registry_volume'] ?? null,
                'metadata' => $validated['metadata'],
                'justificatif_path' => $justificatifPath,
            ]);

            // Pièces jointes
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $document->attachments()->create(['path' => $path]);
            }
        });

        return redirect()->route('citoyen.demandes.index')->with('success', 'Votre demande a été soumise avec succès.');
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('citoyen.demandes.show', compact('document'));
    }

    private function getGreeting()
    {
        $hour = now()->hour;
        return match (true) {
            $hour >= 5 && $hour < 12 => 'Bonjour',
            $hour >= 12 && $hour < 17 => 'Bon après-midi',
            $hour >= 17 && $hour < 21 => 'Bonsoir',
            default => 'Bonne nuit',
        };
    }
}
