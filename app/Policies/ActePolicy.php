<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Acte;

class ActePolicy
{
    public function view(Utilisateur $user, Acte $acte)
    {
        return $user->role->nom === 'admin' || $user->id === $acte->citoyen->utilisateur_id;
    }

    public function create(Utilisateur $user)
    {
        return $user->role->nom === 'admin';
    }

    public function update(Utilisateur $user, Acte $acte)
    {
        return $user->role->nom === 'admin';
    }

    public function delete(Utilisateur $user, Acte $acte)
    {
        return $user->role->nom === 'admin';
    }
}
