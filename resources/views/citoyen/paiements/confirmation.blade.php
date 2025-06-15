@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4><i class="fas fa-check-circle me-2"></i> Paiement confirmé</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading">Merci pour votre paiement!</h5>
                        <p>Votre document est maintenant disponible en téléchargement.</p>
                    </div>

                    <div class="mb-4 p-3 border rounded">
                        <h5>Détails du document et du paiement</h5>
                        <ul class="list-unstyled">
                            <li><strong>Type de document:</strong> {{ $document->type->label() }}</li>
                            <li><strong>Numéro d'enregistrement:</strong> {{ $document->registry_number }}</li>
                            {{-- Utilisez l'objet $payment pour les détails du paiement --}}
                            <li><strong>Montant payé:</strong> {{ number_format($payment->amount, 0, ',', ' ') }} FCFA</li>
                            <li><strong>Méthode de paiement:</strong>
                                {{ $payment->payment_method === 'card' ? 'Carte bancaire' : 'Mobile Money' }}
                            </li>
                            {{-- Vérifiez que $payment->payment_date n'est pas null avant de formater --}}
                            <li><strong>Date du paiement:</strong>
                                @if ($payment->payment_date)
                                    {{ $payment->payment_date->format('d/m/Y H:i') }}
                                @else
                                    Non spécifiée
                                @endif
                            </li>
                            <li><strong>ID de transaction:</strong> {{ $payment->transaction_id }}</li>
                            <li><strong>Statut du paiement:</strong> {{ $payment->status }}</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('citoyen.demandes.download', $document) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-download me-2"></i> Télécharger le document
                        </a>
                        <a href="{{ route('citoyen.demandes.preview', $document) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i> Prévisualiser le document
                        </a>
                        {{-- <div class="modal-body p-0">
                                    <!-- L'iframe chargera le PDF via la route de prévisualisation -->
                                    <iframe src="{{ route('citoyen.demandes.preview', $document->id) }}" class="pdf-preview-iframe"></iframe>
                                </div> --}}
                        <a href="{{ route('citoyen.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i> Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
