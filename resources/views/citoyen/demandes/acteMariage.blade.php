
@extends('layouts.app')

@section('content')


<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-4 fade-in">Demande d'Acte de Mariage</h1>

        <div class="row g-0 shadow rounded-3 overflow-hidden fade-in">
            <!-- Left Side - Hero Section -->
            <div class="col-md-4">
                <div class="hero-section">
                <img src="/img/signature.jpg" alt="Documents officiels" class="hero-img">
                    <h2 class="h4 mb-4">Service d'État Civil</h2>
                    <p class="mb-4">Obtenez votre acte de mariage en toute simplicité grâce à notre service en ligne.</p>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Procédure simplifiée</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Traitement rapide</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Support disponible</span>
                    </div>
                </div>
            </div>

            <!-- Right Side - Form -->
            <div class="col-md-8">
                <div class="form-section">
                <form id="birthCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Informations du Mariage -->
                        <div class="mb-4">
                            <h3 class="section-title">Informations du Mariage</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date du Mariage</label>
                                    <input type="date" class="form-control" name="marriage_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lieu du Mariage</label>
                                    <input type="text" class="form-control" name="marriage_place" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">N° du Registre</label>
                                    <input type="text" class="form-control" name="register_number" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type de Mariage</label>
                                    <select class="form-select" name="marriage_type" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="civil">Civil</option>
                                        <option value="religieux">Religieux</option>
                                        <option value="coutumier">Coutumier</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de l'Époux -->
                        <div class="mb-4">
                            <h3 class="section-title">Informations de l'Époux</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="husband_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prénoms</label>
                                    <input type="text" class="form-control" name="husband_firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de Naissance</label>
                                    <input type="date" class="form-control" name="husband_birth_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lieu de Naissance</label>
                                    <input type="text" class="form-control" name="husband_birth_place" required>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de l'Épouse -->
                        <div class="mb-4">
                            <h3 class="section-title">Informations de l'Épouse</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="wife_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Prénoms</label>
                                    <input type="text" class="form-control" name="wife_firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date de Naissance</label>
                                    <input type="date" class="form-control" name="wife_birth_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Lieu de Naissance</label>
                                    <input type="text" class="form-control" name="wife_birth_place" required>
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
                                    <label class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Lien avec les Époux</label>
                                    <select class="form-select" name="relationship" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="epoux">Je suis l'époux</option>
                                        <option value="epouse">Je suis l'épouse</option>
                                        <option value="parent">Parent</option>
                                        <option value="autre">Autre</option>
                                    </select>
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
                                    <label class="form-label">Livret de Famille (première page)</label>
                                    <input type="file" class="form-control" name="family_book" required>
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
                                    <label class="form-label">Motif de la Demande</label>
                                    <select class="form-select" name="purpose" required>
                                        <option value="">Sélectionnez...</option>
                                        <option value="administratif">Usage Administratif</option>
                                        <option value="visa">Demande de Visa</option>
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

                        <button type="submit" class="btn btn-dark w-100 py-2">
                            Soumettre la Demande
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    <script>
        document.getElementById('marriageCertForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would typically handle the form submission to your Laravel backend
            alert('Formulaire soumis avec succès!');
        });
    </script>
</body>

@endsection