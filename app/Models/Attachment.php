<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $fillable = ['path', 'mime_type', 'document_id'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    // URL publique pour l'affichage
    public function getPublicUrlAttribute(): string
    {
        return Storage::exists($this->path)
            ? asset("storage/{$this->path}")
            : asset("images/file-not-found.png");
    }

}
