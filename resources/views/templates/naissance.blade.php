<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Extrait d'Acte de Naissance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
        button, .btn, .alert, .navbar, .footer {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .card, .detail-view {
            box-shadow: none !important;
            border: 1px solid #000;
        }
    }
    .coat-of-arms {
      width: 80px;
      height: 80px;
      background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Coat_of_arms_of_Ivory_Coast.svg/800px-Coat_of_arms_of_Ivory_Coast.svg.png');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }
    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 100px;
      opacity: 0.03;
      font-weight: bold;
      color: black;
      white-space: nowrap;
      pointer-events: none;
    }
    .signature-line {
      width: 200px;
      border-bottom: 1px solid #000;
      margin: 50px auto 10px;
    }
    .stamp {
      width: 120px;
      height: 120px;
      border: 1px dashed #999;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: #999;
      transform: rotate(-15deg);
    }
    .background {
      position: absolute;
      top: 25%;
      left: 25%;
      width: 50%;
      opacity: 0.08;
      z-index: 0;
    }
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
  <div class="position-relative bg-white shadow p-4 p-md-5 rounded w-100" style="max-width: 800px;">
    <!-- Filigrane -->
    <img src="https://coloriageenfant.com/wp-content/uploads/2023/06/coloriage-armoiries-de-la-cote-divoire.jpg" class="background" alt="Armoirie de la Côte d'Ivoire">

    <!-- Numéro de document -->
    <div class="position-absolute top-0 end-0 mt-3 me-4 fw-bold">N° {{ $document->numero ?? '00-0000/2023' }}</div>
    {{-- <div class="watermark">CÔTE D'IVOIRE</div> --}}

    <!-- En-tête -->
    <div class="text-center mb-4">
      <div class="text-uppercase fw-bold small">RÉPUBLIQUE DE CÔTE D'IVOIRE</div>
      <div class="fst-italic small">UNION - DISCIPLINE - TRAVAIL</div>
      <div class="d-flex my-2" style="height: 10px;">
        <div class="flex-fill bg-warning"></div>
        <div class="flex-fill bg-white"></div>
        <div class="flex-fill bg-success"></div>
      </div>
      <div class="coat-of-arms mx-auto mb-2"></div>
      <h1 class="h4 text-uppercase fw-bold">Extrait du Registre des Actes de l'État Civil</h1>
      <div class="fw-semibold">Acte de Naissance</div>
    </div>

    <!-- Informations du registre -->
    <div class="row border-bottom pb-3 mb-4">
      <div class="col-sm-6 col-md-3">
        <div class="fw-bold small">CENTRE D'ÉTAT CIVIL</div>
        <div>Commune de {{ $document->commune->name ?? 'Abidjan-Plateau' }}</div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="fw-bold small">ANNÉE</div>
        <div>{{ $document->annee ?? now()->year }}</div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="fw-bold small">REGISTRE</div>
        <div>Naissances</div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="fw-bold small">NUMÉRO</div>
        <div>{{ $document->numero_registre ?? '0246/2023' }}</div>
      </div>
    </div>

    <!-- Informations de l'enfant -->
    <div class="mb-4">
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">NOM</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nom_enfant'] ?? 'KOUASSI' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">PRÉNOMS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['prenoms_enfant'] ?? 'Aya Marie' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">SEXE</div>
          <div class="border-bottom pb-1">{{ $document->metadata['sexe'] ?? 'Féminin' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">DATE DE NAISSANCE</div>
          <div class="border-bottom pb-1">
            @if(isset($document->metadata['date_naissance']))
              {{ \Carbon\Carbon::parse($document->metadata['date_naissance'])->translatedFormat('d F Y') }}
            @else
              15 Avril 2023
            @endif
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">HEURE DE NAISSANCE</div>
          <div class="border-bottom pb-1">{{ $document->metadata['heure_naissance'] ?? '08h45' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">LIEU DE NAISSANCE</div>
          <div class="border-bottom pb-1">{{ $document->metadata['lieu_naissance'] ?? 'CHU de Cocody, Abidjan' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">PÈRE - NOM ET PRÉNOMS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nom_pere'] ?? 'KOUASSI Koffi Jean' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">Nationalité</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nationalite_pere'] ?? 'Ivoirienne' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">MÈRE - NOM ET PRÉNOMS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nom_mere'] ?? 'DIALLO Aminata' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">Nationalité</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nationalite'] ?? 'Ivoirienne' }}</div>
        </div>
      </div>
    </div>

    <!-- Pied de page -->
    <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
      <div class="text-center">
            <div class="signature-block">
                <p>L’Officier d’état civil</p>
                {{-- {{ dd($agentSignaturePath) }} --}}
                    @if ($agentSignaturePath)
                        <img src="{{ asset('storage/' . $agent->signature_path) }}" alt="Signature de l'agent" width="100">
                    @else
                        <p>Signature non disponible</p>
                    @endif
                    <p class="fw-bold">
                        {{ $agent->prenom ?? 'PRÉNOM_AGENT' }}
                        {{ $agent->nom ?? 'NOM_AGENT' }}
                    </p>
            </div>
        </div>

      <div class="stamp">
        {{-- @if(isset($timbre)) --}}
          <img src="https://uvicoci.com/wp-content/uploads/2021/08/240879580_4674468529265097_2133671838465871025_n.jpg" alt="Timbre officiel" style="width: 100%; height: 100%; object-fit: contain;">
        {{-- @else --}}
          Cachet Officiel
        {{-- @endif --}}
      </div>
    </div>

    <!-- Date de création -->
    <div class="text-end mt-3 fst-italic">
      Fait à {{ $document->commune->name }},
      le {{ \Carbon\Carbon::parse($document->created_at ?? now())->translatedFormat('d F Y') }}
    </div>
    <div class="mb-3 text-end">
    <button class="btn btn-outline-primary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Imprimer
    </button>
        <a href="{{ route('citoyen.paiements.confirmation', $document->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
  </div>
</body>
</html>


