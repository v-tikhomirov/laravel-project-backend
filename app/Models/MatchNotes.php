<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchNotes extends Model
{
    use HasFactory;

    protected $table = 'match_notes';

    protected $fillable = [
      'user_id',
      'match_id',
      'message'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
