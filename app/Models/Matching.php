<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Matching extends Model
{
    use HasFactory;

    const STATUS_MATCHED = 1;
    const STATUS_CANDIDATE_INTERESTED = 2;
    const STATUS_COMPANY_INTERESTED = 3;
    const STATUS_CANDIDATE_POSTPONE = 4;
    const STATUS_COMPANY_POSTPONE = 5;
    const STATUS_INTERVIEW = 6;
    const STATUS_OFFER = 7;
    const STATUS_COMPLETE = 99;
    const STATUS_HIDDEN = 999;
    const STATUS_DECLINED_BY_COMPANY = 100;
    const STATUS_DECLINED_BY_CANDIDATE = 101;

    protected $fillable = [
        'user_id',
        'company_id',
        'cv_id',
        'percent',
        'vacancy_id',
        'status'
    ];

    protected $table = 'matches';

    public function history(): HasMany
    {
        return $this->hasMany(MatchesHistory::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(MatchNotes::class, 'match_id', 'id')->where("is_deleted", 0 );
    }
}
