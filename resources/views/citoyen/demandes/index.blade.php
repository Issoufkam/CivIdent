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

<div class="container main-container py-4">
    <h1 class="text-center mb-4 fade-in">Créer une nouvelle demande de document</h1>

    <div class="form-container fade-in">
        <div class="row g-4 flex-column-reverse flex-md-row">

            <div class="col-12 col-md-7">
                <div class="form-section px-2 px-md-4 py-4 border rounded shadow-sm">

                    <form id="genericDocumentForm" method="POST" action="{{ route('citoyen.demandes.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fs-5 fw-bold" for="type">Type de document :</label>
                            <select class="form-select form-select-lg @error('type') is-invalid @enderror" name="type" id="type" onchange="updateFormVisibility()" required>
                                <option value="">-- Choisir un type --</option>
                                <option value="naissance" {{ old('type') == 'naissance' ? 'selected' : '' }}>Acte de Naissance</option>
                                <option value="mariage" {{ old('type') == 'mariage' ? 'selected' : '' }}>Acte de Mariage</option>
                                <option value="deces" {{ old('type') == 'deces' ? 'selected' : '' }}>Acte de Décès</option>
                                <option value="certificat-vie" {{ old('type') == 'certificat-vie' ? 'selected' : '' }}>Certificat de Vie</option>
                                <option value="certificat-entretien" {{ old('type') == 'certificat-entretien' ? 'selected' : '' }}>Certificat d'Entretien</option>
                                <option value="certificat-revenu" {{ old('type') == 'certificat-revenu' ? 'selected' : '' }}>Certificat de Non Revenu</option>
                                <option value="certificat-divorce" {{ old('type') == 'certificat-divorce' ? 'selected' : '' }}>Certificat de Divorce</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <h3 class="section-title mb-3">Type de Demande</h3>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_duplicata" id="newRequest" value="0" {{ old('is_duplicata', '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="newRequest">Nouvelle Demande</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="is_duplicata" id="duplicataRequest" value="1" {{ old('is_duplicata') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="duplicataRequest">Duplicata</label>
                            </div>
                        </div>

                        <div id="newRequestFields">
                            <h3 class="section-title mb-3">Informations Générales du Document</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label" for="registry_page">Page du registre :</label>
                                    <input type="number" class="form-control @error('registry_page') is-invalid @enderror" name="registry_page" id="registry_page" value="{{ old('registry_page') }}">
                                    @error('registry_page')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="registry_volume">Volume du registre :</label>
                                    <input type="text" class="form-control @error('registry_volume') is-invalid @enderror" name="registry_volume" id="registry_volume" value="{{ old('registry_volume') }}">
                                    @error('registry_volume')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="naissance-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Naissance</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_enfant">Nom de l'enfant :</label>
                                        <input type="text" class="form-control @error('metadata.nom_enfant') is-invalid @enderror" name="metadata[nom_enfant]" id="nom_enfant" value="{{ old('metadata.nom_enfant') }}">
                                        @error('metadata.nom_enfant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_enfant">Prénom de l'enfant :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_enfant') is-invalid @enderror" name="metadata[prenom_enfant]" id="prenom_enfant" value="{{ old('metadata.prenom_enfant') }}">
                                        @error('metadata.prenom_enfant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="date_naissance">Date de naissance :</label>
                                        <input type="date" class="form-control @error('metadata.date_naissance') is-invalid @enderror" name="metadata[date_naissance]" id="date_naissance" value="{{ old('metadata.date_naissance') }}">
                                        @error('metadata.date_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="lieu_naissance">Lieu de naissance :</label>
                                        <input type="text" class="form-control @error('metadata.lieu_naissance') is-invalid @enderror" name="metadata[lieu_naissance]" id="lieu_naissance" value="{{ old('metadata.lieu_naissance') }}">
                                        @error('metadata.lieu_naissance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="sexe_naissance">Sexe :</label>
                                        <select class="form-select @error('metadata.sexe') is-invalid @enderror" name="metadata[sexe]" id="sexe_naissance">
                                            <option value="">Sélectionnez...</option>
                                            <option value="Masculin" {{ old('metadata.sexe') == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                                            <option value="Féminin" {{ old('metadata.sexe') == 'Féminin' ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                        @error('metadata.sexe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_pere">Nom complet du Père :</label>
                                        <input type="text" class="form-control @error('metadata.nom_pere') is-invalid @enderror" name="metadata[nom_pere]" id="nom_pere" value="{{ old('metadata.nom_pere') }}">
                                        @error('metadata.nom_pere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="nationalite_pere">Nationalité du Père :</label>
                                        <input type="text" class="form-control @error('metadata.nationalite_pere') is-invalid @enderror" name="metadata[nationalite_pere]" id="nationalite_pere" value="{{ old('metadata.nationalite_pere') }}">
                                        @error('metadata.nationalite_pere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_mere">Nom complet de la Mère :</label>
                                        <input type="text" class="form-control @error('metadata.nom_mere') is-invalid @enderror" name="metadata[nom_mere]" id="nom_mere" value="{{ old('metadata.nom_mere') }}">
                                        @error('metadata.nom_mere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="nationalite_mere">Nationalité de la Mère :</label>
                                        <input type="text" class="form-control @error('metadata.nationalite_mere') is-invalid @enderror" name="metadata[nationalite_mere]" id="nationalite_mere" value="{{ old('metadata.nationalite_mere') }}">
                                        @error('metadata.nationalite_mere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="mariage-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Mariage</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_epoux">Nom époux :</label>
                                        <input type="text" class="form-control @error('metadata.nom_epoux') is-invalid @enderror" name="metadata[nom_epoux]" id="nom_epoux" value="{{ old('metadata.nom_epoux') }}">
                                        @error('metadata.nom_epoux')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_epoux">Prénom époux :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_epoux') is-invalid @enderror" name="metadata[prenom_epoux]" id="prenom_epoux" value="{{ old('metadata.prenom_epoux') }}">
                                        @error('metadata.prenom_epoux')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_epouse">Nom épouse :</label>
                                        <input type="text" class="form-control @error('metadata.nom_epouse') is-invalid @enderror" name="metadata[nom_epouse]" id="nom_epouse" value="{{ old('metadata.nom_epouse') }}">
                                        @error('metadata.nom_epouse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_epouse">Prénom épouse :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_epouse') is-invalid @enderror" name="metadata[prenom_epouse]" id="prenom_epouse" value="{{ old('metadata.prenom_epouse') }}">
                                        @error('metadata.prenom_epouse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="date_mariage">Date du mariage :</label>
                                        <input type="date" class="form-control @error('metadata.date_mariage') is-invalid @enderror" name="metadata[date_mariage]" id="date_mariage" value="{{ old('metadata.date_mariage') }}">
                                        @error('metadata.date_mariage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="lieu_mariage">Lieu du mariage :</label>
                                        <input type="text" class="form-control @error('metadata.lieu_mariage') is-invalid @enderror" name="metadata[lieu_mariage]" id="lieu_mariage" value="{{ old('metadata.lieu_mariage') }}">
                                        @error('metadata.lieu_mariage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="deces-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Décès</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_defunt">Nom du défunt :</label>
                                        <input type="text" class="form-control @error('metadata.nom_defunt') is-invalid @enderror" name="metadata[nom_defunt]" id="nom_defunt" value="{{ old('metadata.nom_defunt') }}">
                                        @error('metadata.nom_defunt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_defunt">Prénom du défunt :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_defunt') is-invalid @enderror" name="metadata[prenom_defunt]" id="prenom_defunt" value="{{ old('metadata.prenom_defunt') }}">
                                        @error('metadata.prenom_defunt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="date_deces">Date du décès :</label>
                                        <input type="date" class="form-control @error('metadata.date_deces') is-invalid @enderror" name="metadata[date_deces]" id="date_deces" value="{{ old('metadata.date_deces') }}">
                                        @error('metadata.date_deces')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="lieu_deces">Lieu du décès :</label>
                                        <input type="text" class="form-control @error('metadata.lieu_deces') is-invalid @enderror" name="metadata[lieu_deces]" id="lieu_deces" value="{{ old('metadata.lieu_deces') }}">
                                        @error('metadata.lieu_deces')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="certificat-vie-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Certificat de Vie</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_demandeur_vie">Nom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.nom_demandeur') is-invalid @enderror" name="metadata[nom_demandeur]" id="nom_demandeur_vie" value="{{ old('metadata.nom_demandeur') }}">
                                        @error('metadata.nom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_demandeur_vie">Prénom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_demandeur') is-invalid @enderror" name="metadata[prenom_demandeur]" id="prenom_demandeur_vie" value="{{ old('metadata.prenom_demandeur') }}">
                                        @error('metadata.prenom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="certificat-entretien-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Certificat d'Entretien</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_demandeur_entretien">Nom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.nom_demandeur') is-invalid @enderror" name="metadata[nom_demandeur]" id="nom_demandeur_entretien" value="{{ old('metadata.nom_demandeur') }}">
                                        @error('metadata.nom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_demandeur_entretien">Prénom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_demandeur') is-invalid @enderror" name="metadata[prenom_demandeur]" id="prenom_demandeur_entretien" value="{{ old('metadata.prenom_demandeur') }}">
                                        @error('metadata.prenom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="certificat-revenu-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Certificat de Non Revenu</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_demandeur_revenu">Nom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.nom_demandeur') is-invalid @enderror" name="metadata[nom_demandeur]" id="nom_demandeur_revenu" value="{{ old('metadata.nom_demandeur') }}">
                                        @error('metadata.nom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_demandeur_revenu">Prénom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_demandeur') is-invalid @enderror" name="metadata[prenom_demandeur]" id="prenom_demandeur_revenu" value="{{ old('metadata.prenom_demandeur') }}">
                                        @error('metadata.prenom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div id="certificat-divorce-fields" style="display:none;">
                                <h3 class="section-title mb-3">Détails Certificat de Divorce</h3>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label" for="nom_demandeur_divorce">Nom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.nom_demandeur') is-invalid @enderror" name="metadata[nom_demandeur]" id="nom_demandeur_divorce" value="{{ old('metadata.nom_demandeur') }}">
                                        @error('metadata.nom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="prenom_demandeur_divorce">Prénom du demandeur :</label>
                                        <input type="text" class="form-control @error('metadata.prenom_demandeur') is-invalid @enderror" name="metadata[prenom_demandeur]" id="prenom_demandeur_divorce" value="{{ old('metadata.prenom_demandeur') }}">
                                        @error('metadata.prenom_demandeur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="date_evenement_divorce">Date de l'événement (ex: date de divorce) :</label>
                                        <input type="date" class="form-control @error('metadata.date_evenement') is-invalid @enderror" name="metadata[date_evenement]" id="date_evenement_divorce" value="{{ old('metadata.date_evenement') }}">
                                        @error('metadata.date_evenement')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="motif_divorce">Motif (si applicable) :</label>
                                        <textarea class="form-control @error('metadata.motif') is-invalid @enderror" name="metadata[motif]" id="motif_divorce" rows="3">{{ old('metadata.motif') }}</textarea>
                                        @error('metadata.motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <h3 class="section-title mb-3">Pièces Justificatives</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label" for="justificatif">Pièce jointe (Scan lisible du document original si disponible ou autre justificatif pertinent)</label>
                                    <input type="file" class="form-control @error('justificatif') is-invalid @enderror" name="justificatif" id="justificatif">
                                    @error('justificatif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="duplicataFields" style="display:none;">
                            <h3 class="section-title mb-3">Duplicata - Renseignez le numéro du registre</h3>
                            <div class="mb-3">
                                <label class="form-label" for="registry_number">N° du registre de l'acte original</label>
                                <input type="text" class="form-control @error('registry_number') is-invalid @enderror" name="registry_number" id="registry_number" placeholder="Ex: REG-NAI-20230101-ABCDE" value="{{ old('registry_number') }}">
                                @error('registry_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="idFront">Pièce d'identité (recto) du demandeur</label>
                                <input type="file" class="form-control @error('idFront') is-invalid @enderror" name="idFront" id="idFront">
                                @error('idFront')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label" for="commune_id_common">Commune ou Sous-Préfecture</label>
                            <select class="form-select @error('commune_id') is-invalid @enderror" name="commune_id" id="commune_id_common" required>
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

                        <div>
                            <div class="form-check mb-3">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="terms">
                                    Je certifie que les informations fournies sont exactes
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-dark w-100 py-2">Soumettre la Demande</button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="col-12 col-md-5 mb-4 mb-md-0">
                <div class="hero-section h-100 text-center p-4 rounded shadow-sm d-flex flex-column justify-content-center align-items-center" style="background-color: #f8f9fa;">
                    <img src="{{ asset('img/signature.jpg') }}" alt="Documents officiels" class="hero-img img-fluid mb-4 rounded">
                    <div class="hero-content">
                        <h2 class="mb-3 text-primary">Simplifiez vos démarches administratives</h2>
                        <p class="mb-4 text-muted">Demandez et obtenez vos documents officiels en ligne, rapidement et en toute sécurité.</p>
                        <ul class="list-unstyled text-start mx-auto" style="max-width: 250px;">
                            <li class="feature-item mb-2">
                                <i class="fas fa-check me-2 text-success"></i> **Processus 100% en ligne**
                            </li>
                            <li class="feature-item mb-2">
                                <i class="fas fa-check me-2 text-success"></i> **Documents légaux et authentiques**
                            </li>
                            <li class="feature-item">
                                <i class="fas fa-check me-2 text-success"></i> **Suivi de votre demande en temps réel**
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .main-container {
        max-width: 1200px;
        animation: fadeIn 1s ease-out;
    }
    .form-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .form-section {
        background-color: #ffffff;
    }
    .section-title {
        color: #343a40;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .hero-section {
        background-color: #e9ecef;
        padding: 30px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .hero-img {
        max-width: 80%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .feature-item {
        font-size: 1.1rem;
        color: #555;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const newRequestRadio = document.getElementById('newRequest');
        const duplicataRadio = document.getElementById('duplicataRequest');
        const newRequestFields = document.getElementById('newRequestFields');
        const duplicataFields = document.getElementById('duplicataFields');

        // Get all type-specific metadata field containers
        const naissanceFields = document.getElementById('naissance-fields');
        const mariageFields = document.getElementById('mariage-fields');
        const decesFields = document.getElementById('deces-fields');
        const certificatVieFields = document.getElementById('certificat-vie-fields');
        const certificatEntretienFields = document.getElementById('certificat-entretien-fields');
        const certificatRevenuFields = document.getElementById('certificat-revenu-fields');
        const certificatDivorceFields = document.getElementById('certificat-divorce-fields');

        // Map of type-specific field containers
        const typeFieldContainers = {
            'naissance': naissanceFields,
            'mariage': mariageFields,
            'deces': decesFields,
            'certificat-vie': certificatVieFields,
            'certificat-entretien': certificatEntretienFields,
            'certificat-revenu': certificatRevenuFields,
            'certificat-divorce': certificatDivorceFields
        };

        // Define required fields for each type
        const requiredFieldsMap = {
            'naissance': ['nom_enfant', 'prenom_enfant', 'date_naissance', 'lieu_naissance', 'sexe', 'nom_pere', 'nationalite_pere', 'nom_mere', 'nationalite_mere'],
            'mariage': ['nom_epoux', 'prenom_epoux', 'nom_epouse', 'prenom_epouse', 'date_mariage', 'lieu_mariage'],
            'deces': ['nom_defunt', 'prenom_defunt', 'date_deces', 'lieu_deces'],
            'certificat-vie': ['nom_demandeur', 'prenom_demandeur'],
            'certificat-entretien': ['nom_demandeur', 'prenom_demandeur'],
            'certificat-revenu': ['nom_demandeur', 'prenom_demandeur'],
            'certificat-divorce': ['nom_demandeur', 'prenom_demandeur', 'date_evenement', 'motif']
        };

        // Get the registry_number and idFront inputs in the duplicata section
        const duplicataRegistryNumberInput = duplicataFields.querySelector('input[name="registry_number"]');
        const duplicataIdFrontInput = duplicataFields.querySelector('input[name="idFront"]');

        // Get common required inputs
        const commonRequiredInputs = [
            document.getElementById('commune_id_common'),
            document.getElementById('terms')
        ];

        function updateFormVisibility() {
            const selectedType = typeSelect.value;
            const isDuplicata = duplicataRadio.checked;

            // Hide all type-specific metadata containers and remove their required attributes
            // This also hides the "Informations Générales du Document" (registry_page, registry_volume)
            // as they are part of the newRequestFields div.
            Object.values(typeFieldContainers).forEach(container => {
                if (container) {
                    container.style.display = 'none';
                    container.querySelectorAll('input, select, textarea').forEach(input => {
                        input.removeAttribute('required');
                    });
                }
            });

            // Handle 'is_duplicata' toggle
            if (isDuplicata) {
                newRequestFields.style.display = 'none';
                duplicataFields.style.display = 'block';

                // Set required for duplicata fields
                if (duplicataRegistryNumberInput) {
                    duplicataRegistryNumberInput.setAttribute('required', 'required');
                }
                if (duplicataIdFrontInput) {
                    duplicataIdFrontInput.setAttribute('required', 'required');
                }

                // Remove required from 'justificatif' for duplicata
                const justificatifInput = document.getElementById('justificatif');
                if (justificatifInput) {
                    justificatifInput.removeAttribute('required');
                }

            } else { // Nouvelle Demande
                newRequestFields.style.display = 'block';
                duplicataFields.style.display = 'none';

                // Remove required from duplicata fields
                if (duplicataRegistryNumberInput) {
                    duplicataRegistryNumberInput.removeAttribute('required');
                }
                if (duplicataIdFrontInput) {
                    duplicataIdFrontInput.removeAttribute('required');
                }

                // Set 'justificatif' as required for new requests
                const justificatifInput = document.getElementById('justificatif');
                if (justificatifInput) {
                    justificatifInput.setAttribute('required', 'required');
                }

                // Show and set required for the selected document type's fields
                const currentTypeContainer = typeFieldContainers[selectedType];
                if (currentTypeContainer) {
                    currentTypeContainer.style.display = 'block';
                    const fieldsToRequire = requiredFieldsMap[selectedType];
                    if (fieldsToRequire) {
                        fieldsToRequire.forEach(fieldName => {
                            const input = currentTypeContainer.querySelector(`[name="metadata[${fieldName}]"]`);
                            if (input) {
                                input.setAttribute('required', 'required');
                            }
                        });
                    }
                }
            }

            // Ensure common required inputs are always set
            commonRequiredInputs.forEach(input => {
                if (input) {
                    input.setAttribute('required', 'required');
                }
            });
        }

        // Event listeners
        typeSelect.addEventListener('change', updateFormVisibility);
        newRequestRadio.addEventListener('change', updateFormVisibility);
        duplicataRadio.addEventListener('change', updateFormVisibility);

        // Initial call to set the correct state based on pre-selected values or defaults
        updateFormVisibility();
    });
</script>

@endsection
