<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Digit-Ivoire">
  <link rel="shortcut icon" href="{{ asset('img/ivory.jpeg') }}">

  <meta name="description" content="Digit-Ivoire - Obtenez vos documents officiels en quelques clics">
  <meta name="keywords" content="documents officiels, Côte d'Ivoire, services administratifs">

  <title>Digit-Ivoire</title>

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('css/register.css') }}">
  <link rel="stylesheet" href="{{ asset('css/apropos.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/acteNaissance.css') }}">
  <link rel="stylesheet" href="{{ asset('css/acteDécès.css') }}">
  <!--liens boostrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark" id="entete">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ route('accueil') }}">
        Digit-<span class="fw-bold">Ivoire</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse fw-bold" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('accueil') }}">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('propos') }}">À propos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('contact') }}">Contacts</a>
          </li>
        @guest
            <li class="nav-item ms-lg-3">
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-user"></i>
                </a>
            </li>
        @endguest
          @auth
              @php
                $role = Auth::user()->getRoleNames()->first();
                $dashboardRoute = match($role) {
                    'admin' => route('admin.dashboard'),
                    'agent' => route('agent.dashboard'),
                    'citoyen' => route('citoyen.dashboard'),
                    default => '#'
                };
              @endphp

              <li class="nav-item ms-lg-3">
                  <a class="nav-link" href="{{ $dashboardRoute }}">
                       Mon Espace
                  </a>
              </li>

              <li class="nav-item ms-lg-3">
                  <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <i class="fas fa-sign-out-alt"></i> Se déconnecter
                  </a>
              </li>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
          @endauth
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenu principal -->
  <!-- Main Content -->
  <main class="flex-grow-1 py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-section bg-dark text-light pt-5" id="contact">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <h2 class="h4 text-white mb-4">Contactez-nous !</h2>
                    <p class="text-white-50 mb-4">Notre équipe est à votre disposition pour répondre à toutes vos questions.</p>
                    <div class="custom-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row">
                        <div class="col-6 col-sm-4">
                            <h3 class="h6 text-white mb-3">Navigation</h3>
                            <ul class="list-unstyled">
                                <li><a href="#about" class="text-white-50">À propos</a></li>
                                <li><a href="{{ route('login') }}" class="text-white-50">Connexion</a></li>
                                <li><a href="#contact" class="text-white-50">Contact</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-4">
                            <h3 class="h6 text-white mb-3">Support</h3>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white-50">Centre d'aide</a></li>
                                <li><a href="#" class="text-white-50">Chat en direct</a></li>
                                <li><a href="#" class="text-white-50">FAQ</a></li>
                            </ul>
                        </div>
                        <div class="col-6 col-sm-4">
                            <h3 class="h6 text-white mb-3">Légal</h3>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-white-50">Conditions d'utilisation</a></li>
                                <li><a href="#" class="text-white-50">Confidentialité</a></li>
                                <li><a href="#" class="text-white-50">Mentions légales</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <h5 class="mb-3 fw-bold text-white">Contact</h5>
                    <p class="mb-2 text-white-50"><i class="fas fa-map-marker-alt me-2"></i>Abidjan, Côte d'Ivoire</p>
                    <p class="mb-2 text-white-50"><i class="fas fa-phone me-2"></i>+225 XX XX XX XX</p>
                    <p class="mb-2 text-white-50"><i class="fas fa-envelope me-2"></i>contact@digit-ivoire.ci</p>
                </div>
            </div>

            <div class="border-top border-secondary pt-4">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="text-white-50 mb-0">
                            &copy; <script>document.write(new Date().getFullYear());</script> Tous droits réservés — Digit-Ivoire
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item"><a href="#" class="text-white-50">Conditions</a></li>
                            <li class="list-inline-item ms-3"><a href="#" class="text-white-50">Confidentialité</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
