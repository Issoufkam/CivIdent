
@extends('layouts.app')
@section('content')
 <!--Debut de contenu -->

 <!-- Hero Section -->
 <div class="hero text-white" >
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-5 mb-5 mb-lg-0">
          <h1 class="display-4 fw-bold mb-4">
            Vos documents officiels, <span class="d-block">en quelques clics !</span>
          </h1>
          <p class="lead mb-4">
            Rejoignez-nous dès maintenant en créant votre compte ou en vous connectant pour profiter de tous les services.
          </p>
          @guest
          <div class="d-flex gap-3">
              <a href="{{ route('login') }}" class="btn btn-dark">Connexion</a>
              <a href="{{ route('register') }}" class="btn btn-warning">Inscription</a>
          </div>
          @endguest

        </div>
        <div class="col-lg-7 ps-5">
          <div class="hero-img-wrap">
            <img src="img/joliebg.png" alt="Hero image" class="img-fluid">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Why Choose Us Section -->
  <div class="why-choose-section bg-white py-5">
    <div class="container">
      <div class="row justify-content-between">
        <div class="col-lg-6">
          <h2 class="h1 mb-4">Pourquoi nous choisir ?</h2>
          <p class="lead mb-5">Notre plateforme vous offre une solution rapide, sécurisée et fiable pour obtenir vos documents officiels.</p>

          <div class="row g-4">
            <div class="col-md-6">
              <div class="feature">
                <div class="icon">
                  <img src="img/checked.png" alt="Livraison" class="img-fluid">
                </div>
                <h3 class="h5">Disponibilité rapide</h3>
                <p>Vos documents administratifs seront disponible en moins de 24h</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature">
                <div class="icon">
                  <img src="img/google-docs.png" alt="Documents" class="img-fluid" style="width: 40px;">
                </div>
                <h3 class="h5">Documents vérifiés</h3>
                <p>Tous nos documents sont authentifiés et sécurisés.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature">
                <div class="icon">
                  <img src="img/customer-support.png" alt="Support" class="img-fluid">
                </div>
                <h3 class="h5">Support 24/7</h3>
                <p>Une équipe disponible pour vous assister à tout moment.</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="feature">
                <div class="icon">
                  <img src="img/need.png" alt="Demande" class="img-fluid" style="width: 50px;">
                </div>
                <h3 class="h5">Suivi simplifié</h3>
                <p>Suivez l'état de vos demandes en temps réel.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 mt-5 mt-lg-0">
          <div class="img-wrap">
            <img src="img/ivory.jpeg" alt="Côte d'Ivoire" class="img-fluid rounded-3 shadow">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Testimonials Section -->
<div class="testimonial-section py-5 bg-light">
    <div class="container">
      <h2 class="text-center h1 mb-5">Témoignages</h2>

      <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

          <!-- Témoignage 1 -->
          <div class="carousel-item active">
            <div class="testimonial-block text-center">
              <p class="lead mb-4">
                "J'avais fait ma demande d'état civil en ligne, et en quelques jours seulement, j'ai reçu mon extrait de naissance directement en format PDF."
              </p>
              <div class="author-info">
                <img src="img/identité4.jpg" alt="Fatou Coulibaly" class="mb-3 rounded-circle" width="80" height="80">
                <h3 class="h5">Fatou Coulibaly</h3>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>

                </div>
              </div>
            </div>
          </div>

          <!-- Témoignage 2 -->
          <div class="carousel-item">
            <div class="testimonial-block text-center">
              <p class="lead mb-4">
                "Un service exceptionnel ! Rapide, clair et efficace. Je recommande vivement."
              </p>
              <div class="author-info">
                <img src="img/identité3.jpg" alt="Jean Koffi" class="mb-3 rounded-circle" width="80" height="80">
                <h3 class="h5">Jean Koffi</h3>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star-half-alt"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Témoignage 3 -->
          <div class="carousel-item">
            <div class="testimonial-block text-center">
              <p class="lead mb-4">
                "Grâce à votre plateforme, j'ai économisé du temps et de l'argent. Bravo pour cette belle initiative !"
              </p>
              <div class="author-info">
                <img src="img/identité5.jpg" alt="Awa Traoré" class="mb-3 rounded-circle" width="80" height="80">
                <h3 class="h5">Awa Traoré</h3>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
          </div>


          <div class="carousel-item">
            <div class="testimonial-block text-center">
              <p class="lead mb-4">
                "ouais c'est faciiiiiiiiile !"
              </p>
              <div class="author-info">
                <img src="img/identité2.jpg" alt="Awa Traoré" class="mb-3 rounded-circle" width="80" height="80">
                <h3 class="h5">Diomandé edmond</h3>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="carousel-item">
            <div class="testimonial-block text-center">
              <p class="lead mb-4">
                "trop perfomant cette plateforme!"
              </p>
              <div class="author-info">
                <img src="img/identité1.jpg" alt="Awa Traoré" class="mb-3 rounded-circle" width="80" height="80">
                <h3 class="h5">Fanck amani</h3>
                <div class="text-warning">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Contrôles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
          <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
          <span class="visually-hidden">Suivant</span>
        </button>
      </div>

    </div>
  </div>


  <!-- Start Blog Section -->
  <div class="blog-section bg-white">
    <div class="container">
        <h1 class="mb-5">Demander un acte</h1>
        <div class="row">

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="img/doc (3).jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}">Extrait de naissance</a></h3>
                        <div class="meta">
                            <span>Notes <a href="}"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>


                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="img/doc.jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}">Acte de mariage</a></h3>
                        <div class="meta">
                            <span>Notes <a href="#"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>


                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="img/doc4.jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}"> Acte de décès</a></h3>
                        <div class="meta">
                            <span>Notes <a href="#"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <h1 class="mb-5">Faire une demande de certificat</h1>

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="./img/img1.jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}">Certificat de vie</a></h3>
                        <div class="meta">
                            <span>Notes <a href="#"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="../img/dossier 1.jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}">Certification d'entretien</a></h3>
                        <div class="meta">
                            <span>Notes <a href="#"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>


                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4 mb-5">
                <div class="post-entry">
                    <a href="{{ route('login') }}" class="post-thumbnail"><img src="img/doc 6.jpeg" alt="Image" class="img-fluid"></a>
                    <div class="post-content-entry">
                        <h3><a href="{{ route('login') }}">Certification de non revenu </a></h3>
                        <div class="meta">
                            <span>Notes <a href="#"></a></span>
                            <span>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>
                                <i class="fa fa-star text-warning"></i>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Blog Section -->

<!-- Fin de contenu -->

@endsection
