<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Casts\OfficeType;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'company_id',
        'position',
        'office_type',
        'is_ready_to_relocate',
        'currency',
        'max_salary',
        'desired_salary',
        'description',
        'about',
        'status',
        'created_by',
        'city_id',
        'country_id'
    ];

    protected $casts = [
        'office_type' => OfficeType::class
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ARCHIVE = 'archive';

    public const YES_NO = [
        0 => 'no',
        1 => 'yes'
    ];

    public const STATUS = [
        1 => self::STATUS_ACTIVE,
        2 => self::STATUS_DRAFT,
        3 => self::STATUS_ARCHIVE
    ];

    public function skills(): HasMany
    {
        return $this->hasMany(VacancySkillSet::class);
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'vacancy_domain');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'vacancy_benefit');
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'vacancy_language')->withPivot('level');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
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
