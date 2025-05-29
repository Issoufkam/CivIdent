<?php

namespace App\Enums;

enum DocumentType: string
{
    case NAISSANCE = 'naissance';
    case MARIAGE = 'mariage';
    case DECES = 'deces';
    case VIE = 'vie';
    case ENTRETIEN = 'entretien';
    case REVENU = 'revenu';
    case DIVORCE = 'divorce';

    public function label(): string
    {
        return match($this) {
            self::NAISSANCE => 'Extrait de naissance',
            self::MARIAGE => 'Acte de mariage',
            self::DECES => 'Acte de décès',
            self::VIE => 'Certificat de vie',
            self::ENTRETIEN => 'Certificat d\'entretien',
            self::REVENU => 'Certificat de non revenu',
            self::DIVORCE => 'Acte de divorce',
        };
    }
}
