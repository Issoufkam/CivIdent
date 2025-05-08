<?php

namespace App\Enums;

enum DemandeStatut: string
{
    case EN_ATTENTE = 'en_attente';
    case APPROUVEE = 'approuvee';
    case REJETEE = 'rejetee';
    case ANNULEE = 'annulee';
}
