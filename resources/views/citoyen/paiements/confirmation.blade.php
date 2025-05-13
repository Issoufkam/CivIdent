@extends('layouts.app')

@section('content')
<html lang="fr">
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card text-center shadow">
          <div class="card-body">
            <img src="{{ asset('img/validééééé.jpg') }}" alt="success">
            <h3 class="card-title">Paiement Réussi !</h3>
            <p class="lead">Merci ! Votre demande d’état civil a été enregistrée.</p>

            <hr>

            <p><strong>Mode de paiement :</strong> <span id="methodePaiement">MTN</span></p>
            <p><strong>Numéro utilisé :</strong> <span id="numeroTelephone">0700000000</span></p>

            <div class="d-grid gap-2 mt-4">
              <a href="index.html" class="btn btn-primary">Retour à l'accueil</a>
              <a href="demande.html" class="btn btn-outline-success">Faire une autre demande</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Simuler l'envoi depuis le formulaire précédent (à remplacer par des valeurs dynamiques dans un vrai système)
    const params = new URLSearchParams(window.location.search);
    document.getElementById('methodePaiement').textContent = params.get('methode')?.toUpperCase() || 'N/A';
    document.getElementById('numeroTelephone').textContent = params.get('numero') || 'N/A';
  </script>
</body>
</html>
@endsection