<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder; // Assure-toi que ce fichier existe et est correctement importé

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Appel du seeder de rôles
        $this->call(RoleSeeder::class); 
    }
}
