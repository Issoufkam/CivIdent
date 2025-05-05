
@extends('layouts.app')
@section('content')

<!-- Header -->
<header class="py-4 bg-light border-bottom">
        <div class="container">
            <h1 class="mb-0 text-primary fw-bold">A propos de nous</h1>
        </div>
    </header>

    <main class="container my-5">
        <!-- Mission Section -->
        <section class="mission-section mb-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Notre mission</h2>
                <div class="w-50 mx-auto">
                    <p class="text-muted">
                        Nous facilitons l'accès aux documents administratifs pour tous les Ivoiriens,
                        en simplifiant et en accélérant les procédures grâce à la technologie numérique.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card border-0 shadow-sm">
                        <div class="p-3">
                            <img src="/img/communication.jpeg" 
                                 alt="Communication rapide" 
                                 class="feature-image">
                        </div>
                        <div class="card-body text-center">
                            <h3 class="card-title h5 fw-bold">Communication rapide</h3>
                            <p class="card-text text-muted">Notre plateforme permet une communication instantanée entre vous et nos agents pour un suivi efficace de vos demandes.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card border-0 shadow-sm">
                        <div class="p-3">
                            <img src="/img/acces facile.jpeg" 
                                 alt="Accès à tous les documents" 
                                 class="feature-image">
                        </div>
                        <div class="card-body text-center">
                            <h3 class="card-title h5 fw-bold">Accès à tous les documents</h3>
                            <p class="card-text text-muted">Nous vous offrons un accès simple et rapide à tous vos documents administratifs officiels en quelques clics.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card border-0 shadow-sm">
                        <div class="p-3">
                            <img src="/img/centre d'aide.jpeg" 
                                 alt="Support 24/7" 
                                 class="feature-image">
                        </div>
                        <div class="card-body text-center">
                            <h3 class="card-title h5 fw-bold">Support 24/7</h3>
                            <p class="card-text text-muted">Notre équipe de support est disponible 24h/24 et 7j/7 pour répondre à toutes vos questions et résoudre vos problèmes.</p>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card feature-card border-0 shadow-sm">
                        <div class="p-3">
                            <img src="/img/equipe.jpeg" 
                                 alt="Partenariats stratégiques" 
                                 class="feature-image">
                        </div>
                        <div class="card-body text-center">
                            <h3 class="card-title h5 fw-bold">Partenariats stratégiques</h3>
                            <p class="card-text text-muted">Nous collaborons avec les administrations publiques pour vous offrir un service rapide, fiable et sécurisé.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section mb-5 py-4">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">Qui sommes-nous ?</h2>
                    <p class="lead mb-4">
                        Digit-Ivoire est une plateforme numérique innovante dédiée à simplifier 
                        l'accès aux documents administratifs en Côte d'Ivoire.
                    </p>
                    <p class="text-muted">
                        Passionnés par la technologie et l'amélioration des services publics, nous 
                        avons développé cette plateforme pour réduire les délais d'attente et 
                        simplifier les démarches administratives pour tous les citoyens.
                    </p>
                    <p class="text-muted">
                        Notre équipe combine expertise technologique et connaissance approfondie 
                        des processus administratifs ivoiriens pour vous offrir un service 
                        de qualité supérieure.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="rounded shadow-lg overflow-hidden">
                        <img src="/img/télécharger.jpeg" 
                             alt="Notre équipe Digit-Ivoire" 
                             class="img-fluid"
                             style="width: 100%; height: 400px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Functioning Section -->
        <section class="functioning-section mb-5 py-4">
            <div class="bg-light p-4 p-md-5 rounded-3">
                <h2 class="fw-bold mb-4">Comment ça fonctionne ?</h2>
                
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3 bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold">1</span>
                                </div>
                                <p class="mb-0 fs-5">Créez votre compte Digit-Ivoire en quelques minutes</p>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3 bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold">2</span>
                                </div>
                                <p class="mb-0 fs-5">Sélectionnez le document officiel dont vous avez besoin</p>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3 bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold">3</span>
                                </div>
                                <p class="mb-0 fs-5">Fournissez les informations requises et effectuez le paiement</p>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3 bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold">4</span>
                                </div>
                                <p class="mb-0 fs-5">Suivez le statut de votre demande en temps réel</p>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="me-3 bg-white rounded-circle p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold">5</span>
                                </div>
                                <p class="mb-0 fs-5">Recevez votre document par voie électronique ou choisissez la livraison physique</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="p-4 bg-white rounded-3 shadow-sm h-100">
                            <h4 class="text-primary mb-4">Avantages de notre processus</h4>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Économisez du temps en évitant les files d'attente</span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Processus sécurisé et transparent</span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Traitement rapide des demandes</span>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Support client disponible à chaque étape</span>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Notifications automatiques sur l'avancement</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section mb-5 py-4">
            <h2 class="fw-bold mb-4">Nos valeurs</h2>
            
            <div class="row g-4">
                <!-- Value 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card value-card border-0 shadow-sm text-center p-4">
                        <div class="text-primary mb-3 d-flex justify-content-center">
                            <i class="fas fa-shield-alt fa-3x"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Sécurité</h3>
                        <p class="card-text text-muted">Protection maximale de vos données personnelles et de vos documents.</p>
                    </div>
                </div>

                <!-- Value 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card value-card border-0 shadow-sm text-center p-4">
                        <div class="text-primary mb-3 d-flex justify-content-center">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Efficacité</h3>
                        <p class="card-text text-muted">Traitement rapide et précis de toutes vos demandes administratives.</p>
                    </div>
                </div>

                <!-- Value 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card value-card border-0 shadow-sm text-center p-4">
                        <div class="text-primary mb-3 d-flex justify-content-center">
                            <i class="fas fa-user-check fa-3x"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Transparence</h3>
                        <p class="card-text text-muted">Information claire sur les coûts, les délais et les procédures.</p>
                    </div>
                </div>

                <!-- Value 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="card value-card border-0 shadow-sm text-center p-4">
                        <div class="text-primary mb-3 d-flex justify-content-center">
                            <i class="fas fa-handshake fa-3x"></i>
                        </div>
                        <h3 class="card-title h5 fw-bold">Engagement</h3>
                        <p class="card-text text-muted">Dévouement total pour faciliter votre expérience administrative.</p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p class="lead">
                    Chez Digit-Ivoire, nous nous engageons à offrir un service de qualité exceptionnelle,
                    en mettant toujours vos besoins au centre de nos préoccupations.
                </p>
            </div>
        </section>
    </main>

    @endsection    