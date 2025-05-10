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
                    <h5 class="mb-3">Demandes en cours</h5>
                    <div class="p-3 bg-light rounded">
                        <h6>Demande d'acte de naissance</h6>
                        <p class="mb-2">
                            <span class="badge bg-warning">En cours</span>
                        </p>
                        <small class="text-muted">10 Avril 2025</small>
                    </div>
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
                        <a class="nav-link" data-bs-toggle="tab" href="#certificat-vie">Certificat de vie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#celibat">Certificat de non revenu</a>
                    </li>
                </ul>

                <div class="tab-content">
                    @foreach ($demandes as $type => $demandesType)
                        <div class="tab-pane fade{{ $loop->first ? ' show active' : '' }}" id="{{ Str::slug($type) }}">
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
                                        @foreach ($demandesType as $demande)
                                            <tr>
                                                <td>{{ $demande->acte->type->label() }}</td>
                                                <td><span class="badge bg-{{ $demande->statut->color() }}">{{ $demande->statut->label() }}</span></td>
                                                <td>{{ $demande->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('citoyen.demandes.show', $demande) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-eye me-1"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
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
                            <a href="{{ route('citoyen.demande.' . Str::slug($type)) }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.deces') }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.naissance') }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.mariage') }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.vie') }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.entretien') }}" class="btn btn-outline-success w-100">Demander</a>
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
                        <a href="{{ route('citoyen.demande.revenu') }}" class="btn btn-outline-success w-100">Demander</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
