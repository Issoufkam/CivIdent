<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'commune_id',
        'year',
        'month',
        'count',
    ];

    // Relation avec la commune
    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
