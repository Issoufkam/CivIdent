@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Paiement de la demande #{{ $document->registry_number }}</h2>

    <div class="card p-4 mb-3">
        <p><strong>Type :</strong> {{ $document->type->label() }}</p>
        <p><strong>Date :</strong> {{ $document->created_at->format('d/m/Y') }}</p>
        <p><strong>Statut :</strong>
            <span class="badge bg-success">{{ $document->status->label() }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('citoyen.paiement', $document) }}">
        @csrf
        <div class="mb-3">
            <label for="cardNumber" class="form-label">Numéro de carte (simulé)</label>
            <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Nom sur la carte</label>
            <input type="text" class="form-control" id="name" placeholder="Jean Dupont" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-credit-card me-1"></i> Payer maintenant
        </button>
        <a href="{{ route('citoyen.demandes.show', $document) }}" class="btn btn-outline-secondary ms-2">Annuler</a>
    </form>
</div>
@endsection
