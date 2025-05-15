<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Commune;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'role' => $this->faker->randomElement(['admin', 'agent', 'citoyen']),
            'commune_id' => \App\Models\Commune::inRandomOrder()->first()?->id,
            'password' => bcrypt('password'), // ou Hash::make('password')
        ];
    }
}
