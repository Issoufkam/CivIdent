<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'mime_type',
        'document_id',
    ];

    // Relation avec le document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
