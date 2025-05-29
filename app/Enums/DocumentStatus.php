<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case APPROUVEE = 'approuvee';
    case REJETEE = 'rejetee';
    case VALIDATED = 'validated';
    case ANNULEE = 'annulee';

    public function color(): string
    {
        return match($this) {
        self::EN_ATTENTE => 'warning',
        self::APPROUVEE => 'success',
        self::REJETEE => 'danger',
        self::VALIDATED => 'primary',
        self::ANNULEE => 'secondary',
    };
    }

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::APPROUVEE => 'Approuvée',
            self::REJETEE => 'Rejetée',
            self::VALIDATED => 'Validée',
            self::ANNULEE => 'Annulée',
        };
    }
}
