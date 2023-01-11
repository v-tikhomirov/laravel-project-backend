<?php

namespace App\Models;

use Nnjeim\World\Models\Language as VendorLanguage;

/**
 * @method static where(string $string, $name)
 */
class Language extends VendorLanguage
{
    /**
     * @param string $name
     * @return mixed
     */
    static function getIdByName(string $name): int {
        return Language::where('name', $name)->firstOrFail('id')->id;
    }
}
