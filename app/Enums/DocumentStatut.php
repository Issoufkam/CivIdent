<?php

namespace App\Enums;

enum DocumentStatut: string
{
    case ACTIF = 'actif';
    case INACTIF = 'inactif';
    case EN_ATTENTE = 'en_attente';

    public function color(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::ACTIF => 'success',
            self::INACTIF => 'danger'
        };
    }

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::ACTIF => 'Actif',
            self::INACTIF => 'Inactif'
        };
    }
}
