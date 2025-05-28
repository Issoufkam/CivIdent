@extends('layouts.app')

@section('content')

<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - État Civil Côte d'Ivoire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --orange: #F77F00;
            --green: #009B48;
            --white: #FFFFFF;
            --primary: #14213d;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--gray-100);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .flag-colors {
            display: flex;
            height: 6px;
            width: 100%;
            margin-bottom: 1rem;
        }

        .flag-colors div {
            flex: 1;
        }

        .color-orange { background-color: var(--orange); }
        .color-white { background-color: var(--white); }
        .color-green { background-color: var(--green); }

        .payment-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .method-card {
            cursor: pointer;
            border: 2px solid var(--gray-300);
            border-radius: 10px;
            transition: var(--transition);
            padding: 1rem;
        }

        .method-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .method-card.selected {
            border-color: var(--primary);
            background-color: rgba(20, 33, 61, 0.05);
        }

        .payment-summary {
            background-color: var(--gray-100);
            border-radius: 10px;
            padding: 1.5rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(20, 33, 61, 0.25);
            border-color: var(--primary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #1a2b4d;
            border-color: #1a2b4d;
        }

        .secure-badge {
            color: var(--primary);
            background-color: rgba(20, 33, 61, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="flag-colors">
        <div class="color-orange"></div>
        <div class="color-white"></div>
        <div class="color-green"></div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="mb-3">Paiement de Document d'État Civil</h2>
                    <span class="secure-badge">
                        <i class="bi bi-shield-check me-2"></i>Paiement Sécurisé
                    </span>
                </div>

                <div class="card payment-card">
                    <div class="card-body p-4">
                        <!-- Document Summary -->
                        <div class="payment-summary mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Document demandé</p>
                                    <h5 class="document-type mb-0">Extrait de Naissance</h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="text-muted mb-1">Référence</p>
                                    <h5 class="reference mb-0">REF-2023-001</h5>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <h5 class="mb-4">Choisir le mode de paiement</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="method-card selected" onclick="selectPaymentMethod(this, 'card')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-credit-card fs-4 me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Carte Bancaire</h6>
                                            <small class="text-muted">Visa, Mastercard</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="method-card" onclick="selectPaymentMethod(this, 'mobile')">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-phone fs-4 me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Mobile Money</h6>
                                            <small class="text-muted">Orange Money, MTN Money</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Payment Form -->
                        <div id="cardPaymentForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Numéro de carte</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
                                        <span class="input-group-text">
                                            <i class="bi bi-credit-card"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date d'expiration</label>
                                    <input type="text" class="form-control" placeholder="MM/AA">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Code de sécurité (CVV)</label>
                                    <input type="text" class="form-control" placeholder="123">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Nom sur la carte</label>
                                    <input type="text" class="form-control" placeholder="JOHN DOE">
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Money Form (Initially Hidden) -->
                        <div id="mobilePaymentForm" style="display: none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Opérateur</label>
                                    <select class="form-select">
                                        <option value="">Choisir un opérateur</option>
                                        <option value="orange">Orange Money</option>
                                        <option value="mtn">MTN Money</option>
                                        <option value="moov">Moov Money</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Numéro de téléphone</label>
                                    <input type="tel" class="form-control" placeholder="07 XX XX XX XX">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Montant du document</span>
                                <span>2,500 FCFA</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Frais de service</span>
                                <span>500 FCFA</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <strong>Total à payer</strong>
                                <strong>3,000 FCFA</strong>
                            </div>

                            <button class="btn btn-primary w-100 py-3" onclick="processPayment()">
                                Payer maintenant
                            </button>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-lock me-1"></i>
                                    Vos informations de paiement sont sécurisées
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div class="modal fade" id="processingModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <h5>Traitement du paiement en cours</h5>
                    <p class="text-muted mb-0">Veuillez ne pas fermer cette fenêtre...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5>Paiement réussi !</h5>
                    <p class="text-muted">Votre demande a été enregistrée avec succès.</p>
                    <button class="btn btn-primary" onclick="window.location.href='/'">
                        Retour à l'accueil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPaymentMethod(element, method) {
            // Remove selected class from all method cards
            document.querySelectorAll('.method-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            element.classList.add('selected');
            
            // Show/hide appropriate form
            document.getElementById('cardPaymentForm').style.display = method === 'card' ? 'block' : 'none';
            document.getElementById('mobilePaymentForm').style.display = method === 'mobile' ? 'block' : 'none';
        }

        function processPayment() {
            // Show processing modal
            const processingModal = new bootstrap.Modal(document.getElementById('processingModal'));
            processingModal.show();

            // Simulate payment processing
            setTimeout(() => {
                processingModal.hide();
                
                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, 2000);
        }

        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(.{4})/g, '$1 ').trim();
            e.target.value = value;
        });
    </script>
</body>
</html>
@endsection