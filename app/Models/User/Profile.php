<?php

namespace App\Models\User;

use App\Models\AdditionalLanguages;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Nnjeim\World\Models\City;
use Nnjeim\World\Models\Country;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';
    protected $with = ['language', 'additionalLanguages'];

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'birthdate',
        'country_id',
        'city_id',
        'education',
        'is_wa_as_phone',
        'whatsapp',
        'telegram',
        'country_code',
        'native_language_id',
        'job_role',
        'link_to_linkedin',
        'link_to_facebook',
        'link_to_stackoverflow',
        'link_to_youtube',
        'link_to_medium',
        'link_to_github',
        'link_to_other',
        'is_journey_finished',
        'profile_picture'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function language(): HasOne
    {
        return $this->hasOne(Language::class, 'id', 'native_language_id');
    }

    public function additionalLanguages(): HasMany
    {
        return $this->hasMany(AdditionalLanguages::class, 'user_id', 'user_id');
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

}
