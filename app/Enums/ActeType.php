<?php

namespace App\Enums;

enum ActeType: string
{
    case NAISSANCE = 'naissance';
    case MARIAGE = 'mariage';
    case DECES = 'deces';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
    