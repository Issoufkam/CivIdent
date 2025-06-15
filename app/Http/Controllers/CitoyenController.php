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
use App\Enums\DocumentType;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CitoyenController extends Controller
{
    // Répertoire où les signatures des agents sont stockées dans le disque public
    private const SIGNATURES_DIR = 'signatures';
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
        // L'appel à authorizeCitoyenAccess($citoyen) a été retiré car la méthode n'existe pas
        // et l'accès au tableau de bord de l'utilisateur connecté est généralement implicite.

        // Récupération paginée des documents par type
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
    | SECTION TÉLÉCHARGEMENT ET PRÉVISUALISATION
    |--------------------------------------------------------------------------
    | Gère le téléchargement des documents et l'affichage des pages de détails.
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
     * Affiche une vue HTML détaillée du document pour prévisualisation.
     * Cette méthode remplace l'ancienne logique de streaming PDF direct pour cette route.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function preview(Document $document) // Renommé et modifié pour retourner une vue HTML
    {
        $this->authorizeDocumentOwner($document);

        // Vérifie si le document est payé (si la prévisualisation HTML est conditionnée par le paiement)
        if (!$document->is_paid) {
            return redirect()->back()->with('error', 'Paiement requis pour prévisualiser ce document.');
        }

        // Préparer les données nécessaires à la vue HTML
        $data = [
            'document' => $document,
            'metadata' => $document->metadata,
            'citoyen' => $document->user,
            'agent' => $document->agent,
            'commune' => $document->commune,
            // Assurez-vous que la relation 'agent' est chargée sur le document
            // si elle n'est pas déjà chargée, ajoutez $document->load('agent'); ici.
            'agentSignaturePath' => $document->agent ? $this->getAgentSignaturePath($document->agent->id) : null,
            // Ajoutez d'autres données si vos templates HTML en ont besoin
        ];

        // Déterminez la vue Blade à utiliser en fonction du type de document
        // Les templates sont situés dans 'resources/views/templates/'
        $viewName = match ($document->type->value ?? $document->type) {
            'naissance' => 'templates.naissance',
            'mariage' => 'templates.mariage',
            'deces' => 'templates.deces',
            'certificat-vie' => 'templates.certificat_vie',
            'certificat-entretien' => 'templates.certificat_entretien',
            'certificat-revenu' => 'templates.certificat_revenu',
            'certificat-divorce' => 'templates.certificat_divorce',
            default => throw new \InvalidArgumentException('Type de document inconnu pour la prévisualisation HTML.'),
        };

        // Retourne la vue HTML spécifique au document
        return view($viewName, $data);
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
            // 'idFront' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
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
            'montant' => $this->calculateAmount($document) // Appel à la méthode de calcul du montant
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
                'amount' => $this->calculateAmount($document), // Montant calculé dynamiquement
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
            'type' => ['required', Rule::in(array_column(DocumentType::cases(), 'value'))], // Utilise l'Enum
            'is_duplicata' => 'required|boolean',
            'metadata.copies' => 'required|integer|min:1|max:5', // Toujours obligatoire pour tous les types de demande
        ];

        if ($request->boolean('is_duplicata')) {
            $rules['registry_number'] = 'required|string|exists:documents,registry_number';
            // 'metadata.purpose' est facultatif pour les duplicatas
            $rules['metadata.purpose'] = 'nullable|string';
            // Les champs 'justificatif', 'idFront' et les autres métadonnées de nouvelle demande
            // ne sont pas requis pour un duplicata. Ils sont omis des règles de validation ici.
            $rules['commune_id'] = 'nullable|exists:communes,id'; // La commune est héritée pour un duplicata
        } else {
            $rules['registry_number'] = 'nullable|string'; // Pour les nouvelles demandes, le numéro de registre est généré
            $rules['justificatif'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'; // Obligatoire pour nouvelle demande
            $rules['commune_id'] = 'required|exists:communes,id'; // Obligatoire pour nouvelle demande
            $rules['metadata.purpose'] = 'required|string'; // Obligatoire pour nouvelle demande

            $metadataRules = $this->getMetadataRules($request->input('type'));
            // Fusionne les règles spécifiques aux métadonnées pour les nouvelles demandes
            // en s'assurant de ne pas dupliquer les règles de 'copies' et 'purpose'
            foreach ($metadataRules as $key => $value) {
                if (!array_key_exists($key, $rules)) { // Ajoute la règle seulement si elle n'existe pas déjà
                    $rules[$key] = $value;
                }
            }
        }

        $validated = $request->validate($rules);

        // Si ce n'est pas un duplicata et que le numéro de registre est vide, le générer
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
        $originalDocument = Document::where('registry_number', $data['registry_number'])->firstOrFail();
        $this->authorizeDocumentOwner($originalDocument);

        Log::debug('Starting createDuplicata for original document: ' . $originalDocument->registry_number);

        $existingDuplicatasCount = Document::where('original_document_id', $originalDocument->id)
                                          ->where('is_duplicata', true)
                                          ->count();

        $duplicataNumber = $existingDuplicatasCount + 1;

        $duplicata = $originalDocument->replicate();
        Log::debug('Original document replicated.');

        // Pour un duplicata, le justificatif n'est pas obligatoire via le formulaire,
        // mais nous le conservons s'il a été hérité de l'original ou soumis (bien que non requis).
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
            $justificatifPath = $originalDocument->justificatif_path;
            Log::debug('No new justificatif file provided for duplicata. Inherited original justificatif_path: ' . ($justificatifPath ?? 'NULL'));
        }

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
            $idFrontPath = $originalDocument->id_front_path;
            Log::debug('No new ID Front file provided for duplicata. Inherited original id_front_path: ' . ($idFrontPath ?? 'NULL'));
        }

        // Si le formulaire inclut 'metadata.copies' et 'metadata.purpose' pour le duplicata,
        // nous les mettons à jour, sinon nous conservons celles de l'original.
        $metadata = $originalDocument->metadata;
        if ($request->has('metadata.copies')) {
            $metadata['copies'] = (int) $request->input('metadata.copies');
        }
        if ($request->has('metadata.purpose')) {
            $metadata['purpose'] = $request->input('metadata.purpose');
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
            'id_front_path' => $idFrontPath,
            'metadata' => $metadata, // Utilise la métadonnée potentiellement mise à jour
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
        // Si le document n'est PAS un duplicata et que des métadonnées sont envoyées, les extraire.
        // Les duplicatas héritent leurs métadonnées de l'original, sauf copies/purpose qui sont gérés en amont.
        if (!$document->is_duplicata && $request && $request->has('metadata')) {
            $document->metadata = $this->extractMetadataFromRequest($request, $document->type->value);
        }

        $document->fill($data); // Applique les autres champs validés (type, is_duplicata, registry_number, commune_id, etc.)

        // Gestion du fichier justificatif
        if ($request && $request->hasFile('justificatif')) {
            if ($document->justificatif_path) {
                Storage::disk('public')->delete($document->justificatif_path);
            }
            $document->justificatif_path = $request->file('justificatif')->store('justificatifs', 'public');
        }
        // Gestion du fichier idFront (si pertinent)
        if ($request && $request->hasFile('idFront')) {
            if ($document->id_front_path) {
                Storage::disk('public')->delete($document->id_front_path);
            }
            $document->id_front_path = $request->file('idFront')->store('id_fronts', 'public');
        }

        $document->save();
    }

    /**
     * Récupère les règles de validation des métadonnées en fonction du type de document.
     * Ces règles sont pour les NOUVELLES DEMANDES uniquement.
     * Les champs 'copies' et 'purpose' sont gérés globalement dans validateDocumentRequest.
     * @param string $type
     * @return array
     */
    protected function getMetadataRules(string $type): array
    {
        return match($type) {
            'naissance' => [
                'metadata.nom_enfant' => 'required|string|max:255',
                'metadata.prenom_enfant' => 'required|string|max:255',
                'metadata.date_naissance' => 'required|date',
                'metadata.lieu_naissance' => 'required|string|max:255',
                'metadata.sexe' => ['required', Rule::in(['M', 'F'])],
                'metadata.nom_pere' => 'required|string|max:255',
                'metadata.nationalite_pere' => 'required|string|max:255',
                'metadata.nom_mere' => 'required|string|max:255',
                'metadata.nationalite_mere' => 'required|string|max:255',
            ],
            'mariage' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'required|string|max:255',
                'metadata.nationalite_epoux' => 'required|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'required|string|max:255',
                'metadata.date_naissance_epouse' => 'required|date',
                'metadata.lieu_naissance_epouse' => 'required|string|max:255',
                'metadata.nom_mere_epouse' => 'required|string|max:255',
                'metadata.nationalite_epouse' => 'required|string|max:255',
                'metadata.date_mariage' => 'required|date',
                'metadata.lieu_mariage' => 'required|string|max:255',
            ],
            'deces' => [
                'metadata.nom_defunt' => 'required|string|max:255',
                'metadata.prenom_defunt' => 'required|string|max:255',
                'metadata.date_deces' => 'required|date',
                'metadata.lieu_deces' => 'required|string|max:255',
                'metadata.death_cause' => 'nullable|string|max:255', // Cause is optional
                'metadata.requester_name' => 'required|string|max:255',
                'metadata.requester_firstname' => 'required|string|max:255',
                'metadata.relationship' => 'required|string|max:255',
                'metadata.phone' => 'required|string|max:20', // Validation du format de téléphone si nécessaire ailleurs
            ],
            'certificat-vie' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.date_naissance_demandeur' => 'required|date',
                'metadata.lieu_naissance_demandeur' => 'required|string|max:255',
            ],
            'certificat-entretien' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.relation' => 'required|string|max:255',
            ],
            'certificat-revenu' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.montant_revenu' => 'required|numeric|min:0',
                'metadata.source_revenu' => 'required|string|max:255',
            ],
            'certificat-divorce' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'required|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'required|string|max:255',
                'metadata.date_divorce' => 'required|date',
            ],
            default => [],
        };
    }

    /**
     * Extrait les métadonnées de la requête en fonction du type de document.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return array
     */
    protected function extractMetadataFromRequest(Request $request, string $type): array
    {
        $metadata = $request->input('metadata', []);

        // Assurer que les champs 'copies' et 'purpose' sont toujours inclus dans les métadonnées
        // même s'ils ne sont pas listés dans getMetadataRules.
        $metadata['copies'] = (int) $request->input('metadata.copies', 1);
        $metadata['purpose'] = $request->input('metadata.purpose');

        // Filtrer les métadonnées pour ne garder que celles qui sont pertinentes pour le type de document
        // et qui ont été définies dans les règles de validation du contrôleur.
        // C'est important pour éviter de stocker des données non désirées dans le champ 'metadata'.
        $filteredMetadata = [];
        $allowedMetadataKeys = array_keys($this->getMetadataRules($type));

        // Ajoutons manuellement les clés qui sont toujours attendues dans le 'metadata'
        $allowedMetadataKeys[] = 'metadata.copies'; // Préférer 'copies' sans le préfixe 'metadata.' si c'est la clé finale
        $allowedMetadataKeys[] = 'metadata.purpose'; // Idem

        foreach ($metadata as $key => $value) {
            // Reconstruit la clé avec 'metadata.' pour la comparaison avec les règles
            $fullMetadataKey = 'metadata.' . $key;
            if (in_array($fullMetadataKey, $allowedMetadataKeys) || $key === 'copies' || $key === 'purpose') {
                $filteredMetadata[$key] = $value;
            }
        }
        return $filteredMetadata;
    }


    /**
     * Génère un numéro de registre unique pour un nouveau document.
     *
     * @param string $type
     * @return string
     */
    protected function generateRegistryNumber(string $type): string
    {
        $prefix = strtoupper(substr($type, 0, 3)); // Ex: NAI, MAR, DEC
        $datePart = now()->format('Ymd');
        $randomPart = Str::upper(Str::random(5)); // 5 caractères aléatoires

        $latestDocument = Document::where('type', $type)
            ->where('registry_number', 'like', "{$prefix}-{$datePart}-%")
            ->orderByDesc('registry_number')
            ->first();

        $sequence = 1;
        if ($latestDocument) {
            $parts = explode('-', $latestDocument->registry_number);
            if (count($parts) === 3) {
                // Tente d'extraire la partie numérique après le dernier tiret si elle existe
                $lastPart = end($parts);
                if (preg_match('/^(\d+)/', $lastPart, $matches)) { // Recherche une séquence numérique au début
                    $sequence = (int) $matches[1] + 1;
                }
            } else {
                 // Gère le format original comme REG-DEC-20230101-ABCDE.
                // Si l'ancien format est REG-TYPE-DATE-RANDOM, la séquence est réinitialisée par la date.
                // Sinon, il faut une logique plus robuste si la séquence est censée s'incrémenter sur le random.
                // Pour l'instant, on suppose une séquence liée au jour pour la simplicité.
            }
        }

        // Reconstruit le numéro de registre en intégrant la séquence numérique
        return "REG-{$prefix}-{$datePart}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }


    /**
     * Autorise l'accès si l'utilisateur connecté est le propriétaire du document.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeDocumentOwner(Document $document): void
    {
        if (auth()->id() !== $document->user_id) {
            abort(403, 'Accès non autorisé à ce document.');
        }
    }

    /**
     * Supprime le document et ses fichiers associés.
     *
     * @param \App\Models\Document $document
     * @return void
     */
    protected function deleteDocument(Document $document): void
    {
        DB::transaction(function () use ($document) {
            // Supprime les fichiers du stockage
            if ($document->justificatif_path && Storage::disk('public')->exists($document->justificatif_path)) {
                Storage::disk('public')->delete($document->justificatif_path);
            }
            if ($document->pdf_path && Storage::disk('public')->exists($document->pdf_path)) {
                Storage::disk('public')->delete($document->pdf_path);
            }
            if ($document->id_front_path && Storage::disk('public')->exists($document->id_front_path)) {
                Storage::disk('public')->delete($document->id_front_path);
            }

            // Supprime les pièces jointes associées (s'il y en a)
            foreach ($document->attachments as $attachment) {
                if ($attachment->path && Storage::disk('public')->exists($attachment->path)) {
                    Storage::disk('public')->delete($attachment->path);
                }
                $attachment->delete();
            }

            $document->delete();
        });
    }

    /**
     * Calcule le montant à payer pour un document.
     * Récupère le prix unitaire depuis la table `settings`.
     *
     * @param \App\Models\Document $document
     * @return float
     */
    protected function calculateAmount(Document $document): float
    {
        // Assurez-vous que le modèle Setting est bien importé: use App\Models\Setting;
        // Ou accédez-y via son chemin complet: \App\Models\Setting::get(...)
        $unitPrice = \App\Models\Setting::get('unit_price_' . $document->type->value, 0.00);

        // Assurez-vous que 'copies' est accessible via metadata
        $numberOfCopies = isset($document->metadata['copies']) && is_numeric($document->metadata['copies'])
                          ? (int)$document->metadata['copies']
                          : 1;

        $totalAmount = $unitPrice * $numberOfCopies;

        return $totalAmount;
    }

    /**
     * Génère le contenu PDF brut pour un document donné.
     *
     * @param \App\Models\Document $document
     * @return string Le contenu binaire du PDF.
     * @throws \Exception
     */
    protected function generatePdfOutput(Document $document): string
    {
        // Déterminez la vue Blade à utiliser pour le PDF en fonction du type de document
        // Le chemin a été ajusté pour pointer vers le répertoire 'templates'
        $viewName = 'templates.' . Str::slug($document->type->value, '_'); // Ex: templates.acte_naissance

        if (!view()->exists($viewName)) {
            Log::error("Vue PDF introuvable pour le type de document: {$viewName}");
            throw new Exception("Vue PDF non définie pour le type de document: " . $document->type->value);
        }

        // Passer les données nécessaires à la vue PDF
        $data = [
            'document' => $document,
            'metadata' => $document->metadata, // Accéder directement aux métadonnées
            'citoyen' => $document->user,
            'agent' => $document->agent,
            'commune' => $document->commune,
            'agentSignaturePath' => $document->agent ? $this->getAgentSignaturePath($document->agent->id) : null,
            // Ajoutez d'autres données si nécessaire
        ];

        // Générer le PDF
        $pdf = Pdf::loadView($viewName, $data);
        return $pdf->output(); // Retourne le contenu binaire du PDF
    }


    /**
     * Génère et stocke le PDF pour un document donné.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Exception
     */
    protected function generateAndStorePdf(Document $document): void
    {
        // Utilise la nouvelle méthode pour obtenir le contenu PDF
        $pdfOutput = $this->generatePdfOutput($document);

        // Définir le chemin de stockage
        $directory = 'pdfs/documents';
        $fileName = Str::slug($document->type->value, '_') . '_' . $document->registry_number . '.pdf';
        $path = $directory . '/' . $fileName;

        // Stocker le PDF
        Storage::disk('public')->put($path, $pdfOutput);

        // Mettre à jour le chemin du PDF dans la base de données
        $document->update(['pdf_path' => $path]);
    }

    /**
     * Récupère le chemin d'accès public à la signature d'un agent.
     * La signature est attendue sous la forme "{id_agent}.png" dans le répertoire "signatures".
     *
     * @param int $agentId L'ID de l'agent dont on veut la signature.
     * @return string|null Le chemin URL public de la signature, ou null si non trouvée.
     */
    private function getAgentSignaturePath(int $agentId): ?string
    {
        // Cherche la signature dans storage/app/public/signatures
        $path = self::SIGNATURES_DIR . '/' . $agentId . '.png';
        return Storage::disk('public')->exists($path) ? Storage::disk('public')->url($path) : null;
    }
}
