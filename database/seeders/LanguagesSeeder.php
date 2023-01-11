<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::truncate();

        $json = File::get("database/data/languages.json");
        $languages = json_decode($json);

        foreach ($languages as $key => $value) {
            Language::create([
                "name" => $value->name,
                "code" => $value->code,
                "nativeName" => $value->nativeName
            ]);
        }
    }
}
