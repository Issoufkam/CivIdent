<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'email',
        'password',
        'photo',
        'adresse',
        'commune_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-profile.png');
    }



    // Relation avec commune (si nécessaire)
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function getActivityDescription(string $event): string
    {
        return match($event) {
            'created' => "Utilisateur créé: {$this->email}",
            'updated' => "Utilisateur modifié: {$this->email}",
            'deleted' => "Utilisateur supprimé: {$this->email}",
            default => "Action inconnue sur l'utilisateur"
        };
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

}
