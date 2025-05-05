<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acte extends Model
{
    protected $fillable = [
        'numero_acte', 'date_etablissement', 'citoyen_id', 'type_acte_id', 'commune_id', 'fichier_pdf'
    ];

    public function citoyen()
    {
        return $this->belongsTo(Citoyen::class);
    }

    public function typeActe()
    {
        return $this->belongsTo(TypeActe::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}

