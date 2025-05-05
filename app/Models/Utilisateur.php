<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    use HasRoles;

    protected $guard_name = 'web'; // Pour Spatie

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function citoyen()
    {
        return $this->hasOne(Citoyen::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function hasRole($role)
    {
        return $this->role && $this->role->nom === $role;
    }


}
