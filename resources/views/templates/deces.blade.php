<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Extrait d'Acte de Décès</title>
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
    .background {
      position: absolute;
      top: 25%;
      left: 25%;
      width: 50%;
      opacity: 0.08;
      z-index: 0;
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
  </style>
</head>
<body class="bg-light d-flex justify-content-center align-items-center min-vh-100">
  <div class="position-relative bg-white shadow p-4 p-md-5 rounded w-100" style="max-width: 800px;">

    <!-- Filigrane -->
    <img src="https://coloriageenfant.com/wp-content/uploads/2023/06/coloriage-armoiries-de-la-cote-divoire.jpg" class="background" alt="Armoirie de la Côte d'Ivoire">

    <!-- Numéro -->
    <div class="position-absolute top-0 end-0 mt-3 me-4 fw-bold">N° {{ $document->numero ?? '00-0000/2023' }}</div>

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
      <div class="fw-semibold">Acte de Décès</div>
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
        <div>Décès</div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="fw-bold small">NUMÉRO</div>
        <div>{{ $document->numero_registre ?? '0458/2023' }}</div>
      </div>
    </div>

    <!-- Informations du défunt -->
    <div class="mb-4">
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">NOM</div>
          <div class="border-bottom pb-1">{{ $document->metadata['nom_defunt'] ?? 'YAO' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">PRÉNOMS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['prenoms_defunt'] ?? 'Jean Baptiste' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">SEXE</div>
          <div class="border-bottom pb-1">{{ $document->metadata['sexe'] ?? 'Masculin' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">DATE DE DÉCÈS</div>
          <div class="border-bottom pb-1">
            @if(isset($document->metadata['date_deces']))
              {{ \Carbon\Carbon::parse($document->metadata['date_deces'])->translatedFormat('d F Y') }}
            @else
              10 Janvier 2023
            @endif
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="fw-bold small">ÂGE AU DÉCÈS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['age'] ?? '72 ans' }}</div>
        </div>
        <div class="col-md-6">
          <div class="fw-bold small">LIEU DE DÉCÈS</div>
          <div class="border-bottom pb-1">{{ $document->metadata['lieu_deces'] ?? 'CHU de Treichville, Abidjan' }}</div>
        </div>
      </div>
      <div class="mb-3">
        <div class="fw-bold small">DOMICILE</div>
        <div class="border-bottom pb-1">{{ $document->metadata['domicile'] ?? 'Abobo Baoulé, Abidjan' }}</div>
      </div>
    </div>

    <!-- Signature et cachet -->
    <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap gap-3">
      <div class="text-center">
        <p>L’Officier d’état civil</p>
        @if (!empty($signaturePath))
            <img src="{{ asset($signaturePath) }}" alt="Signature" width="100">
        @else
            <p>Signature non disponible</p>
        @endif
        <p class="fw-bold">{{ $document->agent?->nom ?? Auth::user()->nom }}</p>
      </div>
      <div class="stamp">
        <img src="https://uvicoci.com/wp-content/uploads/2021/08/240879580_4674468529265097_2133671838465871025_n.jpg" alt="Cachet officiel" style="width: 100%; height: 100%; object-fit: contain;">
      </div>
    </div>

    <!-- Date -->
    <div class="text-end mt-3 fst-italic">
      Fait à {{ $document->commune->name ?? 'Abidjan' }}, le {{ \Carbon\Carbon::parse($document->created_at ?? now())->translatedFormat('d F Y') }}
    </div>

    <!-- Boutons -->
    <div class="text-end mt-3">
      <button class="btn btn-outline-primary" onclick="window.print()">
        Imprimer
      </button>
      <a href="{{ route('agent.documents.show', $document->id) }}" class="btn btn-secondary">
        Retour
      </a>
    </div>
  </div>
</body>
</html>
