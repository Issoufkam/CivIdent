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
  <h1 class="text-center mb-4 fade-in">Demande d'Acte Extrait de Naissance</h1>

  <div class="form-container fade-in">
    <div class="row g-4 flex-column-reverse flex-md-row">

      <div class="col-12 col-md-7">
        <div class="form-section px-2 px-md-4">

          <form id="birthCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="naissance">

            <!-- Choix type de demande -->
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

            <!-- Formulaire complet (nouvelle demande) -->
            <div id="newRequestFields">
              <h3 class="section-title mb-3">Informations Personnelles</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="nom_enfant">Nom</label>
                  <input type="text" class="form-control" name="metadata[nom_enfant]" id="nom_enfant" required value="{{ old('metadata.nom_enfant') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="prenom_enfant">Prénoms</label>
                  <input type="text" class="form-control" name="metadata[prenom_enfant]" id="prenom_enfant" required value="{{ old('metadata.prenom_enfant') }}">
                </div>
              </div>

              <h3 class="section-title mb-3">Informations de Naissance</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="date_naissance">Date de Naissance</label>
                  <input type="date" class="form-control" name="metadata[date_naissance]" id="date_naissance" required value="{{ old('metadata.date_naissance') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="lieu_naissance">Lieu de Naissance</label>
                  <input type="text" class="form-control" name="metadata[lieu_naissance]" id="lieu_naissance" required value="{{ old('metadata.lieu_naissance') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="sexe">Genre</label>
                  <select class="form-select" name="metadata[sexe]" id="sexe" required>
                    <option value="">Sélectionnez...</option>
                    <option value="M" {{ old('metadata.sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                    <option value="F" {{ old('metadata.sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                  </select>
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

              <h3 class="section-title mb-3">Informations des Parents</h3>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label" for="nom_pere">Nom complet du Père</label>
                  <input type="text" class="form-control" name="metadata[nom_pere]" id="nom_pere" required value="{{ old('metadata.nom_pere') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nationalite_pere">Nationalité du Père</label>
                  <input type="text" class="form-control" name="metadata[nationalite_pere]" id="nationalite_pere" required value="{{ old('metadata.nationalite_pere') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nom_mere">Nom complet de la Mère</label>
                  <input type="text" class="form-control" name="metadata[nom_mere]" id="nom_mere" required value="{{ old('metadata.nom_mere') }}">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nationalite_mere">Nationalité de la Mère</label>
                  <input type="text" class="form-control" name="metadata[nationalite_mere]" id="nationalite_mere" required value="{{ old('metadata.nationalite_mere') }}">
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

            <!-- Champs pour duplicata -->
            <div id="duplicataFields" style="display:none;">
              <h3 class="section-title mb-3">Duplicata - Renseignez le numéro du registre</h3>
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
            <h2 class="mb-4">Obtenez votre acte de naissance</h2>
            <p class="mb-4">Document officiel nécessaire pour toutes vos démarches administratives.</p>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Légal et authentique
            </div>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Reconnu par toutes les administrations
            </div>
            <div class="feature-item">
              <i class="fas fa-check me-2 text-success"></i> Livraison sous 72h
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

        // Enlever les required sur champs nouvelle demande
        newRequestFields.querySelectorAll('input, select').forEach(input => {
          input.removeAttribute('required');
        });
        // Rendre obligatoire le numéro de registre pour duplicata
        duplicataFields.querySelector('input[name="registry_number"]').setAttribute('required', 'required');
      } else {
        newRequestFields.style.display = 'block';
        duplicataFields.style.display = 'none';

        // Remettre required sur champs nouvelle demande
        newRequestFields.querySelectorAll('input, select').forEach(input => {
          input.setAttribute('required', 'required');
        });
        // Retirer required sur duplicata
        duplicataFields.querySelector('input[name="registry_number"]').removeAttribute('required');
      }
    }

    newRequestRadio.addEventListener('change', toggleFields);
    duplicataRadio.addEventListener('change', toggleFields);

    toggleFields(); // Initialisation
  });
</script>

@endsection
