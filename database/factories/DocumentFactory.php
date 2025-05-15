<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Commune;
use App\Enums\DocumentStatut;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['naissance', 'mariage', 'deces', 'vie', 'revenue', 'entretien']),
            'status' => $this->faker->randomElement(['en_attente', 'actif', 'inactif']),
            'registry_number' => Str::upper(Str::random(8)),
            'registry_page' => 'P-' . rand(1, 100),
            'registry_volume' => 'V-' . rand(1, 50),
            'metadata' => ['source' => 'auto'],
            'commune_id' => \App\Models\Commune::inRandomOrder()->first()?->id,
            'user_id' => User::factory(),
            'agent_id' => User::factory(),
            'decision_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'comments' => $this->faker->sentence,
            'justificatif_path' => 'documents/' . $this->faker->uuid . '.pdf',
        ];
    }
}
