<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $fillable = ['name', 'code', 'region'];

    // Demandes de la commune
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Utilisateurs associés (agents)
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
