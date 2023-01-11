<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'website',
        'about',
        'country_id',
        'city_id',
        'number_of_employees',
        'user_id',
        'is_branch',
        'candidates_countries',
        'link_to_linkedin',
        'link_to_github',
        'link_to_medium',
        'link_to_youtube',
        'link_to_stackoverflow',
        'link_to_facebook',
        'logo'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'user_company');
    }

    public function benefits(): BelongsToMany
    {
        return $this->belongsToMany(Benefit::class, 'company_benefit');
    }

    public function domains(): BelongsToMany
    {
        return $this->belongsToMany(Domain::class, 'company_domain');
    }

    public function branches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Company::class, 'company_id','id')->where('is_branch', 1);
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }
}
