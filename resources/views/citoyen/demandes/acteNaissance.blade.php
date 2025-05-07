@extends('layouts.app')
@section('content')


<div class="container main-container py-4">
  <h1 class="text-center mb-4 fade-in">Demande d'Acte Extrait de Naissance</h1>

  <div class="form-container fade-in">
    <div class="row g-4 flex-column-reverse flex-md-row">
      <!-- Right Side - Form (affiché en haut sur mobile) -->
      <div class="col-12 col-md-7">
        <div class="form-section px-2 px-md-4">
          <form id="birthCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Informations personnelles -->
            <div class="mb-4">
              <h3 class="section-title mb-3">Informations Personnelles</h3>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="lastName" class="form-label">Nom</label>
                  <input type="text" id="lastName" class="form-control" name="lastName" autocomplete="family-name" required>
                </div>
                <div class="col-md-6">
                  <label for="firstName" class="form-label">Prénoms</label>
                  <input type="text" id="firstName" class="form-control" name="firstName" autocomplete="given-name" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" id="email" class="form-control" name="email" autocomplete="email" required>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Téléphone</label>
                  <input type="tel" id="phone" class="form-control" name="phone" autocomplete="tel" required>
                </div>
              </div>
            </div>

            <!-- Informations de naissance -->
            <div class="mb-4">
              <h3 class="section-title mb-3">Informations de Naissance</h3>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="dateOfBirth" class="form-label">Date de Naissance</label>
                  <input type="date" id="dateOfBirth" class="form-control" name="dateOfBirth" autocomplete="bday" required>
                </div>
                <div class="col-md-6">
                  <label for="placeOfBirth" class="form-label">Lieu de Naissance</label>
                  <input type="text" id="placeOfBirth" class="form-control" name="placeOfBirth" autocomplete="address-level2" required>
                </div>
                <div class="col-md-6">
                  <label for="subPrefecture" class="form-label">Sous Préfecture (S/P)</label>
                  <input type="text" id="subPrefecture" class="form-control" name="subPrefecture" autocomplete="address-level1" required>
                </div>
                <div class="col-md-6">
                  <label for="gender" class="form-label">Genre</label>
                  <select id="gender" class="form-select" name="gender" required>
                    <option value="">Sélectionnez...</option>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="fatherName" class="form-label">Nom complet du Père</label>
                  <input type="text" id="fatherName" class="form-control" name="fatherName" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                  <label for="motherName" class="form-label">Nom complet de la Mère</label>
                  <input type="text" id="motherName" class="form-control" name="motherName" autocomplete="off" required>
                </div>
                <div class="col-md-6">
                  <label for="registerNumber" class="form-label">N° du registre</label>
                  <input type="number" id="registerNumber" class="form-control" name="registerNumber" autocomplete="off" required>
                </div>
              </div>
            </div>

            <!-- Détails de la demande -->
            <div class="mb-4">
              <h3 class="section-title mb-3">Détails de la Demande</h3>
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="purpose" class="form-label">Motif de la Demande</label>
                  <select id="purpose" class="form-select" name="purpose" required>
                    <option value="">Sélectionnez...</option>
                    <option value="administrative">Usage Administratif</option>
                    <option value="school">Inscription Scolaire</option>
                    <option value="employment">Emploi</option>
                    <option value="other">Autre</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="copies" class="form-label">Nombre de Copies</label>
                  <select id="copies" class="form-select" name="copies" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                  </select>
                </div>
                <div class="col-12">
                  <label for="idFront" class="form-label">Pièce d'identité (recto)</label>
                  <input type="file" id="idFront" class="form-control" name="idFront" required>
                </div>
                <div class="col-12">
                  <label for="idBack" class="form-label">Pièce d'identité (verso)</label>
                  <input type="file" id="idBack" class="form-control" name="idBack" required>
                </div>
                <div class="col-12">
                  <label for="birthCopy" class="form-label">Photo de l'extrait de naissance</label>
                  <input type="file" id="birthCopy" class="form-control" name="birthCopy" required>
                </div>
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

      <!-- Left Side - Image and Text (affiché en bas sur mobile) -->
      <div class="col-12 col-md-5 mb-4 mb-md-0">
        <div class="hero-section h-100 text-center">
          <img src="/img/signature.jpg" alt="Documents officiels" class="hero-img">
          <div class="hero-content">
            <h2 class="mb-4">Obtenez vos documents officiels</h2>
            <p class="mb-4">Simplifiez vos démarches administratives en effectuant votre demande en ligne.</p>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Rapide et sécurisé
            </div>
            <div class="feature-item mb-2">
              <i class="fas fa-check me-2 text-success"></i> Traitement prioritaire
            </div>
            <div class="feature-item">
              <i class="fas fa-check me-2 text-success"></i> Support client disponible
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection
