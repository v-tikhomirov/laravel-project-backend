<?php

namespace App\Console\Commands;

use App\Models\Technology;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateTechWeightCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tech:calc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weight calculation for technologies';

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
        $selectedSkills = DB::table('skill_sets')
            ->select('technology_id', DB::raw('count(*) as total'))
            ->groupBy('technology_id')
            ->get();

        foreach ($selectedSkills as $skill) {
            $forUpdate[$skill->technology_id] = $skill->total;
        }

        $skills = Technology::whereIn('id', array_keys($forUpdate))->get();

        foreach ($skills as $skill) {
            $skill->update(['weight' => $forUpdate[$skill->id]]);
        }


        return 0;
    }
}
