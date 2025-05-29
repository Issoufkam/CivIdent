<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Acte;
use App\Models\Document;

class ActePolicy
{
    public function view(User $user, Document $document)
    {
        return $user->role->nom === 'admin' || $user->id === $document->citoyen->utilisateur_id;
    }

    public function create(User $user)
    {
        return $user->role->nom === 'admin';
    }

    public function update(User $user, Document $document)
    {
        return $user->role->nom === 'admin';
    }

    public function delete(User $user, Document $acte)
    {
        return $user->role->nom === 'admin';
    }
}
