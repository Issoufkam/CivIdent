<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = ['utilisateur_id', 'commune_id'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function demandesTraitees()
    {
        return $this->hasMany(Demande::class, 'agent_id');
    }
}

