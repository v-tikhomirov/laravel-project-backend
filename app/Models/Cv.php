<?php

namespace App\Models;

use App\Casts\OfficeType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cv extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'position',
        'about',
        'office_type',
        'is_ready_to_relocate',
        'type',
        'currency',
        'minimal_salary',
        'desired_salary',
        'status',
        'is_draft'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => 'array',
        'office_type' => OfficeType::class
    ];

    public const YES_NO = [
        0 => 'no',
        1 => 'yes'
    ];

    public const STATUS = [
        0 => null,
        1 => 'open',
        2 => 'passive',
        3 => 'closed'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CvSkillSet::class);
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class);
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'cv_language')->withPivot('level');
    }

    public function recentProjects(): HasMany
    {
        return $this->hasMany(RecentProject::class);
    }

    protected function isReadyToRelocate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::YES_NO[$value],
            set: fn ($value) => array_search($value, self::YES_NO),
        );
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::STATUS[$value],
            set: fn ($value) => array_search($value, self::STATUS),
        );
    }

}
