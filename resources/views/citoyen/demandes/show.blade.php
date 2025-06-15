@extends('layouts.app')

{{-- Success and Error Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@section('content')
<style>
    :root {
        --primary: #0056b3;
        --primary-light: rgba(0, 86, 179, 0.1);
        --secondary: #6c757d;
        --success: #28a745;
        --success-light: rgba(40, 167, 69, 0.1);
        --warning: #ffc107;
        --danger: #dc3545;
        --light: #f8f9fa;
        --dark: #343a40;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #adb5bd;
        --gray-600: #6c757d;
        --gray-700: #495057;
        --gray-800: #343a40;
        --gray-900: #212529;
        --transition-speed: 0.3s;
    }

    .detail-view {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .timeline-item {
        position: relative;
        padding-left: 2rem;
        padding-bottom: 1.5rem;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: var(--gray-300);
    }

    .badge-pending {
        background-color: var(--warning);
        color: var(--dark);
    }

    .badge-approved {
        background-color: var(--success);
        color: white;
    }

    .badge-rejected {
        background-color: var(--danger);
        color: white;
    }

    /* Style pour le conteneur PDF */
    .pdf-container {
        width: 100%;
        height: 600px; /* Ajustez la hauteur selon vos besoins */
        border: 1px solid var(--gray-300);
        border-radius: 0.5rem;
        overflow: hidden; /* Important pour masquer les barres de défilement de l'iframe */
    }

    .pdf-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
</style>

<div class="container-fluid pt-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow rounded-3">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Demande #{{ $demande->registry_number }}</h4>
                </div>

                <div class="card-body detail-view">
                    <div class="row">
                        {{-- Colonne Informations --}}
                        <div class="col-md-6 mb-4">
                            <div class="detail-section">
                                <h5 class="text-secondary">
                                    <i class="bi bi-info-circle me-2"></i>Informations générales
                                </h5>
                                <div class="row mt-3">
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Type</p>
                                        <p class="fw-bold">{{ ucfirst($demande->type->value) }}</p>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Statut</p>
                                        <span class="badge
                                            @if($demande->status->value === 'APPROUVEE') badge-approved
                                            @elseif($demande->status->value === 'REJETEE') badge-rejected
                                            @else badge-pending @endif">
                                            {{ ucfirst($demande->status->value) }}
                                        </span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Date de création</p>
                                        <p class="fw-bold">{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Dernière mise à jour</p>
                                        <p class="fw-bold">{{ $demande->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section mt-4">
                                <h5 class="text-secondary">
                                    <i class="bi bi-person me-2"></i>Agent traitant
                                </h5>
                                <div class="row mt-3">
                                    <div class="col-12 mb-2">
                                        <p class="mb-1 text-muted">Nom</p>
                                        <p class="fw-bold">{{ $demande->agent->nom ?? '-' }} {{ $demande->agent->prenom ?? '-' }}</p>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Email</p>
                                        <p class="fw-bold">{{ $demande->agent->email ?? '-' }}</p>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <p class="mb-1 text-muted">Commune</p>
                                        <p class="fw-bold">{{ $demande->commune->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Colonne Détails spécifiques (métadonnées) --}}
                        <div class="col-md-6 mb-4">
                            <div class="detail-section">
                                @php
                                    // CORRECTION: Accédez directement au tableau 'metadata' car il n'est pas imbriqué sous une autre clé 'metadata'.
                                    $metadata = $demande->metadata ?? []; // Utilisez $demande->metadata directement

                                    // Assurez-vous toujours que $metadata est un tableau pour éviter des erreurs avec foreach
                                    $metadata = is_array($metadata) ? $metadata : [];

                                    $fields = [];
                                    $icon = 'bi-file-text'; // Icône par défaut

                                    switch ($demande->type->value) {
                                        case 'naissance':
                                            $icon = 'bi-baby';
                                            $fields = [
                                                'nom_enfant' => "Nom de l'enfant",
                                                'prenom_enfant' => "Prénom de l'enfant",
                                                'date_naissance' => "Date de naissance",
                                                'sexe' => "Sexe",
                                                'lieu_naissance' => "Lieu de naissance",
                                                'nom_pere' => "Nom du père",
                                                'nationalite_pere' => "Nationalité du père",
                                                'nom_mere' => "Nom de la mère",
                                                'nationalite_mere' => "Nationalité de la mère"
                                            ];
                                            break;
                                        case 'mariage':
                                            $icon = 'bi-heart';
                                            $fields = [
                                                'nom_epoux' => "Nom de l'époux",
                                                'prenom_epoux' => "Prénom de l'époux",
                                                // 'date_naissance_epoux' => "Date de naissance de l'époux",
                                                // 'lieu_naissance_epoux' => "Lieu de naissance de l'époux",
                                                // 'nom_mere_epoux' => "Nom de la mère de l'époux",
                                                'nationalite_epoux' => "Nationalité de l'époux",
                                                'nom_epouse' => "Nom de l'épouse",
                                                'prenom_epouse' => "Prénom de l'épouse",
                                                'date_naissance_epouse' => "Date de naissance de l'épouse",
                                                'lieu_naissance_epouse' => "Lieu de naissance de l'épouse",
                                                'nom_mere_epouse' => "Nom de la mère de l'épouse",
                                                'nationalite_epouse' => "Nationalité de l'épouse",
                                                'date_mariage' => "Date du mariage",
                                                'lieu_mariage' => "Lieu du mariage"
                                            ];
                                            break;
                                        case 'deces':
                                            $icon = 'bi-flag';
                                            $fields = [
                                                'nom_defunt' => "Nom du défunt",
                                                'prenom_defunt' => "Prénom du défunt",
                                                'date_deces' => "Date du décès",
                                                'lieu_deces' => "Lieu du décès",
                                                'nom_declarant' => "Nom du déclarant",
                                                'prenom_declarant' => "Prénom du déclarant",
                                                'lien_parente_declarant' => "Lien de parenté avec le défunt"
                                            ];
                                            break;
                                        case 'certificat-vie':
                                            $icon = 'bi-person-badge';
                                            $fields = [
                                                'nom_demandeur' => "Nom du demandeur",
                                                'prenom_demandeur' => "Prénom du demandeur",
                                                'date_naissance_demandeur' => "Date de naissance du demandeur",
                                                'lieu_naissance_demandeur' => "Lieu de naissance du demandeur",
                                            ];
                                            break;
                                        case 'certificat-entretien':
                                            $icon = 'bi-people';
                                            $fields = [
                                                'nom_demandeur' => "Nom du demandeur",
                                                'prenom_demandeur' => "Prénom du demandeur",
                                                'relation' => "Relation avec la personne entretenue",
                                            ];
                                            break;
                                        case 'certificat-revenu':
                                            $icon = 'bi-cash';
                                            $fields = [
                                                'nom_demandeur' => "Nom du demandeur",
                                                'prenom_demandeur' => "Prénom du demandeur",
                                                'montant_revenu' => "Montant du revenu",
                                                'source_revenu' => "Source du revenu",
                                            ];
                                            break;
                                        case 'certificat-divorce':
                                            $icon = 'bi-file-earmark-break';
                                            $fields = [
                                                'nom_epoux' => "Nom de l'époux",
                                                'prenom_epoux' => "Prénom de l'époux",
                                                'nom_epouse' => "Nom de l'épouse",
                                                'prenom_epouse' => "Prénom de l'épouse",
                                                'date_divorce' => "Date du divorce",
                                            ];
                                            break;
                                        default:
                                            // Pour les autres types, affichez toutes les clés de métadonnées trouvées
                                            foreach ($metadata as $key => $value) {
                                                // Exclure 'copies' de l'affichage détaillé des métadonnées
                                                if ($key !== 'copies') {
                                                    $fields[$key] = ucfirst(str_replace('_', ' ', $key));
                                                }
                                            }
                                            break;
                                    }
                                @endphp

                                <h5 class="text-secondary">
                                    <i class="bi {{ $icon }} me-2"></i>Détails de la {{ ucfirst($demande->type->value) }}
                                </h5>

                                @if(!empty($fields))
                                    <div class="row mt-3">
                                        @foreach($fields as $key => $label)
                                            <div class="col-sm-6 mb-2">
                                                <p class="mb-1 text-muted">{{ $label }}</p>
                                                @if (is_array($metadata[$key] ?? null) || is_object($metadata[$key] ?? null))
                                                    <small class="text-muted">(Donnée complexe)</small>
                                                    <pre class="small text-muted border p-2 bg-light">{{ json_encode($metadata[$key], JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    <p class="fw-bold">{{ $metadata[$key] ?? 'Non renseigné' }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info mt-3">Aucune information spécifique disponible pour ce type de document.</div>
                                @endif

                                {{-- Affichage du nombre de copies, car il est dans les métadonnées pour tous les types --}}
                                @if (isset($demande->metadata['copies']))
                                    <div class="row mt-3">
                                        <div class="col-sm-6 mb-2">
                                            <p class="mb-1 text-muted">Nombre de copies</p>
                                            <p class="fw-bold">{{ $demande->metadata['copies'] }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Documents joints --}}
                    <div class="detail-section mt-4">
                        <h5 class="text-secondary">
                            <i class="bi bi-paperclip me-2"></i>Documents joints
                        </h5>
                        <div class="row mt-3" id="documentsList">
                            @if($demande->justificatif_path)
                                @php
                                    $filePath = Storage::url($demande->justificatif_path);
                                    $extension = pathinfo($demande->justificatif_path, PATHINFO_EXTENSION);
                                    $fileExists = Storage::disk('public')->exists($demande->justificatif_path);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                    $isPdf = (strtolower($extension) === 'pdf');

                                    $icon = 'file-earmark-text text-secondary'; // Default icon
                                    if ($isPdf) {
                                        $icon = 'file-earmark-pdf text-danger';
                                    } elseif ($isImage) {
                                        $icon = 'file-earmark-image text-primary';
                                    }
                                @endphp

                                <div class="col-md-6 mb-3">
                                    <div class="card document-item h-100 border-{{ $fileExists ? 'success' : 'danger' }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                @if($fileExists)
                                                    <i class="bi {{ $icon }} fs-3 me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title fw-bold">Justificatif principal</h6>
                                                        <p class="card-text text-muted small mb-2">
                                                            Ajouté le {{ $demande->created_at->format('d/m/Y à H:i') }}
                                                        </p>

                                                        @if($isImage)
                                                            <div class="mt-2 mb-3">
                                                                <img src="{{ $filePath }}"
                                                                     class="img-thumbnail"
                                                                     style="max-height: 150px; cursor: pointer"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#imagePreviewModal"
                                                                     onclick="document.getElementById('previewImage').src = this.src; document.getElementById('downloadPreview').href = this.src;">
                                                            </div>
                                                        @elseif($isPdf)
                                                            <div class="mt-2 mb-3">
                                                                <a href="{{ $filePath }}" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="bi bi-eye"></i> Voir le PDF
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="btn-group align-self-start" role="group">
                                                        <a href="{{ route('citoyen.demandes.download', ['document' => $demande->id, 'type' => 'justificatif']) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           download>
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @if($isImage)
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#imagePreviewModal"
                                                                    onclick="document.getElementById('previewImage').src = '{{ $filePath }}'; document.getElementById('downloadPreview').href = '{{ $filePath }}';">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <i class="bi {{ $icon }} fs-3 me-3 text-danger"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title fw-bold">Justificatif principal</h6>
                                                        <p class="card-text text-danger small">Fichier introuvable sur le serveur</p>
                                                        <p class="card-text text-muted small">Chemin: {{ $demande->justificatif_path }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Section pour les pièces jointes supplémentaires --}}
                            @forelse($demande->attachments as $attachment)
                                @php
                                    $attachmentPath = Storage::url($attachment->path);
                                    $attachmentExtension = pathinfo($attachment->path, PATHINFO_EXTENSION);
                                    $attachmentExists = Storage::disk('public')->exists($attachment->path);
                                    $isAttachmentImage = in_array(strtolower($attachmentExtension), ['jpg', 'jpeg', 'png']);
                                    $isAttachmentPdf = (strtolower($attachmentExtension) === 'pdf');

                                    $attachmentIcon = 'file-earmark-text text-secondary'; // Default icon
                                    if ($isAttachmentPdf) {
                                        $attachmentIcon = 'file-earmark-pdf text-danger';
                                    } elseif ($isAttachmentImage) {
                                        $attachmentIcon = 'file-earmark-image text-primary';
                                    }
                                @endphp

                                <div class="col-md-6 mb-3">
                                    <div class="card document-item h-100 border-{{ $attachmentExists ? 'success' : 'danger' }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                @if($attachmentExists)
                                                    <i class="bi {{ $attachmentIcon }} fs-3 me-3"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title fw-bold">{{ $attachment->name }}</h6>
                                                        <p class="card-text text-muted small mb-2">
                                                            {{ $attachment->created_at->format('d/m/Y à H:i') }} •
                                                            {{ round(Storage::disk('public')->size($attachment->path) / 1024, 1) }} KB
                                                        </p>

                                                        @if($isAttachmentImage)
                                                            <div class="mt-2 mb-3">
                                                                <img src="{{ $attachmentPath }}"
                                                                     class="img-thumbnail"
                                                                     style="max-height: 150px; cursor: pointer"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#imagePreviewModal"
                                                                     onclick="document.getElementById('previewImage').src = this.src; document.getElementById('downloadPreview').href = this.src;">
                                                            </div>
                                                        @elseif($isAttachmentPdf)
                                                            <div class="mt-2 mb-3">
                                                                <a href="{{ $attachmentPath }}" target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="bi bi-eye"></i> Voir le PDF
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="btn-group align-self-start" role="group">
                                                        {{-- Assuming 'agent.documents.download' is the correct route for attachments --}}
                                                        <a href="{{ route('agent.documents.download', ['document' => $demande->id, 'attachment' => $attachment->id]) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           download>
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @if($isAttachmentImage)
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#imagePreviewModal"
                                                                    onclick="document.getElementById('previewImage').src = '{{ $attachmentPath }}'; document.getElementById('downloadPreview').href = '{{ $attachmentPath }}';">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <i class="bi {{ $attachmentIcon }} fs-3 me-3 text-danger"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title fw-bold">{{ $attachment->name }}</h6>
                                                        <p class="card-text text-danger small">Fichier introuvable sur le serveur</p>
                                                        <p class="card-text text-muted small">Chemin: {{ $attachment->path }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-info">Aucun document supplémentaire joint.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Image Preview Modal --}}
                    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Aperçu du document</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="previewImage" src="" class="img-fluid" alt="Aperçu du document">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <a href="#" id="downloadPreview" class="btn btn-primary" download>
                                        <i class="bi bi-download me-1"></i>Télécharger
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal (moved outside the main content card for better structure) --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        {{-- The form action was commented out, I've left it as is but typically you would uncomment and provide a valid route --}}
        {{-- <form action="{{ route('citoyen.demandes.reject', $demande) }}" method="POST"> --}}
        <form method="POST" action="/your-reject-route/{{ $demande->id }}"> {{-- Example placeholder --}}
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Motif du rejet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Veuillez indiquer la raison du rejet :</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtenez une référence à l'élément modal
        const imagePreviewModalElement = document.getElementById('imagePreviewModal');

        // Assurez-vous que l'élément modal existe avant d'ajouter l'écouteur
        if (imagePreviewModalElement) {
            // Utilisez l'écouteur d'événements natif pour l'événement show.bs.modal de Bootstrap
            imagePreviewModalElement.addEventListener('show.bs.modal', function (event) {
                const previewImage = document.getElementById('previewImage');
                const downloadPreview = document.getElementById('downloadPreview');

                if (previewImage && downloadPreview) {
                    const imgSrc = previewImage.src; // L'image a déjà été définie par le onclick sur l'aperçu
                    downloadPreview.href = imgSrc;
                }
            });
        }
    });
</script>
@endpush
@endsection
