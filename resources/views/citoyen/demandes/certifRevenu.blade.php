@extends('layouts.app')

@section('content')

<body>
<header class="header text-center">
    <div class="container">
      <img src="{{ asset('img/drapeau CI.jpg') }}"  alt="Drapeau de la Côte d'Ivoire" width="80" class="mb-3">
      <h1 class="display-6 fw-bold">République de Côte d'Ivoire</h1>
      <p class="lead">Union - Discipline - Travail</p>
      <h2 class="fs-4 fw-normal mt-3">Formulaire de Demande de Certificat de non revenu</h2>
    </div>
  </header>


  <div class="container py-5">
    <div class="form-container">
      
      <form id="nonRevenueForm" class="p-4 p-md-5" novalidate>
        <!-- Instructions -->
        <div class="alert alert-info mb-4" role="alert">
          <i class="fas fa-info-circle me-2"></i> Remplissez tous les champs marqués d'un astérisque (*). Assurez-vous que les informations fournies sont exactes et complètes.
        </div>
        
        <!-- Personal Information Section -->
        <div class="section">
          <h3 class="section-title">Informations Personnelles</h3>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="civilite" class="form-label required">Civilité</label>
              <select class="form-select" id="civilite" required>
                <option value="" selected disabled>Choisir...</option>
                <option value="M.">M.</option>
                <option value="Mme">Mme</option>
                <option value="Mlle">Mlle</option>
              </select>
              <div class="invalid-feedback">Veuillez sélectionner votre civilité.</div>
            </div>
            
            <div class="col-md-4">
              <label for="nom" class="form-label required">Nom de famille</label>
              <input type="text" class="form-control" id="nom" required>
              <div class="invalid-feedback">Veuillez saisir votre nom de famille.</div>
            </div>
            
            <div class="col-md-4">
              <label for="prenoms" class="form-label required">Prénoms</label>
              <input type="text" class="form-control" id="prenoms" required>
              <div class="invalid-feedback">Veuillez saisir vos prénoms.</div>
            </div>
            
            <div class="col-md-6">
              <label for="dateNaissance" class="form-label required">Date de naissance</label>
              <input type="date" class="form-control" id="dateNaissance" required>
              <div class="invalid-feedback">Veuillez saisir votre date de naissance.</div>
            </div>
            
            <div class="col-md-6">
              <label for="lieuNaissance" class="form-label required">Lieu de naissance</label>
              <input type="text" class="form-control" id="lieuNaissance" required>
              <div class="invalid-feedback">Veuillez saisir votre lieu de naissance.</div>
            </div>
            
            <div class="col-md-6">
              <label for="nationalite" class="form-label required">Nationalité</label>
              <input type="text" class="form-control" id="nationalite" value="Ivoirienne" required>
              <div class="invalid-feedback">Veuillez saisir votre nationalité.</div>
            </div>
            
            <div class="col-md-6">
              <label for="profession" class="form-label">Profession</label>
              <input type="text" class="form-control" id="profession">
            </div>
          </div>
        </div>
        
        <!-- Identification Section -->
        <div class="section">
          <h3 class="section-title">Pièces d'Identité</h3>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="typeIdentite" class="form-label required">Type de pièce d'identité</label>
              <select class="form-select" id="typeIdentite" required>
                <option value="" selected disabled>Choisir...</option>
                <option value="CNI">Carte Nationale d'Identité</option>
                <option value="Passeport">Passeport</option>
                <option value="PermisConduire">Permis de Conduire</option>
                <option value="Autre">Autre</option>
              </select>
              <div class="invalid-feedback">Veuillez sélectionner le type de pièce d'identité.</div>
            </div>
            
            <div class="col-md-6">
              <label for="numeroIdentite" class="form-label required">Numéro de pièce d'identité</label>
              <input type="text" class="form-control" id="numeroIdentite" required>
              <div class="invalid-feedback">Veuillez saisir le numéro de votre pièce d'identité.</div>
            </div>
            
            <div class="col-md-6">
              <label for="dateEmission" class="form-label required">Date d'émission</label>
              <input type="date" class="form-control" id="dateEmission" required>
              <div class="invalid-feedback">Veuillez saisir la date d'émission.</div>
            </div>
            
            <div class="col-md-6">
              <label for="dateExpiration" class="form-label required">Date d'expiration</label>
              <input type="date" class="form-control" id="dateExpiration" required>
              <div class="invalid-feedback">Veuillez saisir la date d'expiration.</div>
            </div>
          </div>
        </div>
        
        <!-- Contact Information Section -->
        <div class="section">
          <h3 class="section-title">Coordonnées</h3>
          <div class="row g-3">
            <div class="col-md-12">
              <label for="adresse" class="form-label required">Adresse complète</label>
              <textarea class="form-control" id="adresse" rows="2" required></textarea>
              <div class="invalid-feedback">Veuillez saisir votre adresse.</div>
            </div>
            
            <div class="col-md-4">
              <label for="ville" class="form-label required">Ville</label>
              <input type="text" class="form-control" id="ville" required>
              <div class="invalid-feedback">Veuillez saisir votre ville.</div>
            </div>
            
            <div class="col-md-4">
              <label for="commune" class="form-label">Commune</label>
              <input type="text" class="form-control" id="commune">
            </div>
            
            <div class="col-md-4">
              <label for="quartier" class="form-label">Quartier</label>
              <input type="text" class="form-control" id="quartier">
            </div>
            
            <div class="col-md-6">
              <label for="telephone" class="form-label required">Numéro de téléphone</label>
              <div class="input-group">
                <span class="input-group-text">+225</span>
                <input type="tel" class="form-control" id="telephone" pattern="[0-9]{10}" required>
              </div>
              <div class="invalid-feedback">Veuillez saisir un numéro de téléphone valide.</div>
              <div class="instruction">Format: 0707070707</div>
            </div>
            
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email">
              <div class="invalid-feedback">Veuillez saisir une adresse email valide.</div>
            </div>
          </div>
        </div>
        
        <!-- Request Details Section -->
        <div class="section">
          <h3 class="section-title">Détails de la Demande</h3>
          <div class="row g-3">
            <div class="col-md-12">
              <label for="motifDemande" class="form-label required">Motif de la demande</label>
              <select class="form-select" id="motifDemande" required>
                <option value="" selected disabled>Choisir...</option>
                <option value="Administratif">Procédure Administrative</option>
                <option value="Juridique">Procédure Juridique</option>
                <option value="Bancaire">Demande de Prêt Bancaire</option>
                <option value="Emploi">Recherche d'Emploi</option>
                <option value="Etudes">Poursuites d'Études</option>
                <option value="Social">Aide Sociale</option>
                <option value="Autre">Autre</option>
              </select>
              <div class="invalid-feedback">Veuillez sélectionner le motif de votre demande.</div>
            </div>
            
            <div class="col-md-12" id="autreMotifContainer" style="display: none;">
              <label for="autreMotif" class="form-label required">Précisez le motif</label>
              <input type="text" class="form-control" id="autreMotif">
              <div class="invalid-feedback">Veuillez préciser le motif de votre demande.</div>
            </div>
            
            <div class="col-md-12">
              <label for="detailsDemande" class="form-label">Informations complémentaires</label>
              <textarea class="form-control" id="detailsDemande" rows="3"></textarea>
            </div>
          </div>
        </div>
        
        <!-- Declaration Section -->
        <div class="declaration">
          <h3 class="section-title">Déclaration sur l'Honneur</h3>
          
          <p>Je soussigné(e), <span id="nomCompletDeclaration" class="fw-bold">_________________</span>, déclare sur l'honneur que je ne dispose d'aucun revenu imposable en République de Côte d'Ivoire pour la période fiscale en cours et pour les périodes précédentes.</p>
          
          <p>Je certifie que les informations fournies dans ce formulaire sont exactes et complètes. Je comprends que toute fausse déclaration peut entraîner des poursuites judiciaires conformément aux lois en vigueur en République de Côte d'Ivoire.</p>
          
          <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" id="acceptDeclaration" required>
            <label class="form-check-label" for="acceptDeclaration">
              Je confirme avoir lu et j'accepte cette déclaration sur l'honneur
            </label>
            <div class="invalid-feedback">Vous devez accepter la déclaration pour continuer.</div>
          </div>
        </div>

        <!--info sur le payement -->
        <div class="card mb-5">
      <div class="card-header bg-light">
        <h4 class="mb-0 fs-5">Informations utiles</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5 class="fs-6 fw-bold"><i class="fas fa-clock me-2 text-secondary"></i> Délai de traitement</h5>
            <p>Le certificat de vie est généralement délivré le jour même de la demande.</p>
            
            <h5 class="fs-6 fw-bold mt-3"><i class="fas fa-money-bill-wave me-2 text-secondary"></i> Frais administratifs</h5>
            <p>Le coût du certificat de vie est de 3500 FCFA, payable par mobile.</p>
          </div>
          <div class="col-md-6">
            <h5 class="fs-6 fw-bold"><i class="fas fa-map-marker-alt me-2 text-secondary"></i> Où faire la demande?</h5>
            <p>Le certificat de vie peut être demandé à la mairie de votre lieu de résidence.</p>
            
            <h5 class="fs-6 fw-bold mt-3"><i class="fas fa-question-circle me-2 text-secondary"></i> Besoin d'aide?</h5>
            <p>Pour toute assistance, contactez le service d'état civil de votre mairie.</p>
          </div>
        </div>
      </div>
    </div>
        
        <!-- Submission Section -->
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary me-2">
            <i class="fas fa-paper-plane me-2"></i> Soumettre la Demande
          </button>
          <button type="reset" class="btn btn-secondary">
            <i class="fas fa-redo me-2"></i> Réinitialiser
          </button>
        </div>
      </form>
    </div>
  
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('nonRevenueForm');
      const motifDemande = document.getElementById('motifDemande');
      const autreMotifContainer = document.getElementById('autreMotifContainer');
      const autreMotif = document.getElementById('autreMotif');
      const nom = document.getElementById('nom');
      const prenoms = document.getElementById('prenoms');
      const nomCompletDeclaration = document.getElementById('nomCompletDeclaration');
      
      // Show/hide "Other" field based on selection
      motifDemande.addEventListener('change', function() {
        if (this.value === 'Autre') {
          autreMotifContainer.style.display = 'block';
          autreMotif.setAttribute('required', '');
        } else {
          autreMotifContainer.style.display = 'none';
          autreMotif.removeAttribute('required');
        }
      });
      
      // Update declaration name when name fields change
      function updateDeclarationName() {
        const nomValue = nom.value.trim();
        const prenomsValue = prenoms.value.trim();
        
        if (nomValue && prenomsValue) {
          nomCompletDeclaration.textContent = prenomsValue + ' ' + nomValue;
        } else {
          nomCompletDeclaration.textContent = '_________________';
        }
      }
      
      nom.addEventListener('input', updateDeclarationName);
      prenoms.addEventListener('input', updateDeclarationName);
      
      // Form submission
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        if (!form.checkValidity()) {
          event.stopPropagation();
          // Add animation to highlight invalid fields
          const invalidFields = form.querySelectorAll(':invalid');
          invalidFields.forEach(field => {
            field.classList.add('is-invalid');
            field.animate([
              { transform: 'translateX(-5px)' },
              { transform: 'translateX(5px)' },
              { transform: 'translateX(-5px)' },
              { transform: 'translateX(5px)' },
              { transform: 'translateX(0)' }
            ], {
              duration: 300,
              iterations: 1
            });
          });
        } else {
          // Form is valid, show success message
          alert('Votre demande a été soumise avec succès. Un numéro de suivi vous sera communiqué prochainement.');
          // In a real application, you would submit the form data to a server here
        }
        
        form.classList.add('was-validated');
      });
      
      // Reset form validation on reset
      form.addEventListener('reset', function() {
        form.classList.remove('was-validated');
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));
        nomCompletDeclaration.textContent = '_________________';
      });
      
      // Validate date fields to ensure expiration is after emission
      const dateEmission = document.getElementById('dateEmission');
      const dateExpiration = document.getElementById('dateExpiration');
      
      dateExpiration.addEventListener('change', function() {
        if (dateEmission.value && dateExpiration.value) {
          const emission = new Date(dateEmission.value);
          const expiration = new Date(dateExpiration.value);
          
          if (expiration <= emission) {
            dateExpiration.setCustomValidity('La date d\'expiration doit être ultérieure à la date d\'émission');
          } else {
            dateExpiration.setCustomValidity('');
          }
        }
      });
      
      dateEmission.addEventListener('change', function() {
        if (dateEmission.value && dateExpiration.value) {
          const emission = new Date(dateEmission.value);
          const expiration = new Date(dateExpiration.value);
          
          if (expiration <= emission) {
            dateExpiration.setCustomValidity('La date d\'expiration doit être ultérieure à la date d\'émission');
          } else {
            dateExpiration.setCustomValidity('');
          }
        }
      });
    });
  </script>
</body>
@endsection