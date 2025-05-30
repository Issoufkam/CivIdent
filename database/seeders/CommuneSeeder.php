<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commune;
use Illuminate\Support\Str; // Importez la classe Str pour les fonctions de manipulation de chaînes

class CommuneSeeder extends Seeder
{
    /**
     * Exécute les seeds de la base de données.
     */
    public function run(): void
    {
        // Données des communes avec leur nom et leur région
        $communesData = [
            // Région d'Abidjan
            ['name' => 'Abobo', 'region' => 'Abidjan'],
            ['name' => 'Adjamé', 'region' => 'Abidjan'],
            ['name' => 'Anyama', 'region' => 'Abidjan'],
            ['name' => 'Attécoubé', 'region' => 'Abidjan'],
            ['name' => 'Bingerville', 'region' => 'Abidjan'],
            ['name' => 'Cocody', 'region' => 'Abidjan'],
            ['name' => 'Koumassi', 'region' => 'Abidjan'],
            ['name' => 'Marcory', 'region' => 'Abidjan'],
            ['name' => 'Port-Bouët', 'region' => 'Abidjan'],
            ['name' => 'Songon', 'region' => 'Abidjan'],
            ['name' => 'Treichville', 'region' => 'Abidjan'],
            ['name' => 'Yopougon', 'region' => 'Abidjan'],

            // Région des Lagunes
            ['name' => 'Dabou', 'region' => 'Lagunes'],
            ['name' => 'Grand-Lahou', 'region' => 'Lagunes'],
            ['name' => 'Jacqueville', 'region' => 'Lagunes'],

            // Région du Denguélé
            ['name' => 'Odienné', 'region' => 'Denguélé'],
            ['name' => 'Samatiguila', 'region' => 'Denguélé'],
            ['name' => 'Minignan', 'region' => 'Denguélé'],

            // Région du Worodougou
            ['name' => 'Séguéla', 'region' => 'Worodougou'],
            ['name' => 'Mankono', 'region' => 'Worodougou'],
            ['name' => 'Kani', 'region' => 'Worodougou'],

            // Région du Bas-Sassandra
            ['name' => 'San-Pédro', 'region' => 'Bas-Sassandra'],
            ['name' => 'Sassandra', 'region' => 'Bas-Sassandra'],
            ['name' => 'Tabou', 'region' => 'Bas-Sassandra'],

            // Ajoutez d'autres communes et régions selon vos besoins
        ];

        foreach ($communesData as $data) {
            // Génère le code de la région (3 premières lettres en majuscules)
            $regionCode = Str::upper(Str::substr($data['region'], 0, 3));
            // Génère le code de la commune (3 premières lettres en majuscules)
            $communeCode = Str::upper(Str::substr($data['name'], 0, 3));

            // Concatène pour former le code complet
            $fullCode = "CIV-{$regionCode}-{$communeCode}";

            // Crée l'entrée dans la base de données
            Commune::create([
                'name' => $data['name'],
                'code' => $fullCode,
                'region' => $data['region'],
            ]);
        }
    }
}
