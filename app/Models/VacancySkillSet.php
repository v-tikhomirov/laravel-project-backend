<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VacancySkillSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'technology_id',
        'experience'
    ];

    public function technology(): HasOne
    {
        return $this->hasOne(Technology::class, 'id', 'technology_id');
    }
}
