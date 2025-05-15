<?php

namespace Database\Seeders;

use App\Models\Paiement;
use App\Models\User;
use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class PaiementSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $documents = Document::all();

        $statuts = ['success', 'pending', 'failed'];

        foreach (range(1, 20) as $i) {
            Paiement::create([
                'montant' => rand(1000, 10000),
                'reference' => Str::upper(Str::random(10)),
                'method' => 'Mobile Money',
                'status' => Arr::random($statuts),
                'user_id' => $users->random()->id,
                'document_id' => $documents->random()->id,
            ]);
        }
    }
}
