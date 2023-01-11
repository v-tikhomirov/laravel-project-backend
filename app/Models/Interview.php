<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_time',
        'interview_date',
        'is_current',
        'outstanding_notification'
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Matching::class);
    }
}
