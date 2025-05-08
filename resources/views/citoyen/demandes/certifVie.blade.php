@extends('layouts.app')

@section('content')

<body>
  <!-- Header -->
  <header class="header text-center">
    <div class="container">
      <img src="{{ asset('img/drapeau CI.jpg') }}" alt="Drapeau de la Côte d'Ivoire" width="80" class="mb-3">
      <h1 class="display-6 fw-bold">République de Côte d'Ivoire</h1>
      <p class="lead">Union - Discipline - Travail</p>
      <h2 class="fs-4 fw-normal mt-3">Formulaire de Demande de Certificat de Vie</h2>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container my-5">
    <div class="national-colors">
      <div class="orange"></div>
      <div class="white"></div>
      <div class="green"></div>
    </div>

    <div class="form-container">
      <form id="lifeCertificateForm" class="needs-validation" novalidate>
        <!-- Section 1: Informations du Demandeur -->
        <div class="section-personal mb-4">
          <h3 class="section-title">Informations du Demandeur</h3>
          
          <div class="row g-3">
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <select class="form-select" id="civilite" required>
                  <option value="" selected disabled>Choisir</option>
                  <option value="M.">Monsieur</option>
                  <option value="Mme">Madame</option>
                  <option value="Mlle">Mademoiselle</option>
                </select>
                <label for="civilite" class="required-field">Civilité</label>
                <div class="invalid-feedback">Veuillez sélectionner votre civilité.</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="nom" placeholder="Nom" required>
                <label for="nom" class="required-field">Nom</label>
                <div class="invalid-feedback">Veuillez entrer votre nom.</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="prenoms" placeholder="Prénoms" required>
                <label for="prenoms" class="required-field">Prénoms</label>
                <div class="invalid-feedback">Veuillez entrer vos prénoms.</div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="date" class="form-control" id="dateNaissance" required>
                <label for="dateNaissance" class="required-field">Date de Naissance</label>
                <div class="invalid-feedback">Veuillez entrer votre date de naissance.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="lieuNaissance" placeholder="Lieu de Naissance" required>
                <label for="lieuNaissance" class="required-field">Lieu de Naissance</label>
                <div class="invalid-feedback">Veuillez entrer votre lieu de naissance.</div>
              </div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="numCNI" placeholder="Numéro de CNI" required>
                <label for="numCNI" class="required-field">Numéro de CNI</label>
                <div class="invalid-feedback">Veuillez entrer votre numéro de CNI.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="date" class="form-control" id="dateEmissionCNI" required>
                <label for="dateEmissionCNI" class="required-field">Date d'émission de la CNI</label>
                <div class="invalid-feedback">Veuillez entrer la date d'émission de votre CNI.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Adresse & Contact -->
        <div class="section-address mb-4">
          <h3 class="section-title">Adresse & Contact</h3>
          
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="adresse" placeholder="Adresse" required>
                <label for="adresse" class="required-field">Adresse</label>
                <div class="invalid-feedback">Veuillez entrer votre adresse.</div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating mb-3">
                <input type="tel" class="form-control" id="telephone" placeholder="Téléphone" required pattern="[0-9]{10}">
                <label for="telephone" class="required-field">Téléphone</label>
                <div class="invalid-feedback">Veuillez entrer un numéro de téléphone valide (10 chiffres).</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Motif de la demande -->
        <div class="section-reason mb-4">
          <h3 class="section-title">Motif de la demande</h3>
          
          <div class="row g-3">
            <div class="col-md-12">
              <div class="form-floating mb-3">
                <select class="form-select" id="motifDemande" required>
                  <option value="" selected disabled>Choisir</option>
                  <option value="pension">Pension de retraite</option>
                  <option value="allocation">Allocation familiale</option>
                  <option value="assurance">Assurance vie</option>
                  <option value="autre">Autre motif</option>
                </select>
                <label for="motifDemande" class="required-field">Motif de la demande</label>
                <div class="invalid-feedback">Veuillez sélectionner le motif de votre demande.</div>
              </div>
            </div>
          </div>

          <div class="row g-3 d-none" id="autreMotifRow">
            <div class="col-md-12">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="autreMotif" placeholder="Précisez le motif">
                <label for="autreMotif">Précisez le motif</label>
                <div class="invalid-feedback">Veuillez préciser le motif de votre demande.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 4: Documents requis -->
        <div class="section-documents mb-4">
          <h3 class="section-title">Documents requis</h3>
          
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Veuillez vous assurer d'avoir les documents suivants:
          </div>
          
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="docCNI" required>
            <label class="form-check-label required-field" for="docCNI">
              Photocopie de la CNI
            </label>
            <div class="invalid-feedback">Ce document est obligatoire.</div>
          </div>
          
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="docPhoto" required>
            <label class="form-check-label required-field" for="docPhoto">
              2 photos d'identité récentes
            </label>
            <div class="invalid-feedback">Ce document est obligatoire.</div>
          </div>
        </div>

        <!-- Section 5: Déclaration sur l'honneur -->
        <div class="section-declaration mb-4">
          <h3 class="section-title">Déclaration sur l'honneur</h3>
          
          <div class="bg-light p-3 mb-3 rounded">
            <p>Je soussigné(e), <span id="nomCompletAffichage">_______________________</span>, 
               certifie sur l'honneur être en vie à ce jour et que les informations fournies dans ce formulaire sont exactes.</p>
          </div>
          
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="acceptDeclaration" required>
            <label class="form-check-label required-field" for="acceptDeclaration">
              Je confirme avoir lu et j'accepte cette déclaration
            </label>
            <div class="invalid-feedback">Vous devez accepter la déclaration pour continuer.</div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between mt-4">
          <button type="button" class="btn btn-outline-primary" id="previewBtn">
            <i class="fas fa-eye me-2"></i> Aperçu
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i> Soumettre la demande
          </button>
        </div>
      </form>
    </div>

    <!-- Informations utiles -->
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
            <p>Le coût du certificat de vie est de 500 FCFA, payable en espèces.</p>
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
  </main>

  <!-- Footer -->

  <!-- Bootstrap 5.3.0 JS with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Populate the full name in declaration
      const nomInput = document.getElementById('nom');
      const prenomsInput = document.getElementById('prenoms');
      const nomCompletAffichage = document.getElementById('nomCompletAffichage');
      
      function updateNomComplet() {
        const nomValue = nomInput.value.trim();
        const prenomsValue = prenomsInput.value.trim();
        if (nomValue && prenomsValue) {
          nomCompletAffichage.textContent = prenomsValue + ' ' + nomValue;
        } else if (nomValue) {
          nomCompletAffichage.textContent = nomValue;
        } else if (prenomsValue) {
          nomCompletAffichage.textContent = prenomsValue;
        } else {
          nomCompletAffichage.textContent = '_______________________';
        }
      }
      
      nomInput.addEventListener('input', updateNomComplet);
      prenomsInput.addEventListener('input', updateNomComplet);
      
      // Show/hide "autre motif" field
      const motifSelect = document.getElementById('motifDemande');
      const autreMotifRow = document.getElementById('autreMotifRow');
      const autreMotifInput = document.getElementById('autreMotif');
      
      motifSelect.addEventListener('change', function() {
        if (this.value === 'autre') {
          autreMotifRow.classList.remove('d-none');
          autreMotifInput.setAttribute('required', '');
        } else {
          autreMotifRow.classList.add('d-none');
          autreMotifInput.removeAttribute('required');
        }
      });
      
      // Form validation
      const form = document.getElementById('lifeCertificateForm');
      
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
          
          // Find the first invalid element and focus it
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid) {
            firstInvalid.focus();
            
            // Also scroll to the invalid element
            const sectionTitle = firstInvalid.closest('div[class^="section-"]').querySelector('.section-title');
            if (sectionTitle) {
              sectionTitle.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }
        } else {
          // For demonstration purposes, show an alert instead of actually submitting
          event.preventDefault();
          alert('Formulaire validé avec succès ! En conditions réelles, votre demande serait soumise.');
        }
        
        form.classList.add('was-validated');
      });
      
      // Preview button functionality
      const previewBtn = document.getElementById('previewBtn');
      
      previewBtn.addEventListener('click', function() {
        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          
          // Find the first invalid element and focus it
          const firstInvalid = form.querySelector(':invalid');
          if (firstInvalid) {
            firstInvalid.focus();
            
            // Also scroll to the invalid element
            const sectionTitle = firstInvalid.closest('div[class^="section-"]').querySelector('.section-title');
            if (sectionTitle) {
              sectionTitle.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          }
          
          alert('Veuillez remplir tous les champs obligatoires avant de prévisualiser.');
        } else {
          window.print();
        }
      });
    });
  </script>
</body>

@endsection