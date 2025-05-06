@extends('layouts.app')
@section('content')
<!-- Hero Section -->
<section class="py-5 mb-4 bg-dark">
  

        <div class="container">
          <div class="row">
            <div class="col-lg-8 mx-auto text-center py-4">
              <h1 class="display-4 fw-bold mb-4 text-white">Contactez-nous</h1>
              <p class="lead text-white opacity-75 mb-0">
                Notre équipe est à votre disposition pour vous accompagner dans toutes vos démarches administratives.
                Choisissez le moyen de communication qui vous convient le mieux.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact Content -->
      <div class="container py-5">
        <div class="row g-4 justify-content-center">
          <div class="col-lg-10">
            <h2 class="text-center mb-5">Comment nous contacter</h2>
            
            <!-- Contact Info Cards -->
            <div class="row g-4 mb-5">
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="icon-circle mb-3">
                      <i class="fas fa-map-marker-alt fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-2">Notre Adresse</h4>
                    <p class="card-text mb-1 fw-bold">Abidjan, Côte d'Ivoire</p>
                    <p class="text-muted small">Plateau, Avenue de la République</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="icon-circle mb-3">
                      <i class="fas fa-phone fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-2">Téléphone</h4>
                    <p class="card-text mb-1 fw-bold">+225 XX XX XX XX</p>
                    <p class="text-muted small">Lun-Ven, 8h-18h</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="icon-circle mb-3">
                      <i class="fas fa-envelope fa-lg"></i>
                    </div>
                    <h4 class="card-title mb-2">Email</h4>
                    <p class="card-text mb-1 fw-bold">contact@digit-ivoire.ci</p>
                    <p class="text-muted small">Service client 24/7</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Social Media Section -->
            <div class="card shadow-sm border-0 rounded-4">
              <div class="card-body p-4">
                <h3 class="text-center mb-4">Suivez-nous sur les réseaux sociaux</h3>
                <p class="text-center text-muted mb-4">
                  Restez connecté avec Digit-Ivoire sur les réseaux sociaux pour les dernières mises à jour, 
                  annonces et astuces concernant vos démarches administratives.
                </p>
                
                <div class="row g-4 justify-content-center">
                  <!-- Facebook -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-scale">
                      <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" 
                             style="width: 70px; height: 70px; background-color: #4267B2; color: white">
                          <i class="fab fa-facebook-f fa-2x"></i>
                        </div>
                        <h4 class="card-title">Facebook</h4>
                        <p class="card-text text-muted">Suivez notre page pour les actualités et annonces</p>
                        <a href="#" class="btn btn-outline-dark mt-3 rounded-pill px-4"
                           style="border-color: #4267B2; color: #4267B2">
                          Visiter @DigitIvoire
                        </a>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Twitter -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-scale">
                      <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" 
                             style="width: 70px; height: 70px; background-color: #1DA1F2; color: white">
                          <i class="fab fa-twitter fa-2x"></i>
                        </div>
                        <h4 class="card-title">Twitter</h4>
                        <p class="card-text text-muted">Restez informé des dernières nouvelles</p>
                        <a href="#" class="btn btn-outline-dark mt-3 rounded-pill px-4"
                           style="border-color: #1DA1F2; color: #1DA1F2">
                          Visiter @DigitIvoire
                        </a>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Instagram -->
                  <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-scale">
                      <div class="card-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" 
                             style="width: 70px; height: 70px; background-color: #E1306C; color: white">
                          <i class="fab fa-instagram fa-2x"></i>
                        </div>
                        <h4 class="card-title">Instagram</h4>
                        <p class="card-text text-muted">Découvrez notre quotidien en images</p>
                        <a href="#" class="btn btn-outline-dark mt-3 rounded-pill px-4"
                           style="border-color: #E1306C; color: #E1306C">
                          Visiter @DigitIvoire
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Contact Channels -->
            <div class="row g-4 mt-5">
              <div class="col-12">
                <h3 class="text-center mb-4">Autres moyens de nous contacter</h3>
              </div>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="mb-3">
                      <i class="fab fa-whatsapp fa-3x" style="color: #25D366"></i>
                    </div>
                    <h4 class="card-title mb-2">WhatsApp</h4>
                    <p class="card-text mb-3">+225 XX XX XX XX</p>
                    <a href="https://wa.me/225XXXXXXXX" 
                       class="btn w-100 text-white" 
                       style="background-color: #25D366"
                       target="_blank" 
                       rel="noopener noreferrer">
                      Discuter sur WhatsApp
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="mb-3">
                      <i class="fab fa-telegram fa-3x" style="color: #0088cc"></i>
                    </div>
                    <h4 class="card-title mb-2">Telegram</h4>
                    <p class="card-text mb-3">@DigitIvoire</p>
                    <a href="https://t.me/DigitIvoire" 
                       class="btn w-100 text-white" 
                       style="background-color: #0088cc"
                       target="_blank" 
                       rel="noopener noreferrer">
                      Nous contacter
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                  <div class="card-body p-4 text-center">
                    <div class="mb-3">
                      <i class="fas fa-headset fa-3x" style="color: #3b5d50"></i>
                    </div>
                    <h4 class="card-title mb-2">Support en ligne</h4>
                    <p class="card-text mb-3">Chat en direct</p>
                    <a href="#" 
                       class="btn w-100 text-white" 
                       style="background-color: #3b5d50">
                      Démarrer un chat
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Business Hours -->
            <div class="row mt-5">
              <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                  <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                      <i class="far fa-clock text-primary me-2"></i>
                      <h4 class="card-title m-0">Nos heures d'ouverture</h4>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                          <li class="list-group-item d-flex justify-content-between">
                            <span>Lundi - Vendredi:</span>
                            <span class="fw-bold">08:00 - 18:00</span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between">
                            <span>Samedi:</span>
                            <span class="fw-bold">09:00 - 14:00</span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between">
                            <span>Dimanche:</span>
                            <span class="fw-bold">Fermé</span>
                          </li>
                        </ul>
                      </div>
                      <div class="col-md-6">
                        <div class="alert alert-info mb-0 h-100 d-flex align-items-center">
                          <div>
                            <p class="mb-1 fw-bold">Support en ligne 24/7</p>
                            <p class="mb-0 small">Notre équipe en ligne est disponible pour vous assister en dehors des heures d'ouverture via email ou chat.</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

 @endsection