<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Acte;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Enums\ActeType;
use App\Enums\DemandeStatut;
use App\Services\FileUploadService;
use App\Services\PDFService;

class CitoyenController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    // ========================
    // Tableau de bord
    // ========================
    public function dashboard()
    {
        $citoyen = $this->getCitoyenOrFail();

        return view('citoyen.dashboard', [
            'greeting' => $this->getGreeting(),
            'stats' => $this->getStats($citoyen),
            'demandes' => $this->getGroupedDemandes($citoyen)
        ]);
    }

    // ========================
    // Gestion des demandes
    // ========================
    public function index()
    {
        return view('citoyen.demandes.index', [
            'demandes' => $this->getGroupedDemandes($this->getCitoyenOrFail())
        ]);
    }

    public function show(Demande $demande)
    {
        $this->authorize('view', $demande);

        return view('citoyen.demandes.show', [
            'demande' => $demande->load(['acte', 'paiement']),
            'qrCode' => PDFService::generateQRCode($demande->reference)
        ]);
    }

    // ========================
    // Création de demandes
    // ========================
    public function storeBirthRequest(Request $request)
    {
        $validated = $this->validateBirthRequest($request);
        return $this->processActeCreation($request, ActeType::NAISSANCE, $validated);
    }

    public function storeMarriageRequest(Request $request)
    {
        $validated = $this->validateMarriageRequest($request);
        return $this->processActeCreation($request, ActeType::MARIAGE, $validated);
    }

    // ========================
    // Fichiers PDF
    // ========================
    public function downloadDemandePDF(Demande $demande)
    {
        $this->authorize('view', $demande);
        return PDFService::generateDemandePDF($demande)->download("demande-{$demande->reference}.pdf");
    }

    // ========================
    // Annulation
    // ========================
    public function cancelDemande(Request $request, Demande $demande)
    {
        $this->authorize('update', $demande);

        $demande->update([
            'statut' => DemandeStatut::ANNULEE,
            'motif_annulation' => $request->motif
        ]);

        return back()->with('success', 'Demande annulée avec succès');
    }

    // ========================
    // Méthodes privées
    // ========================
    private function processActeCreation(Request $request, ActeType $type, array $data)
    {
        DB::beginTransaction();

        try {
            $citoyen = $this->getCitoyenOrFail();
            $acte = $this->createActe($type, $data);
            $this->handleFiles($request, $acte, $type);
            $demande = $this->createDemande($citoyen, $acte, $data);

            DB::commit();

            return redirect()->route('citoyen.demandes.show', $demande)
                ->with('success', 'Demande créée avec succès.');

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->fileUploadService->rollbackUploads();
            return back()->withInput()->with('error', 'Erreur lors du traitement : ' . $e->getMessage());
        }
    }

    private function createActe(ActeType $type, array $data)
    {
        return Acte::create([
            'type' => $type,
            'reference' => $this->generateReference($type),
            'nom' => $data['lastName'],
            'prenoms' => $data['firstName'],
            'email' => $data['email'],
            'telephone' => $data['phone'],
            'date_naissance' => $data['dateOfBirth'],
            'lieu_naissance' => $data['placeOfBirth'],
            'sous_prefecture' => $data['subPrefecture'],
            'genre' => $data['gender'],
            'nom_pere' => $data['fatherName'],
            'nom_mere' => $data['motherName'],
            'numero_registre' => $data['registerNumber'],
            'motif_demande' => $data['purpose'],
            'nombre_copies' => $data['copies']
        ]);
    }

    private function handleFiles(Request $request, Acte $acte, ActeType $type)
    {
        $files = [
            'idFront' => 'fichier_id_recto',
            'idBack' => 'fichier_id_verso',
            'birthCopy' => 'copie_extrait'
        ];

        foreach ($files as $inputName => $dbField) {
            if ($request->hasFile($inputName)) {
                $path = $this->storeFile($request->file($inputName), $type);
                $acte->update([$dbField => $path]);
            }
        }
    }

    private function storeFile($file, ActeType $type)
    {
        return $file->store(
            "documents/{$type->value}/" . date('Y/m/d'),
            'public'
        );
    }

    private function createDemande($citoyen, $acte, $data)
    {
        return $citoyen->demandes()->create([
            'acte_id' => $acte->id,
            'statut' => DemandeStatut::EN_ATTENTE,
            'moyen_retrait' => $data['moyen_retrait']
        ]);
    }

    // ========================
    // Validation
    // ========================
    private function validateBirthRequest(Request $request)
    {
        return $request->validate([
            'lastName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'dateOfBirth' => 'required|date|before:today',
            'placeOfBirth' => 'required|string|max:255',
            'subPrefecture' => 'required|string|max:255',
            'gender' => 'required|in:M,F',
            'fatherName' => 'required|string|max:255',
            'motherName' => 'required|string|max:255',
            'registerNumber' => 'required|integer',
            'purpose' => 'required|string',
            'copies' => 'required|integer|min:1|max:5',
            'moyen_retrait' => 'required|string',
            'idFront' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'idBack' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'birthCopy' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
    }

    private function validateMarriageRequest(Request $request)
    {
        return $request->validate([
            // À adapter selon les champs spécifiques du formulaire de mariage
        ]);
    }

    // ========================
    // Helpers
    // ========================
    private function generateReference(ActeType $type)
    {
        return Str::upper(substr($type->value, 0, 3)) . '-' . now()->format('Ymd-His') . '-' . Str::random(4);
    }

    private function getCitoyenOrFail()
    {
        return Auth::user()->citoyen ?? abort(403, 'Accès non autorisé.');
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

    private function getStats($citoyen)
    {
        return [
            'en_attente' => $citoyen->demandes()->where('statut', DemandeStatut::EN_ATTENTE)->count(),
            'approuvees' => $citoyen->demandes()->where('statut', DemandeStatut::APPROUVEE)->count(),
            'rejetees' => $citoyen->demandes()->where('statut', DemandeStatut::REJETEE)->count()
        ];
    }

    private function getGroupedDemandes($citoyen)
    {
        return $citoyen->demandes()
            ->with(['acte', 'paiement'])
            ->latest()
            ->get()
            ->groupBy(fn($d) => $d->acte->type->value);
    }

    // ========================
    // Vues de formulaires
    // ========================
    public function formNaissance()
    {
        return view('citoyen.demandes.acteNaissance');
    }

    public function formMariage()
    {
        return view('citoyen.demandes.acteMariage');
    }

    public function formDeces()
    {
        return view('citoyen.demandes.acteDeces');
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
}
