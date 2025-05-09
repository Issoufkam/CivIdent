<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = ['demande_id', 'montant', 'date_paiement', 'mode_paiement'];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}

