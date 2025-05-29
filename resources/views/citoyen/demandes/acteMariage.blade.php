@extends('layouts.app')

@if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@section('content')

<div class="container main-container py-4">
  <h1 class="text-center mb-4 fade-in">Demande d'Acte de Mariage</h1>

  <div class="form-container fade-in">
    <div class="row g-4 flex-column-reverse flex-md-row">

      <div class="col-12 col-md-7">
        <div class="form-section px-2 px-md-4">

          <form method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="mariage">

            <!-- Type de demande -->
            <div class="mb-4">
              <h3 class="section-title mb-3">Type de Demande</h3>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_duplicata" id="newRequest" value="0" checked>
                <label class="form-check-label" for="newRequest">Nouvelle Demande</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_duplicata" id="duplicataRequest" value="1">
                <label class="form-check-label" for="duplicataRequest">Duplicata</label>
              </div>
            </div>

            <!-- Champs nouvelle demande -->
            <div id="newRequestFields">
              <h3 class="section-title mb-3">Informations du Mariage</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="date_mariage">Date du Mariage</label>
                  <input type="date" class="form-control" name="metadata[date_mariage]" id="date_mariage" required value="{{ old('metadata.date_mariage') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="lieu_mariage">Lieu du Mariage</label>
                  <input type="text" class="form-control" name="metadata[lieu_mariage]" id="lieu_mariage" required value="{{ old('metadata.lieu_mariage') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="commune_id">Commune ou Sous-Préfecture</label>
                  <select class="form-select" name="commune_id" id="commune_id" required>
                    <option value="">Sélectionnez votre commune ou sous-préfecture</option>
                    @foreach ($communes as $commune)
                      <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                        {{ $commune->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <h3 class="section-title mb-3">Informations des Époux</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="nom_epoux">Nom complet de l'Époux</label>
                  <input type="text" class="form-control" name="metadata[nom_epoux]" id="nom_epoux" required value="{{ old('metadata.nom_epoux') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nationalite_epoux">Nationalité de l'Époux</label>
                  <input type="text" class="form-control" name="metadata[nationalite_epoux]" id="nationalite_epoux" required value="{{ old('metadata.nationalite_epoux') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nom_epouse">Nom complet de l'Épouse</label>
                  <input type="text" class="form-control" name="metadata[nom_epouse]" id="nom_epouse" required value="{{ old('metadata.nom_epouse') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nationalite_epouse">Nationalité de l'Épouse</label>
                  <input type="text" class="form-control" name="metadata[nationalite_epouse]" id="nationalite_epouse" required value="{{ old('metadata.nationalite_epouse') }}">
                </div>
              </div>

              <h3 class="section-title mb-3">Pièces Justificatives</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="justificatif">Pièce jointe</label>
                  <input type="file" class="form-control" name="justificatif" id="justificatif" required>
                </div>
              </div>
            </div>

            <!-- Champs duplicata -->
            <div id="duplicataFields" style="display:none;">
              <h3 class="section-title mb-3">Duplicata - N° du registre</h3>
              <div class="mb-3">
                <label class="form-label" for="registry_number">N° du registre</label>
                <input type="text" class="form-control" name="registry_number" id="registry_number" placeholder="Numéro du registre" value="{{ old('registry_number') }}">
              </div>
              <div class="mb-3">
                <label class="form-label" for="idFront">Pièce d'identité (recto)</label>
                <input type="file" class="form-control" name="idFront" id="idFront">
              </div>
            </div>

            <!-- Validation -->
            <div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label" for="terms">
                  Je certifie que les informations fournies sont exactes
                </label>
              </div>
              <button type="submit" class="btn btn-dark w-100 py-2">Soumettre la Demande</button>
            </div>

          </form>
        </div>
      </div>

      <!-- Illustration -->
      <div class="col-12 col-md-5 mb-4 mb-md-0">
        <div class="hero-section h-100 text-center">
          <img src="{{ asset('img/signature.jpg') }}" alt="Documents officiels" class="hero-img">
          <div class="hero-content">
            <h2 class="mb-4">Obtenez votre acte de mariage</h2>
            <p class="mb-4">Document légal pour officialiser votre union civile.</p>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Document authentique
            </div>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Utilisable pour toutes démarches
            </div>
            <div class="feature-item">
              <i class="fas fa-check me-2 text-success"></i> Délai de traitement rapide
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const newRequestRadio = document.getElementById('newRequest');
    const duplicataRadio = document.getElementById('duplicataRequest');
    const newRequestFields = document.getElementById('newRequestFields');
    const duplicataFields = document.getElementById('duplicataFields');

    function toggleFields() {
      if (duplicataRadio.checked) {
        newRequestFields.style.display = 'none';
        duplicataFields.style.display = 'block';
        newRequestFields.querySelectorAll('input, select').forEach(input => input.removeAttribute('required'));
        duplicataFields.querySelector('input[name="registry_number"]').setAttribute('required', 'required');
      } else {
        newRequestFields.style.display = 'block';
        duplicataFields.style.display = 'none';
        newRequestFields.querySelectorAll('input, select').forEach(input => input.setAttribute('required', 'required'));
        duplicataFields.querySelector('input[name="registry_number"]').removeAttribute('required');
      }
    }

    newRequestRadio.addEventListener('change', toggleFields);
    duplicataRadio.addEventListener('change', toggleFields);

    toggleFields();
  });
</script>

@endsection
