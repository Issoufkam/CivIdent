<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Commune;
use App\Models\Document;
use Illuminate\Support\Arr;

// Récupérer uniquement les utilisateurs avec un rôle spécifique
$citoyens = User::where('role', 'citoyen')->get();
// $agents = User::where('role', 'agent')->get();
$communes = Commune::all();

$types = ['naissance', 'mariage', 'deces', 'vie', 'revenue', 'entretien'];
$statuts = ['en_attente', 'actif', 'inactif'];

foreach (range(1, 100) as $i) {
    Document::create([
        'type' => Arr::random($types),
        'status' => Arr::random($statuts),
        'registry_number' => 'RN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
        'registry_page' => 'P-' . rand(1, 50),
        'registry_volume' => 'V-' . rand(1, 20),
        'metadata' => ['note' => 'Généré automatiquement'],
        'commune_id' => Commune::inRandomOrder()->first()->id,
        'user_id' => $citoyens->random()->id,
        // 'agent_id' => $agents->random()->id,
        'decision_date' => now()->subDays(rand(0, 365)),
        'comments' => 'Auto-généré via seeder',
        'justificatif_path' => 'documents/justif_' . $i . '.pdf',
    ]);
}
