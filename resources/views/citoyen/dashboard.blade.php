@extends('layouts.app')

@section('content')
<main class="container py-5">
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card p-4">
                <h4 class="mb-2">
                    @auth
                        {{ $greeting }}, {{ Auth::user()->prenom }} {{ Auth::user()->nom }} !
                    @endauth
                </h4>
                <p class="text-muted mb-4">Voici votre tableau de bord</p>

                <div class="mb-4">
                    <h5 class="mb-3">Dernière demande</h5>

                    @if($latestDocument)
                        <div class="p-3 bg-light rounded">
                            <h6>{{ $latestDocument->type->label() }}</h6>

                            <p class="mb-2">
                                <span class="badge bg-{{ $latestDocument->status->color() }}">
                                    {{ $latestDocument->status->label() }}
                                </span>
                            </p>

                            @if($latestDocument->status === \App\Enums\DocumentStatus::APPROUVEE)
                                @if(!$latestDocument->is_paid)
                                    <div class="mt-2">
                                        <a href="{{ route('citoyen.paiements.form', $latestDocument) }}"
                                        class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-credit-card me-1"></i> Payer
                                        </a>
                                    </div>
                                @else
                                    <span class="badge bg-success mt-2">Payé</span>
                                @endif
                            @endif

                            <small class="text-muted">{{ $latestDocument->created_at->format('d F Y') }}</small>
                        </div>
                    @else
                        <p class="text-muted">Aucune demande pour le moment.</p>
                    @endif
                </div>

                <a href="{{ route('citoyen.demandes.index') }}" class="btn btn-outline-success w-100">
                    <i class="fas fa-plus-circle me-2"></i>
                    Nouvelle demande
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-4">Historique des demandes</h3>

                <ul class="nav nav-tabs mb-4" id="docTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#naissance">Extrait de naissance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#mariage">Acte de mariage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#deces">Acte de décès</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-vie">Certificat de vie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-entretien">Certificat d'entretien</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-revenu">Certificat de non revenu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-divorce">Certificat de divorce</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Tab pour Extrait de naissance --}}
                    <div class="tab-pane fade show active" id="naissance">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($naissanceDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande d'extrait de naissance.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $naissanceDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Acte de mariage --}}
                    <div class="tab-pane fade" id="mariage">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mariageDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande d'acte de mariage.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $mariageDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Acte de décès --}}
                    <div class="tab-pane fade" id="deces">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($decesDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande d'acte de décès.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $decesDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Certificat de vie --}}
                    <div class="tab-pane fade" id="certificat-vie">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($certificatVieDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande de certificat de vie.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $certificatVieDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Certificat d'entretien --}}
                    <div class="tab-pane fade" id="certificat-entretien">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($certificatEntretienDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande de certificat d'entretien.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $certificatEntretienDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Certificat de non revenu --}}
                    <div class="tab-pane fade" id="certificat-revenu">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($certificatRevenuDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande de certificat de non revenu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $certificatRevenuDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>

                    {{-- Tab pour Certificat de divorce --}}
                    <div class="tab-pane fade" id="certificat-divorce">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($certificatDivorceDocuments as $demande)
                                        <tr>
                                            <td>{{ $demande->type->label() }}</td>
                                            <td>
                                                <span class="badge bg-{{ $demande->status->color() }}">
                                                    {{ $demande->status->label() }}
                                                </span>
                                            </td>
                                            <td>{{ $demande->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success mb-1">
                                                    <i class="fas fa-eye me-1"></i> Voir
                                                </a>
                                                @if ($demande->status === \App\Enums\DocumentStatus::APPROUVEE && !$demande->is_paid)
                                                    <a href="{{ route('citoyen.paiements.form', $demande) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-credit-card me-1"></i> Payer
                                                    </a>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">Aucune demande de certificat de divorce.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $certificatDivorceDocuments->links('pagination::bootstrap-5') }} {{-- Ajout des liens de pagination --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="mb-5">
        <h2 class="mb-4">Demander un acte</h2>
        <div class="row g-4">
            {{-- Les cartes de demande d'acte (naissance, mariage, décès) --}}
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc.jpeg') }}" alt="Extrait de naissance" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Extrait de naissance</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.naissance') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc 10.jpeg') }}" alt="Acte de mariage" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Acte de mariage</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.mariage') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc4.jpeg') }}" alt="Acte de décès" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Acte de décès</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.deces') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-4">Faire une demande de certificat</h2>
        <div class="row g-4">
            {{-- Les cartes de demande de certificat --}}
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/img1.jpeg') }}" alt="Certificat de vie" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Certificat de vie</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 3; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.vie') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/dossier 1.jpeg') }}" alt="Certificat d'entretien" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Certificat d'entretien</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.entretien') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc 6.jpeg') }}" alt="Certificat de non revenu" class="card-img-top">
                    <div class="card-body">
                        <h5 class="card-title">Certificat de non revenu</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.revenu') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>

            {{-- Le certificat de divorce n'était pas dans la liste des onglets, je l'ajoute ici pour cohérence --}}
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc 7.jpeg') }}" alt="Certificat de divorce" class="card-img-top"> {{-- Image placeholder --}}
                    <div class="card-body">
                        <h5 class="card-title">Certificat de divorce</h5>
                        <div class="text-warning mb-3">
                            @for ($i = 0; $i < 4; $i++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </div>
                        <a href="{{ route('citoyen.demandes.divorce') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
