<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchesHistory extends Model
{
    protected $fillable = [
        'type',
        'action'
    ];
    use HasFactory;

    public function match(): HasOne
    {
        return $this->hasOne(Matching::class);
    }
}
