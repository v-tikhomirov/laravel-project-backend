<?php

namespace Database\Seeders;

use App\Models\Technology;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TechnologiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = File::allFiles("database/data/technologies");
        if (File::exists('database/data/technologies/data.lock')) {
            $skip = explode(':', File::get('database/data/technologies/data.lock'));
        } else {
            $skip = [];
        }
        $processed = [];

        foreach ($files as $file) {
            if (in_array($file->getFilename(), $skip) || $file->getFilename() == 'data.lock') continue;
            $json = json_decode(file_get_contents($file->getPathname()),true);
            $insert = [];

            foreach ($json as $item) {
                $insert[] = $this->sanitize($item);
            }
            Technology::insert($insert);
            $processed[] = $file->getFilename();
        }

        File::put("database/data/technologies/data.lock",implode(':',array_merge($skip,$processed)));
    }

    protected function sanitize($array): array
    {
        $out = [];
        foreach ($array as $key => $value) {
            if ($key == '#') continue;
            $lowerKey = Str::lower($key);
            if ($lowerKey == 'group') {
                $out[$lowerKey] = Str::slug($value);
            } else {
                $out[$lowerKey] = $value;
            }
        }

        if ($out['group'] === Str::slug($out['name'])) {
            $out['is_root'] = 1;
        } else {
            $out['is_root'] = 0;
        }

        $now = Carbon::now('utc')->toDateTimeString();
        $out['created_at'] = $now;
        $out['updated_at'] = $now;

        return $out;
    }
}
