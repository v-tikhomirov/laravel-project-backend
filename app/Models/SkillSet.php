<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'technology_id',
        'experience'
    ];

    static function prepareSkills($skills, $idAsKey = false): array
    {
        $out = [];
        foreach ($skills as $row) {
            $out[$row['technology_id']] = $row;
        }

        return $out;
    }

    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
