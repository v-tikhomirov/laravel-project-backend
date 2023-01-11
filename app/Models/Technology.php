<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Technology extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight',
    ];

    public const TYPE_LANGUAGE = 'language';

    public function group(): HasMany
    {
        return $this->hasMany(static::class,'group','group');
    }
}
