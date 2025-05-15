<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Commune;
use App\Models\Document;
use App\Models\Paiement;
use App\Models\Attachment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // Appel de seeders personnalisés si nécessaires
        $this->call([
            // RoleSeeder::class, // Si tu as un RoleSeeder
            CommuneSeeder::class, // Si tu as un CommuneSeeder
        ]);

        // Génération des données par factory
        // Commune::factory()->count(10)->create();
        User::factory()->count(80)->create();
        Document::factory()->count(200)->create();
        Paiement::factory()->count(200)->create();
        Attachment::factory()->count(200)->create();
    }
}
