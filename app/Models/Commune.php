<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $fillable = ['nom_commune', 'region', 'code_postal'];

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function actes()
    {
        return $this->hasMany(Acte::class);
    }
}
