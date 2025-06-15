@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<header class="header text-center">
    <div class="container">
        <img src="{{ asset('img/drapeau CI.jpg') }}" alt="Drapeau de la Côte d'Ivoire" width="80" class="mb-3">
        <h1 class="display-6 fw-bold">République de Côte d'Ivoire</h1>
        <p class="lead">Union - Discipline - Travail</p>
        <h2 class="fs-4 fw-normal mt-3">Formulaire de Demande d'Acte de Décès</h2>
    </div>
</header>

<div class="container py-5">
    <div class="form-container">
        <form id="deathCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data" class="p-4 p-md-5">
            @csrf
            <input type="hidden" name="type" value="deces">

            <!-- Instructions -->
            <div class="alert alert-info mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i> Remplissez tous les champs marqués d'un astérisque (*). Assurez-vous que les informations fournies sont exactes et complètes.
            </div>

            <!-- Type de Demande -->
            <div class="section">
                <h3 class="section-title">Type de Demande</h3>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_duplicata" id="newRequest" value="0" {{ old('is_duplicata', '0') == '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="newRequest">Nouvelle Demande</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_duplicata" id="duplicataRequest" value="1" {{ old('is_duplicata') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="duplicataRequest">Duplicata</label>
                </div>
            </div>

            <!-- Section Nouvelle Demande -->
            <div id="newRequestFields">
                <!-- Informations du Défunt -->
                <div class="section">
                    <h3 class="section-title">Informations du Défunt</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_defunt">Nom du Défunt</label>
                            <input type="text" class="form-control @error('metadata.nom_defunt') is-invalid @enderror" id="nom_defunt" name="metadata[nom_defunt]" value="{{ old('metadata.nom_defunt') }}" required>
                            @error('metadata.nom_defunt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="prenom_defunt">Prénoms du Défunt</label>
                            <input type="text" class="form-control @error('metadata.prenom_defunt') is-invalid @enderror" id="prenom_defunt" name="metadata[prenom_defunt]" value="{{ old('metadata.prenom_defunt') }}" required>
                            @error('metadata.prenom_defunt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="date_deces">Date de Décès</label>
                            <input type="date" class="form-control @error('metadata.date_deces') is-invalid @enderror" id="date_deces" name="metadata[date_deces]" value="{{ old('metadata.date_deces') }}" required>
                            @error('metadata.date_deces')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="lieu_deces">Lieu de Décès</label>
                            <input type="text" class="form-control @error('metadata.lieu_deces') is-invalid @enderror" id="lieu_deces" name="metadata[lieu_deces]" value="{{ old('metadata.lieu_deces') }}" required>
                            @error('metadata.lieu_deces')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="death_cause">Cause du Décès</label>
                            <input type="text" class="form-control @error('metadata.death_cause') is-invalid @enderror" id="death_cause" name="metadata[death_cause]" value="{{ old('metadata.death_cause') }}">
                            @error('metadata.death_cause')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="commune_id">Commune ou Sous-Préfecture</label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" name="commune_id" id="commune_id" required>
                                <option value="">Sélectionnez votre commune ou sous-préfecture</option>
                                @foreach ($communes as $commune)
                                <option value="{{ $commune->id }}" {{ old('commune_id') == $commune->id ? 'selected' : '' }}>
                                    {{ $commune->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('commune_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations du Demandeur -->
                <div class="section">
                    <h3 class="section-title">Informations du Demandeur</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="requester_name">Nom</label>
                            <input type="text" class="form-control @error('metadata.requester_name') is-invalid @enderror" id="requester_name" name="metadata[requester_name]" value="{{ old('metadata.requester_name') }}" required>
                            @error('metadata.requester_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="requester_firstname">Prénoms</label>
                            <input type="text" class="form-control @error('metadata.requester_firstname') is-invalid @enderror" id="requester_firstname" name="metadata[requester_firstname]" value="{{ old('metadata.requester_firstname') }}" required>
                            @error('metadata.requester_firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="relationship">Lien avec le Défunt</label>
                            <select class="form-select @error('metadata.relationship') is-invalid @enderror" id="relationship" name="metadata[relationship]" required>
                                <option value="">Sélectionnez...</option>
                                <option value="conjoint" {{ old('metadata.relationship') == 'conjoint' ? 'selected' : '' }}>Conjoint(e)</option>
                                <option value="enfant" {{ old('metadata.relationship') == 'enfant' ? 'selected' : '' }}>Enfant</option>
                                <option value="parent" {{ old('metadata.relationship') == 'parent' ? 'selected' : '' }}>Parent</option>
                                <option value="frere_soeur" {{ old('metadata.relationship') == 'frere_soeur' ? 'selected' : '' }}>Frère/Sœur</option>
                                <option value="autre" {{ old('metadata.relationship') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('metadata.relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="phone">Téléphone</label>
                            <div class="input-group">
                                <span class="input-group-text">+225</span>
                                <input type="tel" class="form-control @error('metadata.phone') is-invalid @enderror" id="phone" name="metadata[phone]" value="{{ old('metadata.phone') }}" required>
                            </div>
                            @error('metadata.phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="instruction">Format: 0707070707</div>
                        </div>
                    </div>
                </div>

                <!-- Pièces Justificatives -->
                <div class="section">
                    <h3 class="section-title">Pièces Justificatives</h3>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required" for="justificatif">Certificat de Décès Original (ou autre justificatif pertinent)</label>
                            <input type="file" class="form-control @error('justificatif') is-invalid @enderror" name="justificatif" id="justificatif" required>
                            @error('justificatif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Détails de la Demande -->
                <div class="section">
                    <h3 class="section-title">Détails de la Demande</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="copies">Nombre de Copies</label>
                            <select class="form-select @error('metadata.copies') is-invalid @enderror" id="copies" name="metadata[copies]" required>
                                <option value="1" {{ old('metadata.copies') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('metadata.copies') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('metadata.copies') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('metadata.copies') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('metadata.copies') == '5' ? 'selected' : '' }}>5</option>
                            </select>
                            @error('metadata.copies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="purpose">Usage Prévu</label>
                            <select class="form-select @error('metadata.purpose') is-invalid @enderror" id="purpose" name="metadata[purpose]" required>
                                <option value="">Sélectionnez...</option>
                                <option value="succession" {{ old('metadata.purpose') == 'succession' ? 'selected' : '' }}>Succession</option>
                                <option value="administratif" {{ old('metadata.purpose') == 'administratif' ? 'selected' : '' }}>Usage Administratif</option>
                                <option value="assurance" {{ old('metadata.purpose') == 'assurance' ? 'selected' : '' }}>Assurance</option>
                                <option value="autre" {{ old('metadata.purpose') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('metadata.purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Duplicata -->
            <div id="duplicataFields" style="display:none;">
                <div class="section">
                    <h3 class="section-title">Duplicata - Renseignez le numéro du registre</h3>
                    <div class="mb-3">
                        <label class="form-label required" for="registry_number">N° du registre de l'acte original</label>
                        <input type="text" class="form-control @error('registry_number') is-invalid @enderror" name="registry_number" id="registry_number" placeholder="Ex: REG-DEC-20230101-ABCDE" value="{{ old('registry_number') }}">
                        @error('registry_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required" for="copies">Nombre de Copies</label>
                        <select class="form-select @error('metadata.copies') is-invalid @enderror" id="copies" name="metadata[copies]" required>
                            <option value="1" {{ old('metadata.copies') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('metadata.copies') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('metadata.copies') == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ old('metadata.copies') == '4' ? 'selected' : '' }}>4</option>
                            <option value="5" {{ old('metadata.copies') == '5' ? 'selected' : '' }}>5</option>
                        </select>
                        @error('metadata.copies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>


            <!-- Boutons de soumission -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-paper-plane me-2"></i> Soumettre la Demande
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const newRequestRadio = document.getElementById('newRequest');
        const duplicataRadio = document.getElementById('duplicataRequest');
        const newRequestFields = document.getElementById('newRequestFields');
        const duplicataFields = document.getElementById('duplicataFields');

        // Fields for new request
        const newRequestFormElements = newRequestFields.querySelectorAll('input, select, textarea');
        // Fields for duplicata request
        const duplicataRegistryNumberInput = duplicataFields.querySelector('input[name="registry_number"]');
        const duplicataIdFrontInput = duplicataFields.querySelector('input[name="idFront"]');

        // Declaration elements
        const requesterNameInput = document.getElementById('requester_name');
        const requesterFirstnameInput = document.getElementById('requester_firstname');
        const nomCompletDeclaration = document.getElementById('nomCompletDeclaration');

        function updateDeclarationName() {
            const nomValue = requesterNameInput.value.trim();
            const prenomsValue = requesterFirstnameInput.value.trim();

            if (nomValue || prenomsValue) {
                nomCompletDeclaration.textContent = `${prenomsValue} ${nomValue}`.trim();
            } else {
                nomCompletDeclaration.textContent = '_________________';
            }
        }

        function toggleFields() {
            const isDuplicata = duplicataRadio.checked;

            if (isDuplicata) {
                newRequestFields.style.display = 'none';
                duplicataFields.style.display = 'block';

                // Remove 'required' from new request fields
                newRequestFormElements.forEach(input => {
                    input.removeAttribute('required');
                });

                // Set 'required' for duplicata fields
                if (duplicataRegistryNumberInput) {
                    duplicataRegistryNumberInput.setAttribute('required', 'required');
                }
                if (duplicataIdFrontInput) {
                    duplicataIdFrontInput.setAttribute('required', 'required');
                }

            } else { // Nouvelle Demande
                newRequestFields.style.display = 'block';
                duplicataFields.style.display = 'none';

                // Set 'required' for new request fields
                document.getElementById('nom_defunt').setAttribute('required', 'required');
                document.getElementById('prenom_defunt').setAttribute('required', 'required');
                document.getElementById('date_deces').setAttribute('required', 'required');
                document.getElementById('lieu_deces').setAttribute('required', 'required');
                document.getElementById('commune_id').setAttribute('required', 'required');
                document.getElementById('requester_name').setAttribute('required', 'required');
                document.getElementById('requester_firstname').setAttribute('required', 'required');
                document.getElementById('relationship').setAttribute('required', 'required');
                document.getElementById('phone').setAttribute('required', 'required');
                document.getElementById('justificatif').setAttribute('required', 'required');
                document.getElementById('copies').setAttribute('required', 'required');
                document.getElementById('purpose').setAttribute('required', 'required');

                // Remove 'required' from duplicata fields
                if (duplicataRegistryNumberInput) {
                    duplicataRegistryNumberInput.removeAttribute('required');
                }
                if (duplicataIdFrontInput) {
                    duplicataIdFrontInput.removeAttribute('required');
                }
            }
        }

        // Event listeners for radio buttons
        newRequestRadio.addEventListener('change', toggleFields);
        duplicataRadio.addEventListener('change', toggleFields);

        // Event listeners for declaration name update
        requesterNameInput.addEventListener('input', updateDeclarationName);
        requesterFirstnameInput.addEventListener('input', updateDeclarationName);

        // Initial call to set the correct state based on pre-selected values or defaults
        toggleFields();
        updateDeclarationName(); // Initial update for declaration name

        // Form validation
        const form = document.getElementById('deathCertForm');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
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
            }

            form.classList.add('was-validated');
        });
    });
</script>

@endsection
