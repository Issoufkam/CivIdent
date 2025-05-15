<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Document;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Si tu utilises API

/**
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $documents
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Document[] $validatedDocuments
 */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'role',
        'commune_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relations

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function validatedDocuments()
    {
        return $this->hasMany(Document::class, 'agent_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }


}
