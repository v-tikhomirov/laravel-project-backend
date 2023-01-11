<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalLanguages extends Model
{
    use HasFactory;
    protected $fillable = [
        'language_id',
        'level'
    ];

    protected $appends = ['name'];

    protected $with = array('language');

    protected $table = 'users_additional_languages';

    public function language(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->language->name
        );
    }
}
