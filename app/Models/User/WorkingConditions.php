<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingConditions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'office_type',
        'is_ready_to_relocate',
        'currency',
        'minimal_salary',
        'desired_salary',
        'status'
    ];

    public const OFFICE_TYPE = [
        1 => 'office',
        2 => 'hybrid',
        3 => 'remote'
    ];

    public const YES_NO = [
        0 => 'no',
        1 => 'yes'
    ];

    public const STATUS = [
        1 => 'open',
        2 => 'passive',
        3 => 'closed'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Interact with the office_type.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function officeType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => self::OFFICE_TYPE[$value],
            set: fn ($value) => array_search($value, self::OFFICE_TYPE),
        );
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
