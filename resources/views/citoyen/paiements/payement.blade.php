@extends('layouts.app')

@section('content')

<html>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow">
          <div class="card-body">
            <h4 class="card-title text-center mb-4">Paiement de la demande</h4>
            
            <form id="paymentForm">
              <div class="mb-3">
                <label class="form-label">Choisissez un moyen de paiement :</label>
                <div class="row g-3">
                  <div class="col-4">
                    <div class="payment-method text-center" data-method="orange">
                      <img src="/img/Orange Money.jpg" alt="Orange Money" class="payment-logo mb-2">
                      <p class="mb-0">Orange</p>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="payment-method text-center" data-method="mtn">
                      <img src="/img/MTN momo.jpg" alt="MTN Money" class="payment-logo mb-2">
                      <p class="mb-0">MTN</p>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="payment-method text-center" data-method="wave">
                      <img src="/img/logo wave.jpg" alt="Wave" class="payment-logo mb-2">
                      <p class="mb-0">Wave</p>
                    </div>
                  </div>
                </div>
              </div>

              <input type="hidden" name="selected_method" id="selectedMethod">

              <div class="mb-3">
                <label for="phoneNumber" class="form-label">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phone" placeholder="Ex : 0700000000" required>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-success">Procéder au paiement</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const methods = document.querySelectorAll('.payment-method');
    const selectedMethodInput = document.getElementById('selectedMethod');

    methods.forEach(method => {
      method.addEventListener('click', () => {
        methods.forEach(m => m.classList.remove('active'));
        method.classList.add('active');
        selectedMethodInput.value = method.getAttribute('data-method');
      });
    });

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
      e.preventDefault();
      if (!selectedMethodInput.value) {
        alert('Veuillez sélectionner un moyen de paiement.');
        return;
      }
      alert(`Paiement en cours via ${selectedMethodInput.value.toUpperCase()} pour le numéro ${document.getElementById('phoneNumber').value}`);
      // Ici vous pouvez ajouter votre intégration API ou redirection.
    });
  </script>
</body>
</html>
@endsection