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
        <h2 class="fs-4 fw-normal mt-3">Formulaire de Demande d'Acte de Naissance</h2>
    </div>
</header>

<div class="container py-5">
    <div class="form-container">
        <form id="birthCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data" class="p-4 p-md-5">
            @csrf
            <input type="hidden" name="type" value="naissance">

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
                <!-- Informations Personnelles -->
                <div class="section">
                    <h3 class="section-title">Informations Personnelles</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_enfant">Nom</label>
                            <input type="text" class="form-control @error('metadata.nom_enfant') is-invalid @enderror" name="metadata[nom_enfant]" id="nom_enfant" value="{{ old('metadata.nom_enfant') }}" required>
                            @error('metadata.nom_enfant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="prenom_enfant">Prénoms</label>
                            <input type="text" class="form-control @error('metadata.prenom_enfant') is-invalid @enderror" name="metadata[prenom_enfant]" id="prenom_enfant" value="{{ old('metadata.prenom_enfant') }}" required>
                            @error('metadata.prenom_enfant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Informations de Naissance -->
                <div class="section">
                    <h3 class="section-title">Informations de Naissance</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="date_naissance">Date de Naissance</label>
                            <input type="date" class="form-control @error('metadata.date_naissance') is-invalid @enderror" name="metadata[date_naissance]" id="date_naissance" value="{{ old('metadata.date_naissance') }}" required>
                            @error('metadata.date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="lieu_naissance">Lieu de Naissance</label>
                            <input type="text" class="form-control @error('metadata.lieu_naissance') is-invalid @enderror" name="metadata[lieu_naissance]" id="lieu_naissance" value="{{ old('metadata.lieu_naissance') }}" required>
                            @error('metadata.lieu_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="sexe">Genre</label>
                            <select class="form-select @error('metadata.sexe') is-invalid @enderror" name="metadata[sexe]" id="sexe" required>
                                <option value="">Sélectionnez...</option>
                                <option value="M" {{ old('metadata.sexe') == 'M' ? 'selected' : '' }}>Masculin</option>
                                <option value="F" {{ old('metadata.sexe') == 'F' ? 'selected' : '' }}>Féminin</option>
                            </select>
                            @error('metadata.sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            @error('commune_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Informations des Parents -->
                <div class="section">
                    <h3 class="section-title">Informations des Parents</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_pere">Nom complet du Père</label>
                            <input type="text" class="form-control @error('metadata.nom_pere') is-invalid @enderror" name="metadata[nom_pere]" id="nom_pere" value="{{ old('metadata.nom_pere') }}" required>
                            @error('metadata.nom_pere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nationalite_pere">Nationalité du Père</label>
                            <input type="text" class="form-control @error('metadata.nationalite_pere') is-invalid @enderror" name="metadata[nationalite_pere]" id="nationalite_pere" value="{{ old('metadata.nationalite_pere') }}" required>
                            @error('metadata.nationalite_pere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_mere">Nom complet de la Mère</label>
                            <input type="text" class="form-control @error('metadata.nom_mere') is-invalid @enderror" name="metadata[nom_mere]" id="nom_mere" value="{{ old('metadata.nom_mere') }}" required>
                            @error('metadata.nom_mere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nationalite_mere">Nationalité de la Mère</label>
                            <input type="text" class="form-control @error('metadata.nationalite_mere') is-invalid @enderror" name="metadata[nationalite_mere]" id="nationalite_mere" value="{{ old('metadata.nationalite_mere') }}" required>
                            @error('metadata.nationalite_mere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Pièces Justificatives -->
                <div class="section">
                    <h3 class="section-title">Pièces Justificatives</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="justificatif">Pièce jointe</label>
                            <input type="file" class="form-control @error('justificatif') is-invalid @enderror" name="justificatif" id="justificatif" required>
                            @error('justificatif')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            </div>

            <!-- Section Duplicata -->
            <div id="duplicataFields" style="display:none;">
                <div class="section">
                    <h3 class="section-title">Duplicata - Renseignez le numéro du registre</h3>
                    <div class="mb-3">
                        <label class="form-label required" for="registry_number">N° du registre</label>
                        <input type="text" class="form-control @error('registry_number') is-invalid @enderror" name="registry_number" id="registry_number" placeholder="Numéro du registre" value="{{ old('registry_number') }}">
                        @error('registry_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
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

        // Form validation
        const form = document.getElementById('birthCertForm');

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

        toggleFields(); // Initialisation
    });
</script>

@endsection
