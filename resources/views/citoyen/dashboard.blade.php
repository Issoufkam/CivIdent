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
                    @if($lastDemande)
                        <div class="p-3 bg-light rounded mb-2">
                            <h6>Demande d'acte de {{ $lastDemande->type }}</h6>
                            <p class="mb-2">
                                <span class="badge bg-{{ $lastDemande->statusColor() }}">
                                    {{ $lastDemande->statusLabel() }}
                                </span>
                            </p>
                            <small class="text-muted">{{ optional($lastDemande->decision_date)->format('d F Y') }}</small>
                        </div>
                    @else
                        <p>Aucune demande récente</p>
                    @endif
                </div>

                <a href="{{ route('citoyen.demandes.create', ['type' => 'deces']) }}" class="btn btn-outline-success w-100">
                    <i class="fas fa-plus-circle me-2"></i> Nouvelle demande
                </a>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h3 class="mb-4">Historique des demandes</h3>

                <ul class="nav nav-tabs mb-4" id="docTab" role="tablist">
                    @foreach($demandesGroupees as $type => $documents)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ \Illuminate\Support\Str::slug($type) }}" role="tab" aria-controls="{{ \Illuminate\Support\Str::slug($type) }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ ucfirst($type) }}</a>
                        </li>
                    @endforeach
                </ul>

               <div class="tab-content">
                    @foreach ($demandesGroupees as $type => $documents)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ \Illuminate\Support\Str::slug($type) }}" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            <th>Commune</th>
                                            <th>Statut</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($documents as $document)
                                            <tr>
                                                <td>{{ $document->registry_number }}</td>
                                                <td>{{ $document->commune->nom_commune ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $document->statusColor() }}">
                                                        {{ $document->statusLabel() }}
                                                    </span>
                                                </td>
                                                <td>{{ $document->created_at->isoFormat('LL') }}</td>
                                                <td>
                                                    <a href="{{ route('citoyen.demandes.show', $document) }}" class="btn btn-sm btn-soft-primary mb-1">
                                                        <i class="fas fa-eye me-1"></i> Détails
                                                    </a>

                                                    @if($document->fichier_path)
                                                        <a href="{{ route('citoyen.demandes.download', $document) }}" class="btn btn-sm btn-soft-success">
                                                            <i class="fas fa-download me-1"></i> Télécharger
                                                        </a>
                                                    @else
                                                        <span class="text-muted small d-block">Aucun fichier</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucune demande enregistrée.</td>
                                            </tr>
                                        @endforelse
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
            @php
                $actes = [
                    ['type' => 'naissance', 'image' => 'doc.jpeg', 'stars' => 4],
                    ['type' => 'mariage', 'image' => 'doc 10.jpeg', 'stars' => 4],
                    ['type' => 'deces', 'image' => 'doc4.jpeg', 'stars' => 4],
                ];
            @endphp

            @foreach ($actes as $acte)
                <div class="col-md-4">
                    <div class="card document-card h-100">
                        <img src="{{ asset('img/' . $acte['image']) }}" alt="Acte de {{ $acte['type'] }}" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Acte de {{ $acte['type'] }}</h5>
                            <div class="text-warning mb-3">
                                @for ($i = 0; $i < $acte['stars']; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <a href="{{ route('citoyen.demandes.create', ['type' => $acte['type']]) }}" class="btn btn-outline-success w-100">Demander</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Certificates Section -->
    <section>
        <h2 class="mb-4">Faire une demande de certificat</h2>
        <div class="row g-4">
            @php
                $certificats = [
                    ['type' => 'vie', 'title' => 'Certificat de vie', 'image' => 'img1.jpeg', 'stars' => 3],
                    ['type' => 'entretien', 'title' => "Certificat d'entretien", 'image' => 'dossier 1.jpeg', 'stars' => 5],
                    ['type' => 'revenu', 'title' => 'Certificat de non revenu', 'image' => 'doc 6.jpeg', 'stars' => 4],
                ];
            @endphp

            @foreach ($certificats as $certificat)
                <div class="col-md-4">
                    <div class="card document-card h-100">
                        <img src="{{ asset('img/' . $certificat['image']) }}" alt="{{ $certificat['title'] }}" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">{{ $certificat['title'] }}</h5>
                            <div class="text-warning mb-3">
                                @for ($i = 0; $i < $certificat['stars']; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </div>
                            <a href="{{ route('citoyen.demandes.create', ['type' => $certificat['type']]) }}" class="btn btn-outline-success w-100">Demander</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</main>
@endsection
