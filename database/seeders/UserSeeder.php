<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Commune;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $commune = Commune::first() ?? Commune::factory()->create();

        User::create([
            'nom' => 'Kambire',
            'prenom' => 'Issouf',
            'email' => 'issouf@example.com',
            'telephone' => '0700000001',
            'role' => 'admin',
            'commune_id' => $commune->id,
            'password' => Hash::make('password'),
        ]);

        User::factory()->count(9)->create([
            'commune_id' => $commune->id,
        ]);
    }
}
