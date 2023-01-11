<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvSkillSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'technology_id',
        'experience'
    ];

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
