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
use Barryvdh\DomPDF\Facade\pdf;
use Exception;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse; // Ajouté pour le type de retour de downloadDocument

/**
 * @mixin \Illuminate\Contracts\Filesystem\Factory // Ajouté pour aider Intelephense à reconnaître les méthodes de Storage
 */
class CitoyenController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SECTION DASHBOARD ET VUES STATISTIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le tableau de bord du citoyen avec des statistiques et des demandes.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        $citoyen = auth()->user();
        $this->authorizeCitoyenAccess($citoyen); // Assure que l'utilisateur est bien un citoyen

        // Récupère les documents du citoyen avec pagination pour chaque type de document
        // Chaque paginator aura un nom de paramètre de requête personnalisé (ex: 'naissancePage')
        // pour éviter les conflits si plusieurs paginations sont sur la même page.
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


        // Logique existante pour le dernier document
        $latestDocument = Document::where('user_id', $citoyen->id)
            ->latest()
            ->first();

        return view('citoyen.dashboard', [
            'greeting' => $this->getGreeting(),
            'stats' => $this->getStats($citoyen),
            // Les variables 'demandes' et 'documents' groupées ne sont plus nécessaires si la pagination est par type
            // 'demandes' => $this->getGroupedRequests($citoyen),
            // 'documents' => $this->getGroupedDocuments($citoyen),
            'latestDocument' => $latestDocument,
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
    | SECTION GESTION DES DEMANDES ET DOCUMENTS
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche la liste des documents (demandes) du citoyen.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $communes = $this->getCommunes();
        $documents = Document::with(['commune', 'agent'])
            ->where('user_id', auth()->id())
            ->get();

        return view('citoyen.demandes.index', compact('communes', 'documents'));
    }

    /**
     * Enregistre une nouvelle demande de document.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Log::debug('Form submission data:', $request->all());

        $validatedData = $this->validateDocumentRequest($request);
        $document = $this->createDocument($validatedData, $request);

        return redirect()
            ->route('citoyen.dashboard')
            ->with('success', 'Votre demande a bien été enregistrée.');
    }

    /**
     * Affiche les détails d'un document spécifique.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Document $document)
    {
        $this->authorizeDocumentOwner($document); // Vérifie que le citoyen est propriétaire du document

        return view('citoyen.demandes.show', [
            'demande' => $document,
            'canDownload' => $document->is_paid && !$document->is_downloaded // Le téléchargement est possible si payé et pas déjà téléchargé
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

        $validatedData = $this->validateDocumentRequest($request, $document);
        $this->updateDocument($document, $validatedData, $request);

        return response()->json([
            'success' => true,
            'message' => 'Document mis à jour avec succès.',
            'document' => $document->fresh() // Retourne le document rafraîchi avec les dernières modifications
        ]);
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

    /**
     * Affiche le template du document pour impression (au lieu de télécharger un PDF).
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function download(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        if (!$document->is_paid) {
            return redirect()->back()->with('error', 'Paiement requis pour télécharger ce document.');
        }

        // Met à jour l'état du document comme "téléchargé" (ou "visualisé pour impression")
        // Ceci est important pour le suivi même si aucun fichier n'est physiquement téléchargé ici.
        $document->update(['is_downloaded' => true]);

        // Détermine la vue du template à afficher en fonction du type de document
        // Assurez-vous que ces vues (ex: 'certificats.naissance') existent et sont configurées pour l'impression.
        $viewName = 'certificats.' . str_replace('-', '_', strtolower($document->type->value));

        if (!view()->exists($viewName)) {
            Log::error("Template de certificat introuvable pour le type de document : {$document->type->value}. Vue attendue : {$viewName}");
            return redirect()->back()->with('error', 'Le modèle de document pour ce type n\'a pas été trouvé. Veuillez contacter l\'administration.');
        }

        // Retourne la vue pour que le citoyen puisse l'imprimer directement depuis le navigateur
        // Les données du document sont passées à la vue.
        return view($viewName, compact('document'));
    }


    /**
     * Demande un duplicata d'un document existant.
     * Le citoyen entre le registry_number de l'original.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestDuplicata(Request $request)
    {
        $request->validate([
            'registry_number' => 'required|string|exists:documents,registry_number',
        ]);

        $originalDocument = Document::where('registry_number', $request->registry_number)->first();

        // 1. Vérifier si le document trouvé est déjà un duplicata
        if ($originalDocument->is_duplicata) {
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Impossible de demander un duplicata à partir d\'un duplicata. Veuillez entrer le numéro de registre du document original.');
        }

        // 2. Vérifier si le document original est approuvé (un duplicata ne peut être demandé que pour un document approuvé)
        if ($originalDocument->status->value !== DocumentStatus::APPROUVEE->value) {
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Impossible de demander un duplicata pour un document non approuvé.');
        }

        // 3. Autorisation : Vérifier que le citoyen est propriétaire du document original
        $this->authorizeDocumentOwner($originalDocument);

        try {
            $duplicata = $this->createDuplicata($originalDocument);

            return redirect()->route('citoyen.dashboard')
                ->with('success', 'Votre demande de duplicata a bien été enregistrée (Numéro : ' . $duplicata->registry_number . '). Elle est en attente d\'approbation.');

        } catch (Exception $e) {
            Log::error("Erreur lors de la demande de duplicata pour le document {$request->registry_number}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la demande de duplicata. Veuillez réessayer.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION FORMULAIRES DE DEMANDE SPÉCIFIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le formulaire de demande d'acte de naissance.
     *
     * @return \Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\View\View
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
     * @return \Illuminate\Contracts\View\View
     */
    public function formVie()
    {
        return view('citoyen.demandes.certifVie');
    }

    /**
     * Affiche le formulaire de demande de certificat d'entretien.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function formEntretien()
    {
        return view('citoyen.demandes.certifEntretien');
    }

    /**
     * Affiche le formulaire de demande de certificat de revenu.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function formRevenu()
    {
        return view('citoyen.demandes.certifRevenu');
    }

    /**
     * Affiche le formulaire de demande de certificat de divorce.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function formDivorce()
    {
        return view('citoyen.demandes.certifDivorce');
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION GESTION DES PAIEMENTS
    |--------------------------------------------------------------------------
    */

    /**
     * Affiche le formulaire de paiement pour un document spécifique.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentForm(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Le document doit être approuvé pour être payé.
        if ($document->status->value !== DocumentStatus::APPROUVEE->value) {
            return redirect()->back()->with('error', 'Le document doit être approuvé avant paiement.');
        }

        // Si le document est déjà payé, redirige vers la confirmation.
        if ($document->is_paid) {
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé.');
        }

        return view('citoyen.paiements.form', [
            'document' => $document,
            'montant' => $this->calculateDocumentAmount($document) // Pass the document object
        ]);
    }

    /**
     * Traite le paiement d'un document.
     * Le fichier PDF est généré et enregistré après un paiement réussi.
     * en utilisant le template de vue correspondant au type de document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request, Document $document)
    {
        $this->authorizeDocumentOwner($document);

        if ($document->status->value !== DocumentStatus::APPROUVEE->value) {
            return redirect()->back()->with('error', 'Le document doit être approuvé avant paiement.');
        }

        if ($document->is_paid) {
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,mobile_money',
            'mobile_operator' => 'required_if:payment_method,mobile_money',
            'phone_number' => 'required_if:payment_method,mobile_money|numeric',
            // Si vous collectez des infos de carte directement, ajoutez-les ici (ex: 'card_number', 'expiry_date', 'cvv')
            // Cependant, il est fortement recommandé d'utiliser des solutions frontend (comme Stripe Elements)
            // pour tokeniser les informations de carte et n'envoyer qu'un token au backend.
        ]);

        try {
            $amount = $this->calculateDocumentAmount($document); // Pass the document object
            $transactionId = null; // Cet ID sera fourni par le PSP
            $paymentStatus = 'completed'; // SIMULATION : Défini à 'completed' pour simuler un succès

            // --- DÉBUT DE LA LOGIQUE D'INTÉGRATION DU FOURNISSEUR DE SERVICES DE PAIEMENT (PSP) ---

            // C'est ici que vous intégrerez le code spécifique à votre PSP choisi.
            // Actuellement, c'est une simulation.
            // Si vous intégrez un PSP réel, supprimez la ligne `$paymentStatus = 'completed';` ci-dessus
            // et décommentez/implémentez la logique réelle ici.

            // Exemple avec Stripe (nécessite le token de carte du frontend)
            // if ($validated['payment_method'] === 'card') {
            //     \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            //     $charge = \Stripe\Charge::create([
            //         'amount' => $amount * 100, // Montant en centimes
            //         'currency' => 'xof',
            //         'source' => $request->input('stripe_token'), // Token de carte du frontend
            //         'description' => 'Paiement document ' . $document->registry_number,
            //     ]);
            //     if ($charge->status === 'succeeded') {
            //         $paymentStatus = 'completed';
            //         $transactionId = $charge->id;
            //     } else {
            //         Log::error("Paiement carte échoué via Stripe: " . $charge->failure_message);
            //         $paymentStatus = 'failed';
            //     }
            // } elseif ($validated['payment_method'] === 'mobile_money') {
            //     // Logique pour le paiement Mobile Money (ex: Paystack, Flutterwave)
            //     // ...
            // }

            // --- FIN DE LA LOGIQUE D'INTÉGRATION DU FOURNISSEUR DE SERVICES DE PAIEMENT (PSP) ---

            // Le paiement est "completed" grâce à la simulation ci-dessus
            if ($paymentStatus === 'completed') {
                // Enregistrement du paiement dans la base de données
                $payment = Payment::create([
                    'document_id' => $document->id,
                    'user_id' => auth()->id(),
                    'amount' => $amount,
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $transactionId ?? 'TXN-' . now()->format('YmdHis') . '-' . uniqid(), // Utilise l'ID du PSP si disponible, sinon un ID simulé
                    'status' => 'completed',
                ]);

                $document->update(['is_paid' => true]);

                // *** RETIRÉ : Génération PDF et sauvegarde ici. Le PDF sera généré à la demande via la vue. ***
                // $viewName = 'templates.' . str_replace('-', '_', strtolower($document->type->value));
                // if (!view()->exists($viewName)) {
                //     Log::error("Template PDF introuvable pour le type de document : {$document->type->value}. Vue attendue : {$viewName}");
                //     throw new Exception("Le modèle de document pour ce type n'a pas été trouvé. Veuillez contacter l'administration.");
                // }
                // $pdfFileName = 'documents/acte-' . $document->registry_number . '.pdf';
                // $fullPdfPath = storage_path('app/public/' . $pdfFileName);
                // Storage::disk('public')->makeDirectory(dirname($pdfFileName));
                // $pdf = PDF::loadView($viewName, [
                //     'document' => $document,
                //     'metadata' => $document->metadata
                // ]);
                // $pdf->save($fullPdfPath);
                // $document->update(['pdf_path' => $pdfFileName]); // pdf_path sera null jusqu'à ce que l'agent le génère

                return redirect()->route('citoyen.paiements.confirmation', $document)
                    ->with('success', 'Paiement effectué avec succès!');
            } else {
                // Cette partie ne devrait normalement pas être atteinte avec la simulation actuelle.
                // Elle est utile en cas d'intégration PSP réelle où le paiement peut échouer.
                return back()->with('error', 'Le paiement n\'a pas pu être finalisé. Veuillez vérifier vos informations ou réessayer.');
            }

        } catch (Exception $e) { // Utilisation de la classe Exception importée
            Log::error("Erreur lors du traitement du paiement ou de la génération PDF: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['card_number', 'cvv']), // Éviter de logger les données sensibles
            ]);
            return back()->with('error', 'Une erreur inattendue est survenue lors de l\'opération. Veuillez réessayer ou contacter l\'administration.');
        }
    }

    /**
     * Affiche la page de confirmation de paiement.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Contracts\View\View
     */
    public function showPaymentConfirmation(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Vérifie si le document a été payé. Si non, redirige.
        if (!$document->is_paid) {
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Ce document n\'a pas été payé.');
        }

        // Récupérer le paiement associé au document.
        // On suppose qu'un document n'a qu'un seul paiement "completed" ou le plus récent.
        // Si un document peut avoir plusieurs paiements, vous devrez affiner cette logique.
        $payment = Payment::where('document_id', $document->id)
            ->where('status', 'completed') // Ou le statut final de votre paiement
            ->latest() // Pour le paiement le plus récent si plusieurs existent
            ->first();

        // Si aucun paiement n'est trouvé malgré is_paid=true (cas d'erreur rare)
        if (!$payment) {
            Log::error("Document ID {$document->id} est marqué comme payé, mais aucun enregistrement de paiement n'a été trouvé.");
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Impossible de trouver les détails du paiement pour ce document.');
        }

        // Redirection vers des vues différentes selon le type de document pour la confirmation
        switch ($document->type->value) {
            case 'naissance':
                return view('templates.naissance', compact('document', 'payment'));
            case 'mariage':
                return view('templates.mariage', compact('document', 'payment'));
            case 'deces':
                return view('templates.deces', compact('document', 'payment'));
            case 'certificat-vie':
                return view('templates.certificat_vie', compact('document', 'payment'));
            case 'certificat-entretien':
                return view('templates.certificat_entretien', compact('document', 'payment'));
            case 'certificat-revenu':
                return view('templates.certificat_revenu', compact('document', 'payment'));
            case 'certificat-divorce':
                return view('templates.certif_divorce', compact('document', 'payment'));
            default:
                // Vue par défaut si le type n'est pas spécifié ou n'a pas de template dédié
                return view('templates.default', compact('document', 'payment'));
        }
    }


    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PROTÉGÉES (HELPERS ET LOGIQUE INTERNE)
    |--------------------------------------------------------------------------
    */

    /**
     * Génère un message d'accueil personnalisé basé sur l'heure et le prénom de l'utilisateur.
     *
     * @return string
     */
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

    /**
     * Récupère les statistiques des documents du citoyen.
     *
     * @param \App\Models\User $citoyen
     * @return array
     */
    protected function getStats($citoyen): array
    {
        $documents = $citoyen->documents; // Assumant que $citoyen est une instance de App\Models\User

        return [
            'total' => $documents->count(),
            'en_attente' => $documents->where('status', DocumentStatus::EN_ATTENTE->value)->count(),
            'valide' => $documents->where('status', DocumentStatus::APPROUVEE->value)->count(),
            'rejete' => $documents->where('status', DocumentStatus::REJETEE->value)->count(),
        ];
    }

    /**
     * Récupère les demandes du citoyen regroupées par type.
     *
     * @param \App\Models\User $citoyen
     * @return \Illuminate\Support\Collection
     */
    protected function getGroupedRequests($citoyen)
    {
        return $citoyen->documents()
            ->with(['attachments', 'commune', 'agent'])
            ->latest()
            ->get()
            ->groupBy(fn($d) => $d->type->value);
    }

    /**
     * Récupère les documents du citoyen regroupés par type.
     * (Semble similaire à getGroupedRequests, à vérifier si la distinction est nécessaire)
     *
     * @param \App\Models\User $citoyen
     * @return \Illuminate\Support\Collection
     */
    protected function getGroupedDocuments($citoyen)
    {
        return $citoyen->documents()
            ->with(['commune', 'agent'])
            ->get()
            ->groupBy(fn($d) => $d->type->value);
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
     * Valide les données de la requête pour la création ou la mise à jour d'un document.
     *
     * @param \Illuminate\Http\Request $request
     * @param ?\App\Models\Document $document
     * @return array
     */
    protected function validateDocumentRequest(Request $request, ?Document $document = null): array
    {
        $isDuplicata = $request->input('is_duplicata');

        $rules = [
            'type' => ['required', Rule::in(['naissance', 'mariage', 'deces', 'certificat-vie', 'certificat-entretien', 'certificat-revenu', 'certificat-divorce'])],
            'commune_id' => $isDuplicata ? 'nullable' : 'required|exists:communes,id',
            'is_duplicata' => 'required|boolean',
            'registry_number' => [
                $isDuplicata ? 'required' : 'nullable', // Requis si duplicata, sinon auto-généré
                'string',
                // La règle unique ignore le document actuel lors d'une mise à jour
                $document ? Rule::unique('documents')->ignore($document->id) : 'unique:documents,registry_number'
            ],
            'registry_page' => 'nullable|integer',
            // Le justificatif est requis pour une nouvelle demande, facultatif pour mise à jour/duplicata
            'justificatif' => ($document || $isDuplicata) ? 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048' : 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'traitement_date' => 'nullable|date',
            'original_document_id' => 'nullable|exists:documents,id',
        ];

        $validated = $request->validate($rules);
        // Valide les règles spécifiques aux métadonnées (nom, prénom, date, etc.)
        $request->validate($this->getMetadataRules($validated['type']));

        // Génère le numéro d'enregistrement si ce n'est pas un duplicata et que ce n'est pas une mise à jour
        if (!$isDuplicata && !$document) {
            $validated['registry_number'] = $this->generateRegistryNumber($validated['type']);
        }

        return $validated;
    }

    /**
     * Crée un nouveau document dans la base de données.
     *
     * @param array $data
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Document
     */
    protected function createDocument(array $data, Request $request): Document
    {
        // Gère le téléchargement du justificatif
        $justificatifPath = null;
        if ($request->hasFile('justificatif')) {
            $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public'); // Stockage sur le disque 'public'
        }


        return Document::create([
            ...$data, // Utilise l'opérateur spread pour inclure les données validées
            'user_id' => auth()->id(),
            'metadata' => $request->input('metadata'), // Laravel gérera l'encodage JSON si 'metadata' est casté en 'array'
            'justificatif_path' => $justificatifPath,
            'is_paid' => false,
            'is_downloaded' => false,
            'status' => DocumentStatus::EN_ATTENTE, // Utilise l'Enum
            'agent_id' => null,
        ]);
    }

    /**
     * Met à jour un document existant.
     *
     * @param \App\Models\Document $document
     * @param array $data
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function updateDocument(Document $document, array $data, Request $request): void
    {
        // Gère la mise à jour des métadonnées
        if ($request->has('metadata')) {
            $data['metadata'] = $request->input('metadata'); // Laravel gérera l'encodage JSON
        }

        // Gère le remplacement du justificatif
        if ($request->hasFile('justificatif')) {
            // Supprime l'ancien justificatif si existant
            if ($document->justificatif_path) {
                Storage::disk('public')->delete($document->justificatif_path);
            }
            $data['justificatif_path'] = $request->file('justificatif')->store('justificatifs', 'public');
        }

        $document->update($data);
    }

    /**
     * Supprime un document et son justificatif associé.
     *
     * @param \App\Models\Document $document
     * @return void
     */
    protected function deleteDocument(Document $document): void
    {
        if ($document->justificatif_path) {
            Storage::disk('public')->delete($document->justificatif_path);
        }
        // Supprime le PDF généré si existant
        if ($document->pdf_path) {
            Storage::disk('public')->delete($document->pdf_path);
        }
        $document->delete();
    }

    /**
     * Crée un duplicata d'un document existant.
     *
     * @param \App\Models\Document $originalDocument
     * @return \App\Models\Document
     */
    protected function createDuplicata(Document $originalDocument): Document
    {
        // Compte le nombre de duplicatas existants pour ce document original
        $existingDuplicatasCount = Document::where('original_document_id', $originalDocument->id)
                                          ->where('is_duplicata', true)
                                          ->count();

        // Incrémente pour le nouveau duplicata
        $duplicataNumber = $existingDuplicatasCount + 1;

        // Utilise la méthode replicate() pour copier l'original
        $duplicata = $originalDocument->replicate();

        // Met à jour les champs spécifiques au duplicata
        $duplicata->is_duplicata = true;
        $duplicata->original_document_id = $originalDocument->id;
        // Génère le numéro de registre unique pour le duplicata
        $duplicata->registry_number = $originalDocument->registry_number . '-DUP-' . $duplicataNumber;
        $duplicata->status = DocumentStatus::EN_ATTENTE; // Un nouveau duplicata est en attente d'approbation
        $duplicata->pdf_path = null; // Le PDF doit être généré à nouveau après approbation
        $duplicata->decision_date = null; // Réinitialise la date de décision
        $duplicata->agent_id = null; // Réinitialise l'agent d'approbation
        $duplicata->is_paid = false; // Le duplicata doit être payé à nouveau
        $duplicata->is_downloaded = false; // Le duplicata n'est pas encore téléchargé

        // Met à jour les timestamps
        $duplicata->created_at = now();
        $duplicata->updated_at = now();

        $duplicata->save(); // Sauvegarde le nouveau duplicata dans la base de données

        return $duplicata;
    }

    /**
     * Génère un numéro d'enregistrement unique pour un document.
     *
     * @param string $type
     * @return string
     */
    protected function generateRegistryNumber(string $type): string
    {
        $typePrefix = strtoupper(substr($type, 0, 3));
        return 'REG-'.$typePrefix.'-'.now()->format('Ymd-His').'-'.strtoupper(substr(uniqid(), -4));
    }

    /**
     * Définit les règles de validation pour les métadonnées spécifiques à chaque type de document.
     *
     * @param string $type
     * @return array
     */
    protected function getMetadataRules(string $type): array
    {
        return match ($type) {
            'naissance' => [
                'metadata.nom_enfant' => 'required|string|max:255',
                'metadata.prenom_enfant' => 'required|string|max:255',
                'metadata.date_naissance' => 'required|date',
                'metadata.sexe' => 'required|string', // Utilisation de 'in' pour des valeurs spécifiques
                'metadata.lieu_naissance' => 'required|string|max:255',
                'metadata.nom_pere' => 'required|string|max:255',
                'metadata.nationalite_pere' => 'required|string|max:255',
                'metadata.nom_mere' => 'required|string|max:255',
                'metadata.nationalite_mere' => 'required|string|max:255',
            ],
            'mariage' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'nullable|string|max:255', // Ajouté si applicable
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'nullable|string|max:255', // Ajouté si applicable
                'metadata.date_mariage' => 'required|date',
                'metadata.lieu_mariage' => 'required|string|max:255',
            ],
            'deces' => [
                'metadata.nom_defunt' => 'required|string|max:255',
                'metadata.prenom_defunt' => 'required|string|max:255',
                'metadata.date_deces' => 'required|date',
                'metadata.lieu_deces' => 'required|string|max:255',
            ],
            'certificat-vie', 'certificat-entretien', 'certificat-revenu', 'certificat-divorce' => [
                // Règles communes ou spécifiques pour ces types de certificats
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.date_evenement' => 'nullable|date', // Ex: date de divorce
                'metadata.motif' => 'nullable|string|max:500', // Ex: motif du certificat
            ],
            default => [],
        };
    }

    /**
     * Calcule le montant à payer pour un document donné.
     * Le prix est calculé comme : (nombre_de_page * prix_unitaire) + 15% de (nombre_de_page * prix_unitaire).
     *
     * @param \App\Models\Document $document
     * @return float
     */
    protected function calculateDocumentAmount(Document $document): float
    {
        $pages = $document->registry_page ?? 1; // Utilise registry_page comme nombre de pages, par défaut 1 si null ou 0
        $unitPrice = 0.0;

        switch ($document->type->value) {
            case 'naissance':
                $unitPrice = 1000.00; // 1000 FCFA par page
                break;
            case 'mariage':
                $unitPrice = 2000.00; // 2000 FCFA par page
                break;
            case 'deces':
                $unitPrice = 1000.00; // 1000 FCFA par page
                break;
            case 'certificat-vie':
            case 'certificat-entretien':
            case 'certificat-revenu':
            case 'certificat-divorce':
                $unitPrice = 500.00; // 500 FCFA par page pour les certificats
                break;
            default:
                $unitPrice = 3000.00; // Montant par défaut si le type n'est pas reconnu (ou prix par défaut pour 1 page)
                break;
        }

        $basePrice = $pages * $unitPrice;
        $totalPrice = $basePrice * 1.15; // Ajoute 15% au prix de base

        return $totalPrice;
    }


    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATIONS ET AUTORISATIONS (MÉTHODES D'ASSISTANCE)
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifie si l'utilisateur authentifié a le rôle de 'citoyen'.
     *
     * @param \App\Models\User $user
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeCitoyenAccess($user): void
    {
        // Assumant que $user est une instance de App\Models\User
        if (!$user || $user->role !== 'citoyen') {
            abort(403, 'Accès interdit. Seuls les citoyens peuvent accéder à cette section.');
        }
    }

    /**
     * Vérifie si l'utilisateur authentifié est le propriétaire du document.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeDocumentOwner(Document $document): void
    {
        if (auth()->id() !== $document->user_id) {
            abort(403, 'Action non autorisée. Vous n\'êtes pas le propriétaire de ce document.');
        }
    }

    /**
     * Valide si un document peut être téléchargé.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateDocumentForDownload(Document $document): void
    {
        if (!$document->is_paid) {
            abort(403, 'Paiement requis pour télécharger ce document.');
        }

        if ($document->is_downloaded) {
            abort(403, 'Ce document a déjà été téléchargé. Veuillez faire une demande de duplicata.');
        }

        // Vérifie l'existence physique du fichier PDF
        // Cette vérification n'est plus nécessaire si le PDF n'est pas pré-généré au paiement.
        // if (!$document->pdf_path || !Storage::disk('public')->exists($document->pdf_path)) {
        //     abort(404, 'Le fichier du document est introuvable. Veuillez contacter l\'administration.');
        // }
    }

    /**
     * Valide si une demande de duplicata est autorisée pour un document donné.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function validateDocumentForDuplicata(Document $document): void
    {
        // Cette méthode est maintenant principalement pour des validations supplémentaires
        // si nécessaire, après les vérifications initiales dans requestDuplicata.
        // Les vérifications is_duplicata et status::APPROUVEE sont faites en amont.

        // Un duplicata n'est généralement demandé que si l'original a déjà été "utilisé" (téléchargé)
        // La logique a été déplacée pour permettre la demande de duplicata sans justificatif.
        // Si vous souhaitez réactiver cette vérification, décommentez la ligne ci-dessous.
        /*
        if (!$document->is_downloaded) {
            abort(403, 'Le document original n\'a pas encore été téléchargé ou utilisé pour demander un duplicata.');
        }
        */
    }


    public function downloadDocument(Document $document)
    {
        // 1. Autorisation : Vérifier si l'utilisateur connecté est le propriétaire du document.
        // Réutilisez la méthode authorizeDocumentOwner que nous avons déjà.
        $this->authorizeDocumentOwner($document);

        // 2. Vérifier si le document a été payé.
        // Il est crucial que le document soit payé pour être téléchargeable/visualisable.
        if (!$document->is_paid) {
            return redirect()->back()->with('error', 'Ce document n\'est pas disponible au téléchargement ou n\'est pas encore payé.');
        }

        // Met à jour l'état du document comme "téléchargé" (ou "visualisé pour impression")
        // Ceci est important pour le suivi même si aucun fichier n'est physiquement téléchargé ici.
        $document->update(['is_downloaded' => true]);

        // Détermine la vue du template à afficher en fonction du type de document
        // Assurez-vous que ces vues (ex: 'certificats.naissance') existent et sont configurées pour l'impression.
        $viewName = 'certificats.' . str_replace('-', '_', strtolower($document->type->value));

        if (!view()->exists($viewName)) {
            Log::error("Template de certificat introuvable pour le type de document : {$document->type->value}. Vue attendue : {$viewName}");
            return redirect()->back()->with('error', 'Le modèle de document pour ce type n\'a pas été trouvé. Veuillez contacter l\'administration.');
        }

        // Retourne la vue pour que le citoyen puisse l'imprimer directement depuis le navigateur
        // Les données du document sont passées à la vue.
        return view($viewName, compact('document'));
    }
}
