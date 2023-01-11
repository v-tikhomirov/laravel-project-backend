<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Cv;
use App\Models\Matching;
use App\Models\User;
use App\Models\Vacancy;
use App\Services\MatchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TryToMatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public MatchService $matchService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public $entity
    )
    {
        $this->matchService = new MatchService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->matchService->run($this->entity);
    }
}
