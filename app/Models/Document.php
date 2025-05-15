<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Enums\DocumentStatut;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'type',
        'status',
        'registry_number',
        'registry_page',
        'registry_volume',
        'metadata',
        'commune_id',
        'user_id',
        'agent_id',
        'decision_date',
        'comments',
        'justificatif_path',
    ];

    protected $casts = [
        'metadata' => 'array',
        'decision_date' => 'datetime',
        'status' => DocumentStatut::class,
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function payments()
    {
        return $this->hasMany(Paiement::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function getStatusAttribute($value)
    {
        return $value === 1 ? 'Actif' : 'Inactif';
    }

    public function statusColor()
    {
        return match ($this->status) {
            'EN_ATTENTE' => 'warning',
            'APPROUVE' => 'success',
            'REJETE' => 'danger',
            default => 'secondary',
        };
    }

    public function statusLabel()
    {
        return ucfirst(strtolower($this->status));
    }

}
