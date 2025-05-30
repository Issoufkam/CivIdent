<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Extrait d'Acte de Mariage - République de Côte d'Ivoire</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Lato:wght@400;700&display=swap');

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

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Lato', sans-serif;
      background-color: #f5f5f5;
      color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      padding: 20px;
    }

    .certificate {
      background-color: #fff;
      width: 100%;
      max-width: 800px;
      padding: 40px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      position: relative;
      border: 1px solid #ccc;
      animation: fadeIn 1s ease-in-out;
      overflow: hidden;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .certificate::before {
      content: '';
      position: absolute;
      top: 10px;
      left: 10px;
      right: 10px;
      bottom: 10px;
      border: 2px solid #e0e0e0;
      pointer-events: none;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      position: relative;
    }

    .republic {
      font-size: 14px;
      text-transform: uppercase;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .motto {
      font-size: 12px;
      font-style: italic;
      margin-bottom: 15px;
    }

    .flag-colors {
      display: flex;
      height: 10px;
      width: 100%;
      margin-bottom: 20px;
    }

    .flag-colors div { flex: 1; height: 100%; }
    .color-orange { background-color: #F77F00; }
    .color-white { background-color: #FFFFFF; }
    .color-green { background-color: #009A44; }

    .coat-of-arms {
      width: 80px;
      height: 80px;
      margin: 0 auto 15px;
      background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Coat_of_arms_of_Ivory_Coast.svg/800px-Coat_of_arms_of_Ivory_Coast.svg.png');
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center;
    }

    h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 5px;
      font-weight: 700;
    }

    .subtitle {
      font-size: 16px;
      margin-bottom: 20px;
      font-weight: 600;
    }

    .registry-info {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 30px;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 15px;
    }

    .registry-info div {
      flex: 1;
      min-width: 120px;
    }

    .section-title {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .info-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .info-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }

    .info-field {
      flex: 1;
      min-width: 200px;
    }

    .info-label {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .info-value {
      padding: 5px 0;
      border-bottom: 1px dotted #ccc;
      min-height: 30px;
    }

    .official-section {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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
      text-align: center;
      transform: rotate(-15deg);
    }

    .signature {
      text-align: center;
    }

    .signature-line {
      width: 200px;
      border-bottom: 1px solid #333;
      margin: 50px auto 10px;
    }

    .official-title {
      font-size: 14px;
      font-weight: bold;
    }

    .document-number {
      position: absolute;
      top: 20px;
      right: 40px;
      font-size: 14px;
      font-weight: bold;
    }

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      font-size: 80px;
      opacity: 0.05;
      pointer-events: none;
      font-weight: bold;
      white-space: nowrap;
      color: #000;
      z-index: 0;
    }

    @media (max-width: 600px) {
      .certificate { padding: 20px; }
      h1 { font-size: 20px; }
      .info-field { min-width: 100%; }
      .registry-info { flex-direction: column; }
      .official-section { flex-direction: column-reverse; gap: 30px; }
      .stamp { margin: 0 auto; }
    }
  </style>
</head>
<body>
  <div class="certificate">
    <div class="document-number">N° {{ $document->numero ?? '00-0000/2023' }}</div>
    <div class="watermark">
    <img src="https://coloriageenfant.com/wp-content/uploads/2023/06/coloriage-armoiries-de-la-cote-divoire.jpg" class="background" alt="Armoirie de la Côte d'Ivoire">

    </div>

    <div class="header">
      <div class="republic">RÉPUBLIQUE DE CÔTE D'IVOIRE</div>
      <div class="motto">UNION - DISCIPLINE - TRAVAIL</div>
      <div class="flag-colors">
        <div class="color-orange"></div>
        <div class="color-white"></div>
        <div class="color-green"></div>
      </div>
      <div class="coat-of-arms"></div>
      <h1>Extrait du Registre des Actes de l'État Civil</h1>
      <div class="subtitle">Acte de Mariage</div>
    </div>

    <div class="registry-info">
      <div>
        <div class="section-title">CENTRE D'ÉTAT CIVIL</div>
        <div>{{ $document->commune->name ?? 'Abidjan-Plateau' }}</div>
      </div>
      <div>
        <div class="section-title">ANNÉE</div>
        <div>{{ $document->annee ?? now()->year }}</div>
      </div>
      <div>
        <div class="section-title">REGISTRE</div>
        <div>{{ $document->registre ?? 'Mariages' }}</div>
      </div>
      <div>
        <div class="section-title">NUMÉRO</div>
        <div>{{ $document->numero ?? '0789/2023' }}</div>
      </div>
    </div>

    <div class="info-container">
      <div class="info-row">
        <div class="info-field">
          <div class="info-label">DATE DU MARIAGE</div>
          <div class="info-value">{{ \Carbon\Carbon::parse($document->date_mariage)->translatedFormat('d F Y') ?? '15 Septembre 2023' }}</div>
        </div>
        <div class="info-field">
          <div class="info-label">LIEU DU MARIAGE</div>
          <div class="info-value">{{ $document->lieu_mariage ?? 'Mairie du Plateau, Abidjan' }}</div>
        </div>
      </div>

      <div class="section-title">ÉPOUX</div>
      <div class="info-row">
        <div class="info-field">
          <div class="info-label">NOM</div>
          <div class="info-value">{{ $document->metadata['nom_epoux'] ?? 'KOFFI' }}</div>
        </div>
        <div class="info-field">
          <div class="info-label">PRÉNOMS</div>
          <div class="info-value">{{ $document->metadata['prenoms_epoux'] ?? 'Yao Emmanuel' }}</div>
        </div>
      </div>
      <div class="info-row">
        <div class="info-field">
          <div class="info-label">PROFESSION</div>
          <div class="info-value">{{ $document->metadata['profession_epoux'] ?? 'Architecte' }}</div>
        </div>
        <div class="info-field">
          <div class="info-label">DOMICILE</div>
          <div class="info-value">{{ $document->metadata['domicile_epoux'] ?? 'Cocody, Abidjan' }}</div>
        </div>
      </div>

      <!-- Partie ÉPOUSE à ajouter ici si souhaité -->

      <div class="official-section">
        <div class="signature">
          <div class="signature-line"></div>
          <div class="official-title">Officier de l'État Civil</div>
        </div>
        <div class="stamp">Cachet de la mairie
        {{-- @if(isset($timbre)) --}}
          <img src="https://uvicoci.com/wp-content/uploads/2021/08/240879580_4674468529265097_2133671838465871025_n.jpg" alt="Timbre officiel" style="width: 100%; height: 100%; object-fit: contain;">
        {{-- @else --}}
          Cachet Officiel
        {{-- @endif --}}
        </div>
      </div>
    </div>
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
