@extends('layouts.app')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Paiement pour le document: {{ $document->type->label() }}</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Montant à payer:</strong> {{ number_format($montant, 0, ',', ' ') }} FCFA
                    </div>

                    <form method="POST" action="{{ route('citoyen.paiements.process', $document) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Méthode de paiement</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card" checked>
                                <label class="form-check-label" for="card">
                                    Carte bancaire
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="mobile_money" value="mobile_money">
                                <label class="form-check-label" for="mobile_money">
                                    Mobile Money
                                </label>
                            </div>
                        </div>

                        <div id="card_fields">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Numéro de carte</label>
                                <input type="text" class="form-control" id="card_number" name="card_number">
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="expiry_date" class="form-label">Date d'expiration</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/AA">
                                </div>
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv">
                                </div>
                            </div>
                        </div>

                        <div id="mobile_money_fields" style="display: none;">
                            <div class="mb-3">
                                <label for="mobile_operator" class="form-label">Opérateur</label>
                                <select class="form-select" id="mobile_operator" name="mobile_operator">
                                    <option value="orange">Orange Money</option>
                                    <option value="mtn">MTN Mobile Money</option>
                                    <option value="moov">Moov Money</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Payer maintenant
                            </button>
                            <a href="{{ route('citoyen.demandes.show', $document) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cardRadio = document.getElementById('card');
        const mobileRadio = document.getElementById('mobile_money');
        const cardFields = document.getElementById('card_fields');
        const mobileFields = document.getElementById('mobile_money_fields');

        function toggleFields() {
            if (cardRadio.checked) {
                cardFields.style.display = 'block';
                mobileFields.style.display = 'none';
            } else {
                cardFields.style.display = 'none';
                mobileFields.style.display = 'block';
            }
        }

        cardRadio.addEventListener('change', toggleFields);
        mobileRadio.addEventListener('change', toggleFields);

        // Initial toggle
        toggleFields();
    });
</script>
@endpush
@endsection
