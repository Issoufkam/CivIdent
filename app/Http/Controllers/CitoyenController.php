<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Commune;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Enums\DocumentStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse; // Importez StreamedResponse si nécessaire pour un téléchargement plus fin

class CitoyenController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SECTION TABLEAU DE BORD
    |--------------------------------------------------------------------------
    | Gère l'affichage du tableau de bord du citoyen avec les statistiques
    | et les listes paginées des différents types de documents.
    */

    /**
     * Affiche le tableau de bord du citoyen.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $citoyen = auth()->user();
        $this->authorizeCitoyenAccess($citoyen);

        // Récupération paginée des documents par type
        // Nous allons passer ces variables directement à la vue avec les noms attendus
        $naissanceDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'naissance')
            ->latest()
            ->paginate(7, ['*'], 'naissancePage');

        $mariageDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'mariage')
            ->latest()
            ->paginate(7, ['*'], 'mariagePage');

        $decesDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'deces')
            ->latest()
            ->paginate(7, ['*'], 'decesPage');

        $certificatVieDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'certificat-vie')
            ->latest()
            ->paginate(7, ['*'], 'viePage');

        $certificatEntretienDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'certificat-entretien')
            ->latest()
            ->paginate(7, ['*'], 'entretienPage');

        $certificatRevenuDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'certificat-revenu')
            ->latest()
            ->paginate(7, ['*'], 'revenuPage');

        $certificatDivorceDocuments = Document::where('user_id', $citoyen->id)
            ->where('type', 'certificat-divorce')
            ->latest()
            ->paginate(7, ['*'], 'divorcePage');


        return view('citoyen.dashboard', [
            'greeting' => $this->getGreeting(),
            'stats' => $this->getStats($citoyen),
            'latestDocument' => Document::where('user_id', $citoyen->id)->latest()->first(),
            // Passez chaque collection paginée avec le nom de variable attendu par la vue
            'naissanceDocuments' => $naissanceDocuments,
            'mariageDocuments' => $mariageDocuments,
            'decesDocuments' => $decesDocuments,
            'certificatVieDocuments' => $certificatVieDocuments,
            'certificatEntretienDocuments' => $certificatEntretienDocuments,
            'certificatRevenuDocuments' => $certificatRevenuDocuments,
            'certificatDivorceDocuments' => $certificatDivorceDocuments,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION GESTION DES DOCUMENTS (CRUD)
    |--------------------------------------------------------------------------
    | Gère les opérations CRUD pour les documents (demandes).
    */

    /**
     * Affiche la liste des demandes de documents du citoyen.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $documents = Document::with(['commune', 'agent'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('citoyen.demandes.index', compact('documents'));
    }

    /**
     * Affiche le formulaire générique de création d'une nouvelle demande.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('citoyen.demandes.create_generic', [
            'communes' => $this->getCommunes()
        ]);
    }

    /**
     * Enregistre une nouvelle demande (originale ou duplicata) dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateDocumentRequest($request);
            $type = $validatedData['type'];
            $isDuplicata = (bool) $validatedData['is_duplicata'];

            // Vérifie si une demande du même type est déjà en attente
            if ($this->hasPendingRequest($type)) {
                return back()->with('error', "Vous avez déjà une demande de ce type en cours de traitement. Veuillez attendre son approbation ou son rejet avant de soumettre une nouvelle demande de ce type.")->withInput();
            }

            // Crée le document en fonction de s'il s'agit d'un duplicata ou d'une nouvelle demande
            $document = $isDuplicata
                ? $this->createDuplicataDocument($validatedData, $request)
                : $this->createNewDocument($validatedData, $request);

            return redirect()->route('citoyen.dashboard')
                ->with('success', $isDuplicata
                    ? 'Votre demande de duplicata a bien été enregistrée.'
                    : 'Votre demande a bien été enregistrée.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error("Erreur lors de l'enregistrement de la demande: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre demande. Veuillez réessayer.')->withInput();
        }
    }

    /**
     * Affiche les détails d'un document spécifique.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View
     */
    public function show(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        return view('citoyen.demandes.show', [
            'demande' => $document,
            // canDownload est vrai si le document est payé ET si le fichier PDF existe physiquement
            'canDownload' => $document->is_paid && $document->pdf_path && Storage::disk('public')->exists($document->pdf_path)
        ]);
    }

    /**
     * Met à jour un document existant.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Document $document)
    {
        $this->authorizeDocumentOwner($document);

        try {
            $validatedData = $this->validateDocumentRequest($request, $document);
            $this->updateDocument($document, $validatedData, $request);

            return response()->json([
                'success' => true,
                'message' => 'Document mis à jour avec succès.',
                'document' => $document->fresh()
            ]);

        } catch (Exception $e) {
            Log::error("Erreur mise à jour document: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'document_id' => $document->id,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprime un document.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Document $document)
    {
        $this->authorizeDocumentOwner($document);
        $this->deleteDocument($document);

        return response()->json([
            'success' => true,
            'message' => 'Document supprimé avec succès.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION FORMULAIRES SPÉCIFIQUES
    |--------------------------------------------------------------------------
    | Méthodes pour afficher les formulaires de demande spécifiques.
    */

    /**
     * Affiche le formulaire de demande d'acte de naissance.
     *
     * @return \Illuminate\View\View
     */
    public function formNaissance()
    {
        return view('citoyen.demandes.acteNaissance', [
            'communes' => $this->getCommunes()
        ]);
    }

    /**
     * Affiche le formulaire de demande d'acte de mariage.
     *
     * @return \Illuminate\View\View
     */
    public function formMariage()
    {
        return view('citoyen.demandes.acteMariage', [
            'communes' => $this->getCommunes()
        ]);
    }

    /**
     * Affiche le formulaire de demande d'acte de décès.
     *
     * @return \Illuminate\View\View
     */
    public function formDeces()
    {
        return view('citoyen.demandes.acteDeces', [
            'communes' => $this->getCommunes()
        ]);
    }

    /**
     * Affiche le formulaire de demande de certificat de vie.
     *
     * @return \Illuminate\View\View
     */
    public function formVie()
    {
        return view('citoyen.demandes.certifVie');
    }

    /**
     * Affiche le formulaire de demande de certificat d'entretien.
     *
     * @return \Illuminate\View\View
     */
    public function formEntretien()
    {
        return view('citoyen.demandes.certifEntretien');
    }

    /**
     * Affiche le formulaire de demande de certificat de revenu.
     *
     * @return \Illuminate\View\View
     */
    public function formRevenu()
    {
        return view('citoyen.demandes.certifRevenu');
    }

    /**
     * Affiche le formulaire de demande de certificat de divorce.
     *
     * @return \Illuminate\View\View
     */
    public function formDivorce()
    {
        return view('citoyen.demandes.certifDivorce');
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION TÉLÉCHARGEMENT ET DUPLICATA
    |--------------------------------------------------------------------------
    | Gère le téléchargement des documents et la demande de duplicata.
    */

    /**
     * Permet le téléchargement d'un document PDF.
     * Génère le PDF à la volée si non existant.
     *
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function download(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        if (!$document->is_paid) {
            return redirect()->back()->with('error', 'Paiement requis pour télécharger ce document.');
        }

        // Vérifie si le PDF est déjà généré et stocké
        // Si non, tente de le générer et de le stocker
        if (!$document->pdf_path || !Storage::disk('public')->exists($document->pdf_path)) {
            Log::warning("Tentative de téléchargement d'un PDF non généré ou introuvable pour le document ID: {$document->id}. Tentative de génération à la volée.");
            try {
                $this->generateAndStorePdf($document);
            } catch (Exception $e) {
                Log::error("Échec de la régénération à la volée du PDF pour le document ID {$document->id}: " . $e->getMessage());
                return redirect()->back()->with('error', 'Le document PDF n\'a pas pu être trouvé ou généré. Veuillez contacter l\'administration.');
            }
        }

        // Marquer le document comme téléchargé
        $document->update(['is_downloaded' => true]);

        // Retourne le fichier PDF stocké
        $fileName = 'acte_' . str_replace('-', '_', strtolower($document->type->value)) . '_' . $document->registry_number . '.pdf';
        return Storage::disk('public')->download($document->pdf_path, $fileName);
    }

    /**
     * Gère la demande de duplicata pour un document.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestDuplicata(Request $request)
    {
        // Cette méthode n'est plus la méthode principale de traitement des duplicatas
        // car elle est gérée par la méthode 'store' via le formulaire unifié.
        // Cependant, si elle est toujours appelée, assurez-vous de sa validation.
        $request->validate([
            'registry_number' => 'required|string|exists:documents,registry_number',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'idFront' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        try {
            $originalDocument = Document::where('registry_number', $request->registry_number)->firstOrFail();
            $this->authorizeDocumentOwner($originalDocument);

            if ($originalDocument->is_duplicata) {
                throw new Exception('Impossible de demander un duplicata à partir d\'un duplicata. Veuillez entrer le numéro de registre du document original.');
            }
            if ($originalDocument->status->value !== DocumentStatus::APPROUVEE->value) {
                throw new Exception('Impossible de demander un duplicata pour un document non approuvé.');
            }

            $duplicata = $this->createDuplicataDocument($request->all(), $request);

            return redirect()->route('citoyen.dashboard')
                ->with('success', 'Votre demande de duplicata a bien été enregistrée (Numéro : ' . $duplicata->registry_number . '). Elle est en attente d\'approbation.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error("Erreur lors de la demande de duplicata pour le document {$request->registry_number}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la demande de duplicata. ' . $e->getMessage())->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION GESTION DES PAIEMENTS
    |--------------------------------------------------------------------------
    | Gère l'affichage du formulaire de paiement et le traitement des paiements.
    */

    /**
     * Affiche le formulaire de paiement pour un document.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        if ($document->status !== DocumentStatus::APPROUVEE) {
            return redirect()->back()->with('error', 'Le document doit être approuvé avant paiement.');
        }

        if ($document->is_paid) {
            // Si déjà payé, rediriger vers la page de confirmation avec un message clair
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé et est prêt à être téléchargé.');
        }

        return view('citoyen.paiements.form', [
            'document' => $document,
            'montant' => $this->calculateAmount($document)
        ]);
    }

    /**
     * Traite le paiement d'un document.
     * Génère et stocke le PDF après un paiement réussi.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request, Document $document)
    {
        $this->authorizeDocumentOwner($document);

        if ($document->status !== DocumentStatus::APPROUVEE) {
            return redirect()->back()->with('error', 'Le document doit être approuvé avant paiement.');
        }

        if ($document->is_paid) {
            // Si déjà payé, rediriger vers la confirmation
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,mobile_money',
            'mobile_operator' => 'required_if:payment_method,mobile_money',
            'phone_number' => 'required_if:payment_method,mobile_money|numeric',
        ]);

        try {
            // Simulation de paiement
            $payment = Payment::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'amount' => $this->calculateAmount($document),
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN-' . now()->timestamp . '-' . Str::random(8),
                'status' => 'completed'
            ]);

            $document->update(['is_paid' => true]);

            // Génération et stockage du PDF
            try {
                $this->generateAndStorePdf($document);
                Log::info("PDF généré et stocké avec succès pour le document ID: {$document->id}");
            } catch (Exception $e) {
                Log::error("Erreur lors de la génération et du stockage du PDF après paiement pour le document ID {$document->id}: ".$e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                // Rediriger vers la page de confirmation avec un message d'erreur spécifique à la génération PDF
                return redirect()->route('citoyen.paiements.confirmation', $document)
                    ->with('error', 'Paiement effectué, mais une erreur est survenue lors de la génération du document. Contactez le support.');
            }

            // Rediriger vers la page de confirmation où le citoyen pourra télécharger le document
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('success', 'Paiement effectué avec succès et document généré ! Vous pouvez maintenant le télécharger.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error("Erreur lors du traitement du paiement: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['card_number', 'cvv']),
            ]);
            return back()->with('error', 'Une erreur inattendue est survenue lors de l\'opération de paiement. Veuillez réessayer ou contacter l\'administration.');
        }
    }

    /**
     * Affiche la page de confirmation de paiement.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentConfirmation(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Vérification que le document est payé
        if (!$document->is_paid) {
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Ce document n\'a pas été payé.');
        }

        // Vérification que le PDF a été généré et que le chemin est valide
        if (!$document->pdf_path || !Storage::disk('public')->exists($document->pdf_path)) {
            Log::error("Fichier PDF manquant ou chemin invalide pour le document ID {$document->id}", [
                'path' => $document->pdf_path
            ]);
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Le fichier du document est introuvable ou n\'a pas été généré correctement. Veuillez contacter l\'administration.');
        }

        // Récupération du paiement associé
        $payment = Payment::where('document_id', $document->id)
            ->where('status', 'completed')
            ->latest()
            ->firstOrFail();

        return view('citoyen.paiements.confirmation', [
            'document' => $document,
            'payment' => $payment,
            'download_available' => true
        ]);
    }

    /**
     * Télécharge un document si le paiement a été effectué et le fichier généré.
     * C'est la méthode que vous avez spécifiquement demandé de ne pas supprimer.
     *
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadDocument(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Vérifie si le document est payé et si le chemin du PDF est enregistré
        if (!$document->is_paid || !$document->pdf_path) {
            abort(403, 'Accès non autorisé ou document non disponible. Le paiement est requis.');
        }

        // Vérifie si le fichier existe physiquement sur le disque
        if (!Storage::disk('public')->exists($document->pdf_path)) {
            Log::error("Fichier PDF introuvable pour le document ID {$document->id} à l'adresse: {$document->pdf_path}");
            abort(404, 'Le fichier demandé n\'existe pas sur le serveur. Veuillez contacter l\'administration.');
        }

        // Met à jour le statut de téléchargement du document
        $document->update(['is_downloaded' => true]);

        // Construit le nom de fichier pour le téléchargement
        $fileName = "acte-{$document->type->value}-{$document->registry_number}.pdf";

        // Retourne le fichier pour le téléchargement forcé
        return Storage::disk('public')->download(
            $document->pdf_path,
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PROTÉGÉES (HELPERS)
    |--------------------------------------------------------------------------
    | Fonctions d'assistance utilisées en interne par le contrôleur.
    */

    /**
     * Retourne une salutation personnalisée basée sur l'heure.
     *
     * @return string
     */
    protected function getGreeting(): string
    {
        $hour = now()->hour;
        $name = auth()->user()->prenom ?? '';

        return match(true) {
            $hour < 12 => "Bonjour $name",
            $hour < 18 => "Bon après-midi $name",
            default => "Bonsoir $name"
        };
    }

    /**
     * Calcule les statistiques des documents pour un utilisateur donné.
     *
     * @param \App\Models\User $user
     * @return array
     */
    protected function getStats($user): array
    {
        $documents = $user->documents;

        return [
            'total' => $documents->count(),
            'en_attente' => $documents->where('status', DocumentStatus::EN_ATTENTE)->count(),
            'valide' => $documents->where('status', DocumentStatus::APPROUVEE)->count(),
            'rejete' => $documents->where('status', DocumentStatus::REJETEE)->count()
        ];
    }

    /**
     * Récupère toutes les communes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCommunes()
    {
        return Commune::all(['id', 'name']);
    }

    /**
     * Vérifie si une demande du même type est déjà en attente pour l'utilisateur.
     *
     * @param string $type
     * @return bool
     */
    protected function hasPendingRequest(string $type): bool
    {
        return Document::where('user_id', auth()->id())
            ->where('type', $type)
            ->where('status', DocumentStatus::EN_ATTENTE)
            ->exists();
    }

    /**
     * Valide les données de la requête pour la création ou la mise à jour d'un document.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document|null $document
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateDocumentRequest(Request $request, ?Document $document = null): array
    {
        $rules = [
            'type' => ['required', Rule::in([
                'naissance', 'mariage', 'deces',
                'certificat-vie', 'certificat-entretien',
                'certificat-revenu', 'certificat-divorce'
            ])],
            'is_duplicata' => 'required|boolean',
            // 'terms' => 'required|accepted' // Ancienne règle: toujours obligatoire
        ];

        // Règle pour 'terms': nullable si c'est un duplicata, sinon obligatoire
        // if ($request->boolean('is_duplicata')) {
        //     $rules['terms'] = 'nullable|accepted'; // Non obligatoire pour duplicata
        // } else {
        //     $rules['terms'] = 'nullable|accepted'; // Obligatoire pour nouvelle demande
        // }

        // Règles spécifiques aux duplicatas
        if ($request->boolean('is_duplicata')) {
            $rules['registry_number'] = 'required|string|exists:documents,registry_number';
            $rules['idFront'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'; // idFront is now nullable for duplicata
            $rules['justificatif'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'; // Justificatif is nullable for duplicata
            $rules['commune_id'] = 'nullable|exists:communes,id'; // Commune ID is nullable for duplicata
        } else {
            // Règles pour les nouvelles demandes
            $rules['registry_number'] = 'nullable|string';
            $rules['justificatif'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'; // Requis pour une nouvelle demande
            $rules['commune_id'] = 'required|exists:communes,id'; // Commune ID is required for new requests

            // Appliquer les règles de métadonnées uniquement pour les nouvelles demandes
            $metadataRules = $this->getMetadataRules($request->input('type'));
            $rules = array_merge($rules, $metadataRules);
        }

        $validated = $request->validate($rules);

        // Générer le numéro de registre si ce n'est pas un duplicata et qu'il est vide
        if (!$request->boolean('is_duplicata') && empty($validated['registry_number'])) {
            $validated['registry_number'] = $this->generateRegistryNumber($validated['type']);
        }

        return $validated;
    }

    /**
     * Crée un nouveau document original.
     *
     * @param array $data Données validées.
     * @param \Illuminate\Http\Request $request Requête HTTP.
     * @return \App\Models\Document
     */
    protected function createNewDocument(array $data, Request $request): Document
    {
        $justificatifPath = null;
        if ($request->hasFile('justificatif')) {
            $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
        }

        $metadata = $this->extractMetadataFromRequest($request, $data['type']);

        return Document::create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'commune_id' => $data['commune_id'],
            'registry_number' => $data['registry_number'],
            'justificatif_path' => $justificatifPath,
            'metadata' => $metadata,
            'status' => DocumentStatus::EN_ATTENTE,
            'is_paid' => false,
            'is_downloaded' => false,
            'pdf_path' => null,
        ]);
    }

    /**
     * Crée un document duplicata à partir des données de la requête.
     *
     * @param array $data Les données validées de la requête (doit contenir 'registry_number' de l'original).
     * @param \Illuminate\Http\Request $request La requête HTTP complète.
     * @return \App\Models\Document
     * @throws \Exception
     */
    protected function createDuplicataDocument(array $data, Request $request): Document
    {
        // Récupérer le document original en utilisant le registry_number de $data
        $originalDocument = Document::where('registry_number', $data['registry_number'])->firstOrFail();
        // Assurez-vous que l'utilisateur est bien le propriétaire de l'original
        $this->authorizeDocumentOwner($originalDocument);

        Log::debug('Starting createDuplicata for original document: ' . $originalDocument->registry_number);

        $existingDuplicatasCount = Document::where('original_document_id', $originalDocument->id)
                                          ->where('is_duplicata', true)
                                          ->count();

        $duplicataNumber = $existingDuplicatasCount + 1;

        $duplicata = $originalDocument->replicate();
        Log::debug('Original document replicated.');

        $justificatifPath = null;
        if ($request->hasFile('justificatif')) {
            try {
                $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
                Log::debug('Justificatif file stored successfully for duplicata: ' . $justificatifPath);
            } catch (\Exception $e) {
                Log::error('Failed to store justificatif file for duplicata: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                throw $e;
            }
        } else {
            Log::debug('No justificatif file provided for duplicata.');
        }

        // Store idFront if provided for duplicata
        $idFrontPath = null;
        if ($request->hasFile('idFront')) {
            try {
                $idFrontPath = $request->file('idFront')->store('id_fronts', 'public');
                Log::debug('ID Front file stored successfully for duplicata: ' . $idFrontPath);
            } catch (\Exception $e) {
                Log::error('Failed to store ID Front file for duplicata: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                throw $e;
            }
        } else {
            Log::debug('No ID Front file provided for duplicata.');
        }

        $duplicata->fill([
            'is_duplicata' => true,
            'original_document_id' => $originalDocument->id,
            'registry_number' => $originalDocument->registry_number . '-DUP-' . $duplicataNumber,
            'status' => DocumentStatus::EN_ATTENTE,
            'pdf_path' => null,
            'traitement_date' => null,
            'agent_id' => null,
            'is_paid' => false,
            'is_downloaded' => false,
            'justificatif_path' => $justificatifPath,
            'id_front_path' => $idFrontPath, // Save the path for idFront
            'metadata' => null, // Metadata should be null for duplicatas
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Log::debug('Attempting to save duplicata to database.');
        $duplicata->save();
        Log::debug('Duplicata saved successfully: ' . $duplicata->registry_number);

        return $duplicata;
    }

    /**
     * Met à jour les données d'un document existant.
     *
     * @param \App\Models\Document $document Le document à mettre à jour.
     * @param array $data Les données validées pour la mise à jour.
     * @param \Illuminate\Http\Request|null $request La requête HTTP (peut être null si pas de fichier).
     * @return void
     */
    protected function updateDocument(Document $document, array $data, ?Request $request = null): void
    {
        // Si ce n'est pas un duplicata, mettre à jour les métadonnées
        if (!$document->is_duplicata && $request && $request->has('metadata')) {
            $data['metadata'] = $this->extractMetadataFromRequest($request, $document->type->value);
        } else {
            // Si c'est un duplicata, s'assurer que les métadonnées ne sont pas mises à jour
            unset($data['metadata']);
        }

        if ($request && $request->hasFile('justificatif')) {
            if ($document->justificatif_path) {
                Storage::disk('public')->delete($document->justificatif_path);
            }
            $data['justificatif_path'] = $request->file('justificatif')->store('justificatifs', 'public');
        }

        // Handle idFront for duplicata updates if needed (though typically not updated after creation)
        if ($document->is_duplicata && $request && $request->hasFile('idFront')) {
            if ($document->id_front_path) {
                Storage::disk('public')->delete($document->id_front_path);
            }
            $data['id_front_path'] = $request->file('idFront')->store('id_fronts', 'public');
        }

        $document->update($data);
    }

    /**
     * Supprime un document et ses fichiers associés (justificatif, PDF).
     *
     * @param \App\Models\Document $document
     * @return void
     */
    protected function deleteDocument(Document $document): void
    {
        if ($document->justificatif_path) {
            Storage::disk('public')->delete($document->justificatif_path);
        }
        if ($document->pdf_path) {
            Storage::disk('public')->delete($document->pdf_path);
        }
        if ($document->id_front_path) { // Supprimer le fichier idFront si présent
            Storage::disk('public')->delete($document->id_front_path);
        }
        $document->delete();
    }

    /**
     * Génère un numéro de registre unique basé sur le type de document et la date/heure.
     *
     * @param string $type Le type de document.
     * @return string
     */
    protected function generateRegistryNumber(string $type): string
    {
        $typePrefix = strtoupper(substr($type, 0, 3));
        return 'REG-' . $typePrefix . '-' . now()->format('Ymd-His') . '-' . Str::random(4);
    }

    /**
     * Retourne les règles de validation des métadonnées spécifiques à chaque type de document.
     *
     * @param string $type Le type de document.
     * @return array
     */
    protected function getMetadataRules(string $type): array
    {
        return match ($type) {
            'naissance' => [
                'metadata.nom_enfant' => 'required|string|max:255',
                'metadata.prenom_enfant' => 'required|string|max:255',
                'metadata.date_naissance' => 'required|date',
                'metadata.sexe' => 'required|string',
                'metadata.lieu_naissance' => 'required|string|max:255',
                'metadata.nom_pere' => 'required|string|max:255',
                'metadata.nationalite_pere' => 'required|string|max:255',
                'metadata.nom_mere' => 'required|string|max:255',
                'metadata.nationalite_mere' => 'required|string|max:255',
            ],
            'mariage' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'nullable|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'nullable|string|max:255',
                'metadata.date_mariage' => 'required|date',
                'metadata.lieu_mariage' => 'required|string|max:255',
            ],
            'deces' => [
                'metadata.nom_defunt' => 'required|string|max:255',
                'metadata.prenom_defunt' => 'required|string|max:255',
                'metadata.date_deces' => 'required|date',
                'metadata.lieu_deces' => 'required|string|max:255',
                'metadata.death_cause' => 'nullable|string|max:255',
                'metadata.requester_name' => 'required|string|max:255',
                'metadata.requester_firstname' => 'required|string|max:255',
                'metadata.relationship' => 'required|string|max:255',
                'metadata.phone' => 'required|string|max:20',
                'metadata.copies' => 'required|integer|min:1',
                'metadata.purpose' => 'required|string|max:255',
            ],
            'certificat-vie', 'certificat-entretien', 'certificat-revenu', 'certificat-divorce' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.date_evenement' => 'nullable|date',
                'metadata.motif' => 'nullable|string|max:500',
            ],
            default => [],
        };
    }

    /**
     * Extrait les métadonnées spécifiques du type de document à partir de la requête.
     * Cette méthode aide à peupler le champ 'metadata' de manière contrôlée.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return array
     */
    protected function extractMetadataFromRequest(Request $request, string $type): array
    {
        $metadata = [];
        switch ($type) {
            case 'naissance':
                $metadata = $request->only([
                    'metadata.nom_enfant', 'metadata.prenom_enfant', 'metadata.date_naissance',
                    'metadata.sexe', 'metadata.lieu_naissance', 'metadata.nom_pere',
                    'metadata.nationalite_pere', 'metadata.nom_mere', 'metadata.nationalite_mere'
                ]);
                break;
            case 'mariage':
                $metadata = $request->only([
                    'metadata.nom_epoux', 'metadata.prenom_epoux', 'metadata.nom_epouse',
                    'metadata.prenom_epouse', 'metadata.date_mariage', 'metadata.lieu_mariage'
                ]);
                break;
            case 'deces':
                $metadata = $request->only([
                    'metadata.nom_defunt', 'metadata.prenom_defunt', 'metadata.date_deces',
                    'metadata.lieu_deces', 'metadata.death_cause', 'metadata.requester_name',
                    'metadata.requester_firstname', 'metadata.relationship', 'metadata.phone',
                    'metadata.copies', 'metadata.purpose'
                ]);
                break;
            case 'certificat-vie':
            case 'certificat-entretien':
            case 'certificat-revenu':
            case 'certificat-divorce':
                $metadata = $request->only([
                    'metadata.nom_demandeur', 'metadata.prenom_demandeur',
                    'metadata.date_evenement', 'metadata.motif'
                ]);
                break;
        }
        // Nettoyer les clés 'metadata.' (ex: 'metadata.nom_enfant' devient 'nom_enfant')
        return collect($metadata)->mapWithKeys(function ($value, $key) {
            return [Str::after($key, 'metadata.') => $value];
        })->toArray();
    }


    /**
     * Calcule le montant à payer pour un document.
     *
     * @param \App\Models\Document $document
     * @return float
     */
    protected function calculateAmount(Document $document): float
    {
        $pages = $document->registry_page ?? 1;
        $unitPrice = 0.0;

        switch ($document->type->value) {
            case 'naissance':
            case 'deces':
                $unitPrice = 1000.00;
                break;
            case 'mariage':
            case 'certificat-vie':
            case 'certificat-entretien':
            case 'certificat-revenu':
            case 'certificat-divorce':
                $unitPrice = 500.00;
                break;
            default:
                $unitPrice = 3000.00;
                break;
        }

        $basePrice = $pages * $unitPrice;
        $totalPrice = $basePrice * 1.15;

        return (float) number_format($totalPrice, 2, '.', '');
    }

    /**
     * Génère le PDF d'un document et le stocke dans le dossier public.
     * Met à jour le chemin du PDF dans la base de données (`pdf_path`).
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Exception Si le template PDF est introuvable.
     */
    protected function generateAndStorePdf(Document $document): void
    {
        $viewName = 'templates.' . str_replace('-', '_', strtolower($document->type->value));

        if (!view()->exists($viewName)) {
            Log::error("Template PDF introuvable pour le type de document : {$document->type->value}. Vue attendue : {$viewName}");
            throw new Exception("Le modèle de document pour ce type n'a pas été trouvé. Veuillez contacter l'administrateur.");
        }

        $pdfFileName = 'documents_pdfs/acte_' . str_replace('-', '_', strtolower($document->type->value)) . '_' . $document->registry_number . '.pdf';
        $fullPdfPath = Storage::disk('public')->path($pdfFileName);

        Storage::disk('public')->makeDirectory(dirname($pdfFileName), 0755, true, true);

        $pdf = Pdf::loadView($viewName, [
            'document' => $document,
            'metadata' => $document->metadata
        ]);

        $pdf->save($fullPdfPath);

        $document->update(['pdf_path' => $pdfFileName]);
        Log::info("PDF généré et stocké pour le document ID {$document->id} à : {$pdfFileName}");
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES D'AUTORISATION
    |--------------------------------------------------------------------------
    | Fonctions pour vérifier les autorisations d'accès.
    */

    /**
     * Vérifie si l'utilisateur connecté a le rôle 'citoyen'.
     *
     * @param mixed $user L'objet utilisateur authentifié.
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function authorizeCitoyenAccess($user): void
    {
        if (!$user || $user->role !== 'citoyen') {
            abort(403, 'Accès interdit. Seuls les citoyens peuvent accéder à cette section.');
        }
    }

    /**
     * Vérifie si l'utilisateur connecté est le propriétaire du document.
     *
     * @param \App\Models\Document $document Le document à vérifier.
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function authorizeDocumentOwner(Document $document): void
    {
        if (auth()->id() !== $document->user_id) {
            abort(403, 'Action non autorisée. Vous n\'êtes pas le propriétaire de ce document.');
        }
    }
}
