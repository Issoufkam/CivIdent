<?php

namespace Database\Factories;

use App\Models\Paiement;
use App\Models\User;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition(): array
    {
        return [
            'montant' => $this->faker->randomFloat(2, 1000, 10000),
            'reference' => Str::upper(Str::random(10)),
            'method' => $this->faker->randomElement(['Mobile Money', 'Carte', 'Espèce']),
            'status' => $this->faker->randomElement(['success', 'pending', 'failed']),
            'user_id' => User::factory(),
            'document_id' => Document::factory(),
        ];
    }
}
