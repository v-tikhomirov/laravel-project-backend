<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Technology;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TechnologiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tech:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import technologies from json';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $tech = file_get_contents('tech.json');
//        $tech = json_decode($tech, true);
//
//        $out = [];
//
//        foreach ($tech as $t) {
//            $out[] = [
//                'name' => $t['Language '],
//                'group' => Str::slug($t['Language ']),
//                'type' => 'language'
//            ];
//        }
//
//        file_put_contents('techDb.json',json_encode($out));

        return 0;
    }
}
