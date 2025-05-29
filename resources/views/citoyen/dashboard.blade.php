@extends('layouts.app')

@section('content')
<main class="container py-5">
    <!-- Dashboard Header -->
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
                                <span class="badge
                                    @if($latestDocument->status === \App\Enums\DocumentStatus::EN_ATTENTE) bg-warning
                                    @elseif($latestDocument->status === \App\Enums\DocumentStatus::APPROUVEE) bg-success
                                    @elseif($latestDocument->status === \App\Enums\DocumentStatus::REJETEE) bg-danger
                                    @else bg-secondary @endif">
                                    {{ $latestDocument->status->label() }}
                                </span>
                            </p>
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
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-revenu">Certificat de non revenu</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Onglet Naissance -->
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
                                    @foreach ($demandes['naissance'] ?? [] as $demande)
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
                                                    <form action="{{ route('citoyen.paiement.form', $demande) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </button>
                                                    </form>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Onglet Mariage -->
                    <div class="tab-pane fade" id="mariage">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <!-- Même structure que pour naissance -->
                                <tbody>
                                    @foreach ($demandes['mariage'] ?? [] as $demande)
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
                                                    <form action="{{ route('citoyen.paiement.form', $demande) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </button>
                                                    </form>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Onglet Décès -->
                    <div class="tab-pane fade" id="deces">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <!-- Même structure que pour naissance -->
                                <tbody>
                                    @foreach ($demandes['deces'] ?? [] as $demande)
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
                                                    <form action="{{ route('citoyen.paiement.form', $demande) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </button>
                                                    </form>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Onglet Certificat de vie -->
                    <div class="tab-pane fade" id="certificat-vie">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <!-- Même structure que pour naissance -->
                                <tbody>
                                    @foreach ($demandes['vie'] ?? [] as $demande)
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
                                                    <form action="{{ route('citoyen.paiement.form', $demande) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </button>
                                                    </form>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Onglet Certificat de non revenu -->
                    <div class="tab-pane fade" id="certificat-revenu">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <!-- Même structure que pour naissance -->
                                <tbody>
                                    @foreach ($demandes['certificat-revenu'] ?? [] as $demande)
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
                                                    <form action="{{ route('citoyen.paiement.form', $demande) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-credit-card me-1"></i> Payer
                                                        </button>
                                                    </form>
                                                @elseif ($demande->is_paid)
                                                    <span class="badge bg-success">Payé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents Section -->
    <section class="mb-5">
        <h2 class="mb-4">Demander un acte</h2>
        <div class="row g-4">
            @foreach ($demandes->keys() as $type)
                <div class="col-md-4">
                    <div class="card document-card h-100">
                        <img src="{{ asset('img/' . Str::slug($type) . '.jpeg') }}" alt="{{ $type }}" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">{{ $type }}</h5>
                            <div class="text-warning mb-3">
                                @for ($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <a href="{{ route('citoyen.demandes.' . Str::slug($type)) }}" class="btn btn-outline-success w-100">Demander</a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Acte de décès (ajout Kassi) -->
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
            <!-- Extrait de naissance  (ajout Kassi) -->
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc.jpeg') }}" alt="Acte de décès" class="card-img-top">
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
            <!-- Acte de mariage (ajout Kassi) -->
            <div class="col-md-4">
                <div class="card document-card h-100">
                    <img src="{{ asset('img/doc 10.jpeg') }}" alt="Acte de décès" class="card-img-top">
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
        </div>
    </section>

    <!-- Certificates Section -->
    <section>
        <h2 class="mb-4">Faire une demande de certificat</h2>
        <div class="row g-4">
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
                    <img src="{{ asset('img/dossier 1.jpeg') }}" alt="Certification d'entretien" class="card-img-top">
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
                    <img src="{{ asset('img/doc 6.jpeg') }}" alt="Certification de non revenu" class="card-img-top">
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
        </div>
    </section>
</main>
@endsection
