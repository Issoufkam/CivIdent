@extends('layouts.app')
@section('content')
<div class="container main-container py-4">
    <h1 class="text-center mb-4 fade-in">Demande d'Acte Extrait de Naissance</h1>
    
    <div class="form-container fade-in">
      <div class="row g-0">
        <!-- Left Side - Image and Text -->
        <div class="col-md-5">
          <div class="hero-section h-100">
            <img src="../img/signature.jpg" alt="Documents officiels" class="hero-img">
            <div class="hero-content">
              <h2 class="mb-4">Obtenez vos documents officiels</h2>
              <p class="mb-4">Simplifiez vos démarches administratives en effectuant votre demande en ligne.</p>
              
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <span>Rapide et sécurisé</span>
              </div>
              
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <span>Traitement prioritaire</span>
              </div>
              
              <div class="feature-item">
                <div class="feature-icon">
                  <i class="fas fa-check"></i>
                </div>
                <span>Support client disponible</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Right Side - Form -->
        <div class="col-md-7">
          <div class="form-section">
            <!-- Success Message (initially hidden) -->
            <div id="successMessage" class="success-message">
              <i class="fas fa-check-circle success-icon"></i>
              <h3 class="mb-3">Demande Envoyée !</h3>
              <p class="text-muted">Votre demande a été soumise avec succès. Vous recevrez bientôt un email de confirmation.</p>
            </div>
            
            <!-- Form Content -->
            <form id="birthCertForm">
              <!-- Personal Information -->
              <div class="mb-4">
                <h3 class="section-title mb-3">Informations Personnelles</h3>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control" name="lastName" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Prénoms</label>
                    <input type="text" class="form-control" name="firstName" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" name="phone" required>
                  </div>
                </div>
              </div>

              <!-- Birth Information -->
              <div class="mb-4">
                <h3 class="section-title mb-3">Informations de Naissance</h3>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Date de Naissance</label>
                    <input type="date" class="form-control" name="dateOfBirth" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Lieu de Naissance</label>
                    <input type="text" class="form-control" name="placeOfBirth" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Sous Préfecture (S/P)</label>
                    <input type="text" class="form-control" name="subPrefecture" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Genre</label>
                    <select class="form-select" name="gender" required>
                      <option value="">Sélectionnez...</option>
                      <option value="M">Masculin</option>
                      <option value="F">Féminin</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nom complet du Père</label>
                    <input type="text" class="form-control" name="fatherName" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nom complet de la Mère</label>
                    <input type="text" class="form-control" name="motherName" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">N° du registre</label>
                    <input type="number" class="form-control" name="registerNumber" required>
                  </div>
                </div>
              </div>

              <!-- Request Details -->
              <div class="mb-4">
                <h3 class="section-title mb-3">Détails de la Demande</h3>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Motif de la Demande</label>
                    <select class="form-select" name="purpose" required>
                      <option value="">Sélectionnez...</option>
                      <option value="administrative">Usage Administratif</option>
                      <option value="school">Inscription Scolaire</option>
                      <option value="employment">Emploi</option>
                      <option value="other">Autre</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Nombre de Copies</label>
                    <select class="form-select" name="copies" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Pièce d'identité (recto)</label>
                    <input type="file" class="form-control" name="idFront" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Pièce d'identité (verso)</label>
                    <input type="file" class="form-control" name="idBack" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Une photo d'extrait bien lisible (pour vérification)</label>
                    <input type="file" class="form-control" name="birthCopy" required>
                  </div>
                </div>
              </div>

              <!-- Terms and Submit -->
              <div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="terms" required>
                  <label class="form-check-label" for="terms">
                    Je certifie que les informations fournies sont exactes
                  </label>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Soumettre la Demande</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>



@endsection
