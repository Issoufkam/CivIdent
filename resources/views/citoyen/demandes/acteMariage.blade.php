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
        <h2 class="fs-4 fw-normal mt-3">Formulaire de Demande d'Acte de Mariage</h2>
    </div>
</header>

<div class="container py-5">
    <div class="form-container">
        <form id="marriageCertForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data" class="p-4 p-md-5">
            @csrf
            <input type="hidden" name="type" value="mariage">

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
                <!-- Informations du Mariage -->
                <div class="section">
                    <h3 class="section-title">Informations du Mariage</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="date_mariage">Date du Mariage</label>
                            <input type="date" class="form-control @error('metadata.date_mariage') is-invalid @enderror" name="metadata[date_mariage]" id="date_mariage" value="{{ old('metadata.date_mariage') }}" required>
                            @error('metadata.date_mariage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="lieu_mariage">Lieu du Mariage</label>
                            <input type="text" class="form-control @error('metadata.lieu_mariage') is-invalid @enderror" name="metadata[lieu_mariage]" id="lieu_mariage" value="{{ old('metadata.lieu_mariage') }}" required>
                            @error('metadata.lieu_mariage')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

                <!-- Informations des Époux -->
                <div class="section">
                    <h3 class="section-title">Informations des Époux</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_epoux">Nom complet de l'Époux</label>
                            <input type="text" class="form-control @error('metadata.nom_epoux') is-invalid @enderror" name="metadata[nom_epoux]" id="nom_epoux" value="{{ old('metadata.nom_epoux') }}" required>
                            @error('metadata.nom_epoux')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nationalite_epoux">Nationalité de l'Époux</label>
                            <input type="text" class="form-control @error('metadata.nationalite_epoux') is-invalid @enderror" name="metadata[nationalite_epoux]" id="nationalite_epoux" value="{{ old('metadata.nationalite_epoux') }}" required>
                            @error('metadata.nationalite_epoux')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nom_epouse">Nom complet de l'Épouse</label>
                            <input type="text" class="form-control @error('metadata.nom_epouse') is-invalid @enderror" name="metadata[nom_epouse]" id="nom_epouse" value="{{ old('metadata.nom_epouse') }}" required>
                            @error('metadata.nom_epouse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="nationalite_epouse">Nationalité de l'Épouse</label>
                            <input type="text" class="form-control @error('metadata.nationalite_epouse') is-invalid @enderror" name="metadata[nationalite_epouse]" id="nationalite_epouse" value="{{ old('metadata.nationalite_epouse') }}" required>
                            @error('metadata.nationalite_epouse')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    {{-- <div class="mb-3">
                        <label class="form-label required" for="idFront">Pièce d'identité (recto)</label>
                        <input type="file" class="form-control @error('idFront') is-invalid @enderror" name="idFront" id="idFront">
                        @error('idFront')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div> --}}
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
                duplicataFields.querySelector('input[name="idFront"]').setAttribute('required', 'required');
            } else {
                newRequestFields.style.display = 'block';
                duplicataFields.style.display = 'none';

                // Remettre required sur champs nouvelle demande
                newRequestFields.querySelectorAll('input, select').forEach(input => {
                    input.setAttribute('required', 'required');
                });
                // Retirer required sur duplicata
                duplicataFields.querySelector('input[name="registry_number"]').removeAttribute('required');
                duplicataFields.querySelector('input[name="idFront"]').removeAttribute('required');
            }
        }

        newRequestRadio.addEventListener('change', toggleFields);
        duplicataRadio.addEventListener('change', toggleFields);

        // Form validation
        const form = document.getElementById('marriageCertForm');

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
