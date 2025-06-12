<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Mot de passe oublié - Mon Application</title>

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
      max-width: 430px;
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

    .login-header {
      padding: 30px 30px 0;
      text-align: center;
    }

    .login-header .logo {
      font-size: 32px;
      color: var(--primary);
      margin-bottom: 10px;
    }

    .login-body {
      padding: 20px 30px 30px;
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

    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
      border-radius: 8px;
      padding: 0.75rem 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: var(--primary-dark);
      border-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    .back-to-login-link {
      text-align: center;
      font-size: 0.95rem;
      color: var(--neutral-700);
    }

    .back-to-login-link a {
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
    }

    .back-to-login-link a:hover {
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

<div class="login-container">
  <div class="login-card">
    <div class="login-header">
      <div class="logo">
        <i class="fas fa-lock"></i> <!-- Changed icon to a lock -->
      </div>
      <h1 class="login-title">Mot de passe oublié ?</h1>
      <p class="login-subtitle">Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
    </div>

    <div class="login-body">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

      <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Adresse email</label>
          <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            autofocus
            placeholder="ex: utilisateur@example.com"
          >
          @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <!-- Bouton d'envoi -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
        </div>

        <!-- Lien retour à la connexion -->
        <div class="back-to-login-link mt-3">
          <a href="{{ route('login') }}">Retour à la connexion</a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
