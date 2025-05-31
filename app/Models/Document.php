<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'type',
    'user_id',
    'commune_id',
    'agent_id',
    'registry_number',
    'registry_page',
    'justificatif_path',
    'pdf_path',
    'metadata',
    'status',
    'is_paid',
    'payment_method',
    'payment_date',
    'payment_amount',
    'is_downloaded',
    'is_duplicata',
    'original_document_id'
    ];

    protected $casts = [
        'type' => DocumentType::class,
        'status' => DocumentStatus::class,
        'metadata' => 'array',
        'is_duplicata' => 'boolean',
    ];

    // Citoyen demandeur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Commune concernée
    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    // Agent traiteur
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Pièces jointes
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    // Document original (si duplicata)
    public function original(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'original_document_id');
    }

    // Tous les duplicatas liés à ce document
    public function duplicatas(): HasMany
    {
        return $this->hasMany(Document::class, 'original_document_id');
    }

    // Formattage de la date de traitement
    public function getTraitementDateFormattedAttribute()
    {
        return $this->traitement_date
            ? \Carbon\Carbon::parse($this->traitement_date)->format('d/m/Y')
            : null;
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

}
