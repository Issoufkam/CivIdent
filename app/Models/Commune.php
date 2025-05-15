<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_commune',
        'code',
        'region',
    ];

    // Relations

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }
    public function payments()
    {
        return $this->hasMany(Paiement::class);
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
