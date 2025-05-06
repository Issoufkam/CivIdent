@extends('layouts.app')
@section('content')

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4 fade-in">Demande d'Acte de Décès</h1>

        <div class="row g-0 shadow rounded-3 overflow-hidden fade-in">
            <!-- Left Side - Hero Section -->
            <div class="col-md-4">
                <div class="hero-section">
                    <img src="/img/décès10.jpg" alt="Documents officiels" class="hero-img">
                    <h2 class="h4 mb-4">Service d'État Civil</h2>
                    <p class="mb-4">Facilitez vos démarches administratives en effectuant votre demande d'acte de décès en ligne.</p>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Traitement rapide et sécurisé</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Suivi en ligne</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Assistance disponible</span>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="col-md-8">
                <div class="form-section">
                    <form id="birthCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Informations du Défunt -->
                        <div class="mb-4">
                            <h3 class="section-title">Informations du Défunt</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom du Défunt</label>
                                    <input type="text" class="form-control" name="deceased_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prénoms du Défunt</label>
                                    <input type="text" class="form-control" name="deceased_firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de Décès</label>
                                    <input type="date" class="form-control" name="death_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lieu de Décès</label>
                                    <input type="text" class="form-control" name="death_place" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cause du Décès</label>
                                    <input type="text" class="form-control" name="death_cause">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">N° du Registre de Décès</label>
                                    <input type="text" class="form-control" name="register_number" required>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du Demandeur -->
                        <div class="mb-4">
                            <h3 class="section-title">Informations du Demandeur</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="requester_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prénoms</label>
                                    <input type="text" class="form-control" name="requester_firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lien avec le Défunt</label>
                                    <select class="form-select" name="relationship" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="conjoint">Conjoint(e)</option>
                                        <option value="enfant">Enfant</option>
                                        <option value="parent">Parent</option>
                                        <option value="frere_soeur">Frère/Sœur</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Requis -->
                        <div class="mb-4">
                            <h3 class="section-title">Documents Requis</h3>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Pièce d'identité du Demandeur</label>
                                    <input type="file" class="form-control" name="requester_id" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Certificat de Décès Original</label>
                                    <input type="file" class="form-control" name="death_certificate" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Document Prouvant le Lien de Parenté</label>
                                    <input type="file" class="form-control" name="relationship_proof" required>
                                </div>
                            </div>
                        </div>

                        <!-- Détails de la Demande -->
                        <div class="mb-4">
                            <h3 class="section-title">Détails de la Demande</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre de Copies</label>
                                    <select class="form-select" name="copies" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Usage Prévu</label>
                                    <select class="form-select" name="purpose" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="succession">Succession</option>
                                        <option value="administratif">Usage Administratif</option>
                                        <option value="assurance">Assurance</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Submit -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Je certifie que les informations fournies sont exactes
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2">
                            Soumettre la Demande
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('deathCertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would typically handle the form submission to your Laravel backend
            alert('Formulaire soumis avec succès!');
        });
    </script>

</body>


@endsection
