<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'is_in_progress',
        'end_date',
        'industry',
        'stack',
        'description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'industry' => 'array',
        'stack' => 'array'
    ];

    public function getExperience(): float|int
    {
        $start = Carbon::parse($this->start_date);
        if ($this->is_in_progress) {
            $end = Carbon::now();
        } else {
            $end = Carbon::parse($this->end_date);
        }

        return $end->diffInMonths($start);
    }
}
