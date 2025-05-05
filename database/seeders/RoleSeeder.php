<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crée les rôles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'citoyen']);
        // Ajouter d'autres rôles si nécessaire
    }
}
