<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citoyen extends Model
{
    protected $fillable = [
        'utilisateur_id', 'nom', 'prenom', 'email', 'date_naissance', 'lieu_naissance', 'nationalite', 'sexe', 'adresse', 'telephone'
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function actes()
    {
        return $this->hasMany(Acte::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }

    
}
