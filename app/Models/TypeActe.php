<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeActe extends Model
{
    protected $fillable = ['libelle'];

    public function actes()
    {
        return $this->hasMany(Acte::class);
    }
}

