<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Cv;
use App\Models\Matching;
use App\Models\User;
use App\Models\Vacancy;
use App\Notifications\NewMatch;
use App\Services\MatchService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $entity;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->entity = Cv::find(31);
//        $this->entity = Vacancy::find(2);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $matchServie = new MatchService();
        $matchServie->run($this->entity);

        return 0;
    }
}
