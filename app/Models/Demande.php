<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = [
        'citoyen_id', 'acte_id', 'date_demande', 'statut', 'moyen_retrait', 'agent_id'
    ];

    public function citoyen()
    {
        return $this->belongsTo(Citoyen::class);
    }

    public function acte()
    {
        return $this->belongsTo(Acte::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }
}
