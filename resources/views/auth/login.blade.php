<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Connexion - Mon Application</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

  <style>
    :root {
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #7209b7;
      --accent: #f72585;
      --success: #38b000;
      --warning: #f9c74f;
      --error: #d90429;
      --light: #f8f9fa;
      --dark: #212529;
      --neutral-100: #f8f9fa;
      --neutral-200: #e9ecef;
      --neutral-300: #dee2e6;
      --neutral-400: #ced4da;
      --neutral-500: #adb5bd;
      --neutral-600: #6c757d;
      --neutral-700: #495057;
      --neutral-800: #343a40;
      --neutral-900: #212529;
    }

    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      max-width: 900px; /* Ajusté pour l'image et le formulaire */
      width: 100%;
      margin: 0 auto;
    }

    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .form-box {
      padding: 30px;
    }

    .login-title {
      font-weight: 600;
      color: var(--neutral-800);
      margin-bottom: 0.5rem;
    }

    .login-subtitle {
      color: var(--neutral-600);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .form-label {
      font-weight: 500;
      color: var(--neutral-700);
    }

    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1.5px solid var(--neutral-300);
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }

    .btn-dark { /* Utilisé pour le bouton de connexion */
      background-color: var(--dark);
      border-color: var(--dark);
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
      color: white;
    }

    .btn-dark:hover {
      background-color: var(--neutral-800);
      border-color: var(--neutral-800);
      transform: translateY(-2px);
    }

    .text-success { /* Utilisé pour les liens */
      color: var(--success) !important;
    }

    .text-success:hover {
      text-decoration: underline;
    }

    .invalid-feedback {
      color: var(--error);
      font-size: 0.85rem;
      margin-top: 0.25rem;
    }

    .is-invalid {
      border-color: var(--error) !important;
    }

    .alert {
      margin-bottom: 1rem;
      padding: 0.75rem 1.25rem;
      border-radius: 0.5rem;
      display: flex;
      align-items: center;
    }
    .alert-dismissible .btn-close {
        padding: 0.75rem 1.25rem;
    }
  </style>
</head>
<body>

<div class="container py-5 login-container">
    <div class="row justify-content-center align-items-center login-card">
        <!-- Colonne image -->
        <div class="col-md-6 d-none d-md-block">
            <img src="{{ asset('img/img20.jpg') }}" alt="Illustration" class="img-fluid rounded-start">
        </div>

        <!-- Colonne formulaire -->
        <div class="col-md-6">
            <div class="form-box p-4">
                <h4 class="text-center mb-4">Se connecter</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone</label>
                        <input type="text" name="telephone" class="form-control" id="telephone" placeholder="07XXXXXXXX" required value="{{ old('telephone') }}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="********" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Connexion</button>

                    <p class="text-center mt-3">
                        <a class="text-success" href="{{ route('register') }}">Inscrivez-vous ici</a>
                    </p>

                    <p class="text-center">
                        {{-- Lien mis à jour pour pointer vers votre route de réinitialisation par téléphone --}}
                        <a class="text-success" href="{{ route('password.phone.request') }}">Mot de passe oublié ?</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
