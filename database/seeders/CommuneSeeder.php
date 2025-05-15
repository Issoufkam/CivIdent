<?php

namespace Database\Seeders;

use App\Models\Commune;
use Illuminate\Database\Seeder;

class CommuneSeeder extends Seeder
{
    public function run(): void
    {
        $communes = [
            ['nom_commune' => 'Yopougon', 'code' => 'YOP', 'region' => 'Abidjan'],
            ['nom_commune' => 'Cocody', 'code' => 'COC', 'region' => 'Abidjan'],
            ['nom_commune' => 'Bouaké', 'code' => 'BKE', 'region' => 'Gbêkê'],
            ['nom_commune' => 'Korhogo', 'code' => 'KOR', 'region' => 'Savanes'],
            ['nom_commune' => 'San Pedro', 'code' => 'SP', 'region' => 'Bas-Sassandra'],
            ['nom_commune' => 'Daloa', 'code' => 'DAL', 'region' => 'Lôh-Djiboua'],
            ['nom_commune' => 'Sikensi', 'code' => 'SIK', 'region' => 'Lôh-Djiboua'],
            ['nom_commune' => 'Tanda', 'code' => 'TAN', 'region' => 'Zanzan'],
            ['nom_commune' => 'Bondoukou', 'code' => 'BON', 'region' => 'Zanzan'],
            ['nom_commune' => 'Ferkessédougou', 'code' => 'FER', 'region' => 'Savanes'],
            ['nom_commune' => 'Agboville', 'code' => 'AGB', 'region' => 'Lagunes'],
            ['nom_commune' => 'Yamoussoukro', 'code' => 'YAM', 'region' => 'Lacs'],
            ['nom_commune' => 'Divo', 'code' => 'DIV', 'region' => 'Lôh-Djiboua'],
        ];

        foreach ($communes as $commune) {
            Commune::create($commune);
        }
    }
}
