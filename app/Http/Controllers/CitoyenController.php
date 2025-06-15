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
use Symfony\Component\HttpFoundation\StreamedResponse; // Import StreamedResponse if necessary for finer download control

class CitoyenController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SECTION TABLEAU DE BORD
    |--------------------------------------------------------------------------
    | Manages the display of the citizen's dashboard with statistics
    | and paginated lists of different document types.
    */

    /**
     * Displays the citizen's dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $citoyen = auth()->user();
        $this->authorizeCitoyenAccess($citoyen);

        // Paginated retrieval of documents by type
        // We will pass these variables directly to the view with the expected names
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
            // Pass each paginated collection with the variable name expected by the view
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
    | SECTION DOCUMENT MANAGEMENT (CRUD)
    |--------------------------------------------------------------------------
    | Manages CRUD operations for documents (requests).
    */

    /**
     * Displays the list of citizen document requests.
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
     * Displays the generic form for creating a new request.
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
     * Saves a new document request (original or duplicate) to the database.
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

            // Checks if a request of the same type is already pending
            if ($this->hasPendingRequest($type)) {
                return back()->with('error', "Vous avez déjà une demande de ce type en cours de traitement. Veuillez attendre son approbation ou son rejet avant de soumettre une nouvelle demande de ce type.")->withInput();
            }

            // Creates the document depending on whether it is a duplicate or a new request
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
     * Displays the details of a specific document.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View
     */
    public function show(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        return view('citoyen.demandes.show', [
            'demande' => $document,
            // canDownload is true if the document is paid AND if the PDF file physically exists
            'canDownload' => $document->is_paid && $document->pdf_path && Storage::disk('public')->exists($document->pdf_path)
        ]);
    }

    /**
     * Updates an existing document.
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
     * Deletes a document.
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
    | SECTION SPECIFIC FORMS
    |--------------------------------------------------------------------------
    | Methods to display specific request forms.
    */

    /**
     * Displays the birth certificate request form.
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
     * Displays the marriage certificate request form.
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
     * Displays the death certificate request form.
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
     * Displays the certificate of life request form.
     *
     * @return \Illuminate\View\View
     */
    public function formVie()
    {
        return view('citoyen.demandes.certifVie');
    }

    /**
     * Displays the certificate of support request form.
     *
     * @return \Illuminate\View\View
     */
    public function formEntretien()
    {
        return view('citoyen.demandes.certifEntretien');
    }

    /**
     * Displays the income certificate request form.
     *
     * @return \Illuminate\View\View
     */
    public function formRevenu()
    {
        return view('citoyen.demandes.certifRevenu');
    }

    /**
     * Displays the divorce certificate request form.
     *
     * @return \Illuminate\View\View
     */
    public function formDivorce()
    {
        return view('citoyen.demandes.certifDivorce');
    }

    /*
    |--------------------------------------------------------------------------
    | SECTION DOWNLOAD AND DUPLICATE
    |--------------------------------------------------------------------------
    | Manages document download and duplicate requests.
    */

    /**
     * Allows downloading a PDF document.
     * Generates the PDF on the fly if it doesn't exist.
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

        // Checks if the PDF is already generated and stored
        // If not, attempts to generate and store it
        if (!$document->pdf_path || !Storage::disk('public')->exists($document->pdf_path)) {
            Log::warning("Tentative de téléchargement d'un PDF non généré ou introuvable pour le document ID: {$document->id}. Tentative de génération à la volée.");
            try {
                $this->generateAndStorePdf($document);
            } catch (Exception $e) {
                Log::error("Échec de la régénération à la volée du PDF pour le document ID {$document->id}: " . $e->getMessage());
                return redirect()->back()->with('error', 'Le document PDF n\'a pas pu être trouvé ou généré. Veuillez contacter l\'administration.');
            }
        }

        // Marks the document as downloaded
        $document->update(['is_downloaded' => true]);

        // Returns the stored PDF file
        $fileName = 'acte_' . str_replace('-', '_', strtolower($document->type->value)) . '_' . $document->registry_number . '.pdf';
        return Storage::disk('public')->download($document->pdf_path, $fileName);
    }

    /**
     * Handles the duplicate request for a document.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestDuplicata(Request $request)
    {
        // This method is no longer the main method for processing duplicates
        // because it is handled by the 'store' method via the unified form.
        // However, if it is still called, ensure its validation.
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
    | SECTION PAYMENT MANAGEMENT
    |--------------------------------------------------------------------------
    | Manages the display of the payment form and payment processing.
    */

    /**
     * Displays the payment form for a document.
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
            // If already paid, redirect to the confirmation page with a clear message
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé et est prêt à être téléchargé.');
        }

        return view('citoyen.paiements.form', [
            'document' => $document,
            'montant' => $this->calculateAmount($document)
        ]);
    }

    /**
     * Processes document payment.
     * Generates and stores the PDF after successful payment.
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
            // If already paid, redirect to confirmation
            return redirect()->route('citoyen.paiements.confirmation', $document)
                ->with('info', 'Ce document a déjà été payé.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:card,mobile_money',
            'mobile_operator' => 'required_if:payment_method,mobile_money',
            'phone_number' => 'required_if:payment_method,mobile_money|numeric',
        ]);

        try {
            // Payment simulation
            $payment = Payment::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'amount' => $this->calculateAmount($document),
                'payment_method' => $validated['payment_method'],
                'transaction_id' => 'TXN-' . now()->timestamp . '-' . Str::random(8),
                'status' => 'completed'
            ]);

            $document->update(['is_paid' => true]);

            // PDF generation and storage
            try {
                $this->generateAndStorePdf($document);
                Log::info("PDF généré et stocké avec succès pour le document ID: {$document->id}");
            } catch (Exception $e) {
                Log::error("Erreur lors de la génération et du stockage du PDF après paiement pour le document ID {$document->id}: ".$e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                // Redirect to the confirmation page with a specific error message for PDF generation
                return redirect()->route('citoyen.paiements.confirmation', $document)
                    ->with('error', 'Paiement effectué, mais une erreur est survenue lors de la génération du document. Contactez le support.');
            }

            // Redirect to the confirmation page where the citizen can download the document
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
     * Displays the payment confirmation page.
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPaymentConfirmation(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Check if the document is paid
        if (!$document->is_paid) {
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Ce document n\'a pas été payé.');
        }

        // Check if the PDF has been generated and the path is valid
        if (!$document->pdf_path || !Storage::disk('public')->exists($document->pdf_path)) {
            Log::error("Fichier PDF manquant ou chemin invalide pour le document ID {$document->id}", [
                'path' => $document->pdf_path
            ]);
            return redirect()->route('citoyen.dashboard')
                ->with('error', 'Le fichier du document est introuvable ou n\'a pas été généré correctement. Veuillez contacter l\'administration.');
        }

        // Retrieve the associated payment
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
     * Downloads a document if payment has been made and the file generated.
     * This is the method you specifically asked not to delete.
     *
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadDocument(Document $document)
    {
        $this->authorizeDocumentOwner($document);

        // Checks if the document is paid and if the PDF path is registered
        if (!$document->is_paid || !$document->pdf_path) {
            abort(403, 'Accès non autorisé ou document non disponible. Le paiement est requis.');
        }

        // Checks if the file physically exists on the disk
        if (!Storage::disk('public')->exists($document->pdf_path)) {
            Log::error("Fichier PDF introuvable pour le document ID {$document->id} à l'adresse: {$document->pdf_path}");
            abort(404, 'Le fichier demandé n\'existe pas sur le serveur. Veuillez contacter l\'administration.');
        }

        // Updates the document's download status
        $document->update(['is_downloaded' => true]);

        // Builds the filename for download
        $fileName = "acte-{$document->type->value}-{$document->registry_number}.pdf";

        // Returns the file for forced download
        return Storage::disk('public')->download(
            $document->pdf_path,
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROTECTED METHODS (HELPERS)
    |--------------------------------------------------------------------------
    | Helper functions used internally by the controller.
    */

    /**
     * Returns a personalized greeting based on the time.
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
     * Calculates document statistics for a given user.
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
     * Retrieves all communes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getCommunes()
    {
        return Commune::all(['id', 'name']);
    }

    /**
     * Checks if a request of the same type is already pending for the user.
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
     * Validates request data for document creation or update.
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
            // 'terms' => 'required|accepted' // Old rule: always mandatory
        ];

        // Rule for 'terms': nullable if it's a duplicate, otherwise mandatory
        // if ($request->boolean('is_duplicata')) {
        //     $rules['terms'] = 'nullable|accepted'; // Not mandatory for duplicate
        // } else {
        //     $rules['terms'] = 'nullable|accepted'; // Mandatory for new request
        // }

        // Specific rules for duplicates
        if ($request->boolean('is_duplicata')) {
            $rules['registry_number'] = 'required|string|exists:documents,registry_number';
            $rules['idFront'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'; // idFront is now nullable for duplicata
            $rules['justificatif'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'; // Justificatif is nullable for duplicata
            $rules['commune_id'] = 'nullable|exists:communes,id'; // Commune ID is nullable for duplicata
        } else {
            // Rules for new requests
            $rules['registry_number'] = 'nullable|string';
            $rules['justificatif'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'; // Required for a new request
            $rules['commune_id'] = 'required|exists:communes,id'; // Commune ID is required for new requests

            // Apply metadata rules only for new requests
            $metadataRules = $this->getMetadataRules($request->input('type'));
            $rules = array_merge($rules, $metadataRules);
        }

        $validated = $request->validate($rules);

        // Generate registry number if it's not a duplicate and it's empty
        if (!$request->boolean('is_duplicata') && empty($validated['registry_number'])) {
            $validated['registry_number'] = $this->generateRegistryNumber($validated['type']);
        }

        return $validated;
    }

    /**
     * Creates a new original document.
     *
     * @param array $data Validated data.
     * @param \Illuminate\Http\Request $request HTTP request.
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
     * Creates a duplicate document from the request data.
     *
     * @param array $data The validated request data (must contain the original's 'registry_number').
     * @param \Illuminate\Http\Request $request The complete HTTP request.
     * @return \App\Models\Document
     * @throws \Exception
     */
    protected function createDuplicataDocument(array $data, Request $request): Document
    {
        // Retrieve the original document using the registry_number from $data
        $originalDocument = Document::where('registry_number', $data['registry_number'])->firstOrFail();
        // Ensure the user is the owner of the original
        $this->authorizeDocumentOwner($originalDocument);

        Log::debug('Starting createDuplicata for original document: ' . $originalDocument->registry_number);

        $existingDuplicatasCount = Document::where('original_document_id', $originalDocument->id)
                                          ->where('is_duplicata', true)
                                          ->count();

        $duplicataNumber = $existingDuplicatasCount + 1;

        $duplicata = $originalDocument->replicate();
        Log::debug('Original document replicated.');

        $justificatifPath = null;
        // If a new justificatif file is provided in the request for the duplicata
        if ($request->hasFile('justificatif')) {
            try {
                $justificatifPath = $request->file('justificatif')->store('justificatifs', 'public');
                Log::debug('Justificatif file stored successfully for duplicata: ' . $justificatifPath);
            } catch (\Exception $e) {
                Log::error('Failed to store justificatif file for duplicata: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                throw $e;
            }
        } else {
            // IMPORTANT CORRECTION: If no new justificatif is provided, inherit from the original document.
            // This addresses the 'justificatif_path' cannot be null error if the DB column is NOT NULL.
            $justificatifPath = $originalDocument->justificatif_path;
            Log::debug('No new justificatif file provided for duplicata. Inherited original justificatif_path: ' . ($justificatifPath ?? 'NULL'));
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
            // IMPORTANT CORRECTION: If no new ID Front is provided, inherit from the original document.
            // This addresses a potential 'id_front_path' cannot be null error if the DB column is NOT NULL.
            $idFrontPath = $originalDocument->id_front_path;
            Log::debug('No new ID Front file provided for duplicata. Inherited original id_front_path: ' . ($idFrontPath ?? 'NULL'));
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
            'justificatif_path' => $justificatifPath, // This will now correctly carry over the original's path or the newly uploaded one
            'id_front_path' => $idFrontPath, // This will now correctly carry over the original's path or the newly uploaded one
            'metadata' => $originalDocument->metadata, // Metadata copied from original
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Log::debug('Attempting to save duplicata to database.');
        $duplicata->save();
        Log::debug('Duplicata saved successfully: ' . $duplicata->registry_number);

        return $duplicata;
    }

    /**
     * Updates an existing document's data.
     *
     * @param \App\Models\Document $document The document to update.
     * @param array $data The validated data for the update.
     * @param \Illuminate\Http\Request|null $request The HTTP request (can be null if no file).
     * @return void
     */
    protected function updateDocument(Document $document, array $data, ?Request $request = null): void
    {
        // If it's not a duplicate, update the metadata
        if (!$document->is_duplicata && $request && $request->has('metadata')) {
            $data['metadata'] = $this->extractMetadataFromRequest($request, $document->type->value);
        }

        // Fill the document with the validated data
        $document->fill($data);

        // Handle file uploads if any
        if ($request && $request->hasFile('justificatif')) {
            // Delete old file if exists
            if ($document->justificatif_path) {
                Storage::disk('public')->delete($document->justificatif_path);
            }
            $document->justificatif_path = $request->file('justificatif')->store('justificatifs', 'public');
        }
        if ($request && $request->hasFile('idFront')) {
            // Delete old file if exists
            if ($document->id_front_path) {
                Storage::disk('public')->delete($document->id_front_path);
            }
            $document->id_front_path = $request->file('idFront')->store('id_fronts', 'public');
        }

        $document->save();
    }

    /**
     * Retrieves metadata validation rules based on document type.
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
                'metadata.sexe' => 'required|in:masculin,féminin',
                'metadata.nom_pere' => 'required|string|max:255',
                'metadata.nationalite_pere' => 'required|string|max:255',
                'metadata.nom_mere' => 'required|string|max:255',
                'metadata.nationalite_mere' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'mariage' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'required|string|max:255',
                'metadata.date_naissance_epoux' => 'required|date',
                'metadata.lieu_naissance_epoux' => 'required|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'required|string|max:255',
                'metadata.date_naissance_epouse' => 'required|date',
                'metadata.lieu_naissance_epouse' => 'required|string|max:255',
                'metadata.date_mariage' => 'required|date',
                'metadata.lieu_mariage' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'deces' => [
                'metadata.nom_defunt' => 'required|string|max:255',
                'metadata.prenom_defunt' => 'required|string|max:255',
                'metadata.date_deces' => 'required|date',
                'metadata.lieu_deces' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'certificat-vie' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.date_naissance_demandeur' => 'required|date',
                'metadata.lieu_naissance_demandeur' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'certificat-entretien' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.relation' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'certificat-revenu' => [
                'metadata.nom_demandeur' => 'required|string|max:255',
                'metadata.prenom_demandeur' => 'required|string|max:255',
                'metadata.montant_revenu' => 'required|numeric|min:0',
                'metadata.source_revenu' => 'required|string|max:255',
                'metadata.copies' => 'required|integer|min:1',
            ],
            'certificat-divorce' => [
                'metadata.nom_epoux' => 'required|string|max:255',
                'metadata.prenom_epoux' => 'required|string|max:255',
                'metadata.nom_epouse' => 'required|string|max:255',
                'metadata.prenom_epouse' => 'required|string|max:255',
                'metadata.date_divorce' => 'required|date',
                'metadata.copies' => 'required|integer|min:1',
            ],
            default => [],
        };
    }

    /**
     * Extracts and formats metadata from the request.
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @return array
     */
    protected function extractMetadataFromRequest(Request $request, string $type): array
    {
        $metadata = [];
        $metadataFields = match($type) {
            'naissance' => ['nom_enfant', 'prenom_enfant', 'date_naissance', 'lieu_naissance', 'sexe', 'nom_pere', 'nationalite_pere', 'nom_mere', 'nationalite_mere', 'copies'],
            'mariage' => ['nom_epoux', 'prenom_epoux', 'date_naissance_epoux', 'lieu_naissance_epoux', 'nom_epouse', 'prenom_epouse', 'date_naissance_epouse', 'lieu_naissance_epouse', 'date_mariage', 'lieu_mariage', 'copies'],
            'deces' => ['nom_defunt', 'prenom_defunt', 'date_deces', 'lieu_deces', 'copies'],
            'certificat-vie' => ['nom_demandeur', 'prenom_demandeur', 'date_naissance_demandeur', 'lieu_naissance_demandeur', 'copies'],
            'certificat-entretien' => ['nom_demandeur', 'prenom_demandeur', 'relation', 'copies'],
            'certificat-revenu' => ['nom_demandeur', 'prenom_demandeur', 'montant_revenu', 'source_revenu', 'copies'],
            'certificat-divorce' => ['nom_epoux', 'prenom_epoux', 'nom_epouse', 'prenom_epouse', 'date_divorce', 'copies'],
            default => [],
        };

        foreach ($metadataFields as $field) {
            // Uses input('metadata.field') to retrieve nested fields
            $metadata[$field] = $request->input("metadata.$field");
        }

        return $metadata;
    }

    /**
     * Generates a unique registry number based on the document type.
     * @param string $type
     * @return string
     */
    protected function generateRegistryNumber(string $type): string
    {
        $prefix = match($type) {
            'naissance' => 'REG-NAI',
            'mariage' => 'REG-MAR',
            'deces' => 'REG-DEC',
            'certificat-vie' => 'CERT-VIE',
            'certificat-entretien' => 'CERT-ENT',
            'certificat-revenu' => 'CERT-REV',
            'certificat-divorce' => 'CERT-DIV',
            default => 'REG',
        };
        return $prefix . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    /**
     * Generates and stores the PDF for a given document.
     *
     * @param \App\Models\Document $document
     * @return void
     * @throws \Exception
     */
    protected function generateAndStorePdf(Document $document): void
    {
        $data = [
            'document' => $document,
            'metadata' => $document->metadata // Ensure metadata is indeed an array/object here
        ];

        // Load the Blade view specific to the document type for the PDF
        $pdfView = 'pdfs.' . Str::kebab($document->type->value); // Ex: pdfs.naissance, pdfs.mariage

        if (!view()->exists($pdfView)) {
            Log::error("PDF view for document type {$document->type->value} not found: {$pdfView}");
            throw new Exception("PDF template not found for document type: {$document->type->value}");
        }

        $pdf = Pdf::loadView($pdfView, $data);

        $directory = 'documents_pdf';
        $fileName = 'document_' . $document->id . '_' . Str::random(10) . '.pdf';
        $path = $directory . '/' . $fileName;

        // Store the PDF
        Storage::disk('public')->put($path, $pdf->output());

        // Update the path in the database
        $document->pdf_path = $path;
        $document->save();

        Log::info("PDF generated and stored for document ID: {$document->id} at: {$path}");
    }

    /**
     * Authorizes a citizen's access to their own document.
     * @param \App\Models\User $citoyen
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeCitoyenAccess($citoyen): void
    {
        if (auth()->user()->id !== $citoyen->id) {
            abort(403, 'Accès non autorisé.');
        }
    }

    /**
     * Authorizes a user's access to the document if they are the owner.
     * @param \App\Models\Document $document
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeDocumentOwner(Document $document): void
    {
        if (auth()->id() !== $document->user_id) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce document.');
        }
    }

    /**
     * Deletes a document and its associated files.
     *
     * @param \App\Models\Document $document
     * @return void
     */
    protected function deleteDocument(Document $document): void
    {
        // Delete justificatif file if it exists
        if ($document->justificatif_path && Storage::disk('public')->exists($document->justificatif_path)) {
            Storage::disk('public')->delete($document->justificatif_path);
        }

        // Delete front ID file if it exists
        if ($document->id_front_path && Storage::disk('public')->exists($document->id_front_path)) {
            Storage::disk('public')->delete($document->id_front_path);
        }

        // Delete generated PDF file if it exists
        if ($document->pdf_path && Storage::disk('public')->exists($document->pdf_path)) {
            Storage::disk('public')->delete($document->pdf_path);
        }

        // Delete the database record
        $document->delete();
    }
}
