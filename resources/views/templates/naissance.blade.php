<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Extrait d'Acte de Naissance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .document-container {
            position: relative;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 800px;
            overflow: hidden; /* Important pour le background */
        }
        .coat-of-arms {
            width: 80px;
            height: 80px;
            background-image: url('{{ asset('images/coat_of_arms.png') }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0 auto;
        }
        .background {
            position: absolute;
            top: 25%;
            left: 25%;
            width: 50%;
            opacity: 0.08;
            z-index: 0;
            content: url('{{ asset('images/armoirie_cote_ivoire.png') }}');
        }
        .stamp img {
            width: 150px; /* Ajustez la taille du timbre */
            height: auto;
            object-fit: contain;
        }
        .signature-block {
            margin-top: 30px;
            text-align: center;
        }
        .signature-block img {
            max-width: 150px; /* Taille de la signature */
            height: auto;
            display: block;
            margin: 0 auto 10px;
        }
        .header-strip {
            display: flex;
            height: 10px;
            margin: 10px 0;
            border-radius: 5px; /* Rounded corners for the strip */
            overflow: hidden;
        }
        .header-strip div {
            flex: 1;
        }
        .text-small {
            font-size: 0.85em;
        }
        .text-x-small {
            font-size: 0.75em;
        }
        .border-bottom-dashed {
            border-bottom: 1px dashed #ced4da;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        /* Nouveau style pour les libellés afin d'assurer un alignement cohérent */
        .label-col {
            min-width: 140px; /* Largeur minimale pour les libellés (ajustable) */
            flex-shrink: 0; /* Empêche le libellé de rétrécir */
        }
        .info-value {
            word-wrap: break-word; /* Assure que le texte long s'enroule */
        }

        /* Styles pour l'impression */
        @media print {
            body {
                background-color: #fff !important;
                margin: 0;
                padding: 0;
            }
            .document-container {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 20px !important;
                margin: 0 !important;
            }
            .btn {
                display: none !important; /* Masque les boutons à l'impression */
            }
        }
    </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
    <div class="document-container position-relative bg-white shadow p-4 p-md-5 rounded w-100">
        <img src="{{ asset('images/armoirie_cote_ivoire.png') }}" class="background" alt="Armoirie de la Côte d'Ivoire">

        <div class="position-absolute top-0 end-0 mt-3 me-4 fw-bold text-small">N° {{ $document->registry_number ?? '00-0000/2023' }}</div>

        <div class="text-center mb-4">
            <div class="text-uppercase fw-bold text-small">RÉPUBLIQUE DE CÔTE D'IVOIRE</div>
            <div class="fst-italic text-x-small">UNION - DISCIPLINE - TRAVAIL</div>
            <div class="header-strip">
                <div class="bg-warning"></div>
                <div class="bg-white"></div>
                <div class="bg-success"></div>
            </div>
            <div class="coat-of-arms mb-2"></div>
            <h1 class="h4 text-uppercase fw-bold">Extrait du Registre des Actes de l'État Civil</h1>
            <div class="fw-semibold">Acte de Naissance</div>
        </div>

        <div class="row border-bottom pb-3 mb-4">
            <div class="col-sm-6 col-md-3 text-small">
                <div class="fw-bold">CENTRE D'ÉTAT CIVIL</div>
                <div>Commune de {{ $document->commune->name ?? 'Abidjan-Plateau' }}</div>
            </div>
            <div class="col-sm-6 col-md-3 text-small">
                <div class="fw-bold">ANNÉE</div>
                <div>{{ \Carbon\Carbon::parse($document->created_at)->year ?? now()->year }}</div>
            </div>
            <div class="col-sm-6 col-md-3 text-small">
                <div class="fw-bold">REGISTRE</div>
                <div>Naissances</div>
            </div>
            <div class="col-sm-6 col-md-3 text-small">
                <div class="fw-bold">NUMÉRO</div>
                <div>{{ $document->registry_page ?? '0246' }}</div>
            </div>
        </div>

        <div class="mb-4">
            {{-- NOM et PRÉNOMS sur la même ligne --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">NOM:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['nom_enfant'] ?? 'KOUASSI' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">PRÉNOMS:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['prenom_enfant'] ?? 'Aya Marie' }}</div>
                    </div>
                </div>
            </div>
            {{-- SEXE et DATE DE NAISSANCE sur la même ligne --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">SEXE:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['sexe'] ?? 'Féminin' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">DATE DE NAISSANCE:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">
                            @if(isset($document->metadata['date_naissance']))
                                {{ \Carbon\Carbon::parse($document->metadata['date_naissance'])->translatedFormat('d F Y') }}
                            @else
                                15 Avril 2023
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- HEURE DE NAISSANCE et LIEU DE NAISSANCE sur la même ligne --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">HEURE DE NAISSANCE:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['heure_naissance'] ?? '08h45' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">LIEU DE NAISSANCE:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['lieu_naissance'] ?? 'CHU de Cocody, Abidjan' }}</div>
                    </div>
                </div>
            </div>
            {{-- PÈRE - NOM ET PRÉNOMS et Nationalité sur la même ligne --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">PÈRE - NOM ET PRÉNOMS:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['nom_pere'] ?? 'KOUASSI Koffi Jean' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">Nationalité:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['nationalite_pere'] ?? 'Ivoirienne' }}</div>
                    </div>
                </div>
            </div>
            {{-- MÈRE - NOM ET PRÉNOMS et Nationalité sur la même ligne --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">MÈRE - NOM ET PRÉNOMS:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['nom_mere'] ?? 'DIALLO Aminata' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-baseline">
                        <div class="fw-bold text-small label-col me-2">Nationalité:</div>
                        <div class="info-value border-bottom-dashed flex-grow-1">{{ $document->metadata['nationalite_mere'] ?? 'Ivoirienne' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
            {{-- NOUVEL ORDRE : Timbre à gauche --}}
            <div class="stamp">
                {{-- Afficher le timbre (cachet) --}}
                <img src="{{ asset('images/timbre_officiel.png') }}" alt="Timbre officiel">
            </div>

            {{-- Signature à droite --}}
            <div class="text-center">
                <div class="signature-block">
                    <p class="mb-1">L’Officier d’état civil</p>
                    {{-- Afficher la signature de l'agent si disponible, sinon un texte --}}
                    @if ($document->agent && $document->agent->signature_path)
                        <img src="{{ asset('storage/' . $document->agent->signature_path) }}" alt="Signature de l'officier" width="100">
                    @else
                        <p class="text-muted fst-italic text-x-small">Signature non disponible</p>
                    @endif
                    <p class="fw-bold mb-0">{{ $document->agent?->nom ?? 'Nom de l\'Officier' }}</p>
                </div>
            </div>
        </div>

        <div class="text-end mt-3 fst-italic text-small">
            Fait à {{ $document->commune->name ?? 'Abidjan' }},
            le
            {{-- Utiliser traitement_date si disponible, sinon created_at --}}
            @if($document->traitement_date)
                {{ \Carbon\Carbon::parse($document->traitement_date)->translatedFormat('d F Y') }}
            @else
                {{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y') }}
            @endif
        </div>
        <div class="mb-3 text-end mt-4">
            <button class="btn btn-outline-primary rounded-pill px-4 py-2" onclick="window.print()">
                <i class="bi bi-printer me-2"></i> Imprimer
            </button>
            <a href="{{ route('citoyen.demandes.show', $document->id) }}" class="btn btn-secondary rounded-pill px-4 py-2 ms-2">
                <i class="bi bi-arrow-left me-2"></i> Retour
            </a>
        </div>
    </div>
</body>
</html>
