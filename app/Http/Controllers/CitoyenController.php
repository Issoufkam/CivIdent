<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CitoyenController extends Controller
{
    // ==================== DASHBOARD & STATISTIQUES ====================

    public function dashboard()
    {
        $latestDocument = Document::where('user_id', auth()->id())
            ->latest()
            ->first();
        $citoyen = auth()->user();
        $this->authorizeCitoyenAccess($citoyen);

        return view('citoyen.dashboard', [
            'greeting' => $this->getGreeting(),
            'stats' => $this->getStats($citoyen),
            'demandes' => $this->getGroupedRequests($citoyen),
            'documents' => $this->getGroupedDocuments($citoyen),
            'latestDocument' => $latestDocument,
        ]);
    }

    protected function getGreeting(): string
    {
        $hour = now()->hour;
        $prenom = auth()->user()->prenom ?? '';

        return match(true) {
            $hour < 12 => "Bonjour $prenom",
            $hour < 18 => "Bon après-midi $prenom",
            default => "Bonsoir $prenom"
        };
    }

    protected function getStats($citoyen): array
    {
        $documents = $citoyen->documents;

        return [
            'total' => $documents->count(),
            'en_attente' => $documents->where('status', 'en_attente')->count(),
            'valide' => $documents->where('status', 'valide')->count(),
            'rejete' => $documents->where('status', 'rejete')->count(),
        ];
    }

    protected function getGroupedRequests($citoyen)
    {
        return $citoyen->documents()
            ->with(['attachments', 'commune', 'agent'])
            ->latest()
            ->get()
            ->groupBy(fn($d) => $d->type->value);
    }

    protected function getGroupedDocuments($citoyen)
    {
        return $citoyen->documents()
            ->with(['commune', 'agent'])
            ->get()
            ->groupBy(fn($d) => $d->type->value);
    }

    // ==================== GESTION DES DOCUMENTS ====================

    public function index()
    {
        $documents = Document::with(['commune', 'agent'])
            ->where('user_id', auth()->id())
            ->get();

        return view('citoyen.demandes.index', compact('documents'));
    }


    public function store(Request $request)
    {
        Log::debug('Form submission data:', $request->all());

        $validatedData = $this->validateDocumentRequest($request);
        $document = $this->createDocument($validatedData, $request);

        return redirect()
            ->route('citoyen.dashboard')
            ->with('success', 'Votre demande a bien été enregistrée.');
    }

    public function show(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        return view('citoyen.demandes.show', ['demande' => $document]);
    }


public function update(Request $request, Document $document)
{
    $this->authorizeDocumentOwner($document);

    // Validation des données selon le type de document
    $validatedData = $this->validateDocumentRequest($request, $document);

    // Mise à jour du document avec les données validées
    $this->updateDocument($document, $validatedData, $request);

    // Retourne le document mis à jour en JSON
    return response()->json([
        'success' => true,
        'message' => 'Document mis à jour avec succès.',
        'document' => $document->fresh()
    ]);
}

public function destroy(Document $document)
{
    $this->authorizeDocumentOwner($document);

    $this->deleteDocument($document);

    return response()->json([
        'success' => true,
        'message' => 'Document supprimé avec succès.'
    ]);
}

public function download(Document $document)
{
    $this->authorizeDocumentOwner($document);
    $this->validateDocumentForDownload($document);

    $document->update(['is_downloaded' => true]);

    $filePath = storage_path("app/{$document->pdf_path}");

    if (!file_exists($filePath)) {
        return abort(404, "Le fichier demandé est introuvable.");
    }

    return response()->download($filePath);
}

public function requestDuplicata(Document $document)
{
    $this->authorizeDocumentOwner($document);
    $this->validateDocumentForDuplicata($document);

    $duplicata = $this->createDuplicata($document);

    return response()->json([
        'success' => true,
        'message' => 'Duplicata créé avec succès.',
        'duplicata' => $duplicata
    ]);
}

    // ==================== FORMULAIRES ====================

    public function formNaissance()
    {
        return view('citoyen.demandes.acteNaissance', [
            'communes' => $this->getCommunes()
        ]);
    }

    public function formMariage()
    {
        return view('citoyen.demandes.acteMariage', [
            'communes' => $this->getCommunes()
        ]);
    }

    public function formDeces()
    {
        return view('citoyen.demandes.acteDeces', [
            'communes' => $this->getCommunes()
        ]);
    }

    public function formVie()
    {
        return view('citoyen.demandes.certifVie');
    }

    public function formEntretien()
    {
        return view('citoyen.demandes.certifEntretien');
    }

    public function formRevenu()
    {
        return view('citoyen.demandes.certifRevenu');
    }

    public function formDivorce()
    {
        return view('citoyen.demandes.certifDivorce');
    }

    // ==================== METHODES PROTEGEES ====================

    protected function getCommunes()
    {
        return Commune::all(['id', 'name']);
    }

    protected function validateDocumentRequest(Request $request, ?Document $document = null): array
    {
        $isDuplicata = $request->input('is_duplicata');

        $rules = [
            'type' => ['required', Rule::in(['naissance', 'mariage', 'deces'])],
            'commune_id' => $isDuplicata ? 'nullable' : 'required|exists:communes,id',
            'is_duplicata' => 'required|boolean',
            'registry_number' => [
                $isDuplicata ? 'required' : 'nullable',
                'string',
                $document ? Rule::unique('documents')->ignore($document->id) : 'unique:documents,registry_number'
            ],
            'registry_page' => 'nullable|integer',
            'justificatif' => ($document || $isDuplicata) ? 'nullable' : 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'traitement_date' => 'nullable|date',
            'original_document_id' => 'nullable|exists:documents,id',
        ];

        $validated = $request->validate($rules);
        $request->validate($this->getMetadataRules($validated['type']));

        if (!$isDuplicata && !$document) {
            $validated['registry_number'] = $this->generateRegistryNumber($validated['type']);
        }

        return $validated;
    }

    protected function createDocument(array $data, Request $request): Document
    {
        $justificatifPath = $request->file('justificatif')->store('justificatifs');

        return Document::create([
            ...$data,
            'user_id' => auth()->id(),
            'metadata' => json_encode($request->input('metadata')),
            'justificatif_path' => $justificatifPath,
            'is_paid' => false,
            'is_downloaded' => false,
            'status' => 'en_attente',
            'agent_id' => null,
        ]);
    }

    protected function updateDocument(Document $document, array $data, Request $request): void
    {
        if ($request->has('metadata')) {
            $data['metadata'] = json_encode($request->input('metadata'));
        }

        if ($request->hasFile('justificatif')) {
            Storage::delete($document->justificatif_path);
            $data['justificatif_path'] = $request->file('justificatif')->store('justificatifs');
        }

        $document->update($data);
    }

    protected function deleteDocument(Document $document): void
    {
        Storage::delete($document->justificatif_path);
        $document->delete();
    }

    protected function createDuplicata(Document $document): Document
    {
        return Document::create([
            'type' => $document->type,
            'registry_number' => now()->format('YmdHis').'-'.$document->id,
            'metadata' => $document->metadata,
            'justificatif_path' => '',
            'user_id' => auth()->id(),
            'commune_id' => $document->commune_id,
            'is_duplicata' => true,
            'original_document_id' => $document->id,
            'status' => 'en_attente',
            'is_paid' => false,
            'is_downloaded' => false,
        ]);
    }

    protected function generateRegistryNumber(string $type): string
    {
        $typePrefix = strtoupper(substr($type, 0, 3));
        return 'REG-'.$typePrefix.'-'.now()->format('Ymd-His').'-'.strtoupper(substr(uniqid(), -4));
    }

    protected function getMetadataRules(string $type): array
    {
        return match ($type) {
            'naissance' => [
                'metadata.nom_enfant' => 'required|string|max:25',
                'metadata.prenom_enfant' => 'required|string|max:50',
                'metadata.date_naissance' => 'required|date',
                'metadata.sexe' => 'required|string|max:8',
                'metadata.lieu_naissance' => 'required|string|max:50',
                'metadata.nom_pere' => 'required|string|max:100',
                'metadata.nationalite_pere' => 'required|string|max:50',
                'metadata.nom_mere' => 'required|string|max:255',
                'metadata.nationalite_mere' => 'required|string|max:50',
            ],
            'mariage' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.date_mariage' => 'required|date',
                'metadata.lieu_mariage' => 'required|string|max:255',
            ],
            'deces' => [
                'metadata.nom_defunt' => 'required|string|max:25',
                'metadata.prenom_defunt' => 'required|string|max:50',
                'metadata.date_deces' => 'required|date',
                'metadata.lieu_deces' => 'required|string|max:255',
            ],
            default => [],
        };
    }

    // ==================== AUTHORISATIONS ====================

    protected function authorizeCitoyenAccess($user): void
    {
        if (!$user || $user->role !== 'citoyen') {
            abort(403, 'Accès interdit.');
        }
    }

    protected function authorizeDocumentOwner(Document $document): void
    {

        if ($document->user_id !== auth()->id()) {
            abort(403, 'Action non autorisée');
        }
    }

    protected function validateDocumentForDownload(Document $document): void
    {
        if (!$document->is_paid) {
            abort(403, 'Paiement requis pour télécharger ce document.');
        }

        if ($document->is_downloaded) {
            abort(403, 'Ce document a déjà été téléchargé. Veuillez faire une demande de duplicata.');
        }

        if (!Storage::exists($document->pdf_path)) {
            abort(404, 'Fichier introuvable.');
        }
    }

    protected function validateDocumentForDuplicata(Document $document): void
    {
        if ($document->is_duplicata) {
            abort(403, 'Impossible de demander un duplicata à partir d\'un duplicata.');
        }

        if (!$document->is_downloaded) {
            abort(403, 'Le document original n\'a pas encore été téléchargé.');
        }
    }
}
