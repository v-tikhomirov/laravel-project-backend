<?php

namespace App\Console\Commands;

use App\Models\Matching;
use App\Notifications\OutstandingInterview;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckInterviewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:interviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if we have a matches that did nothing after interview time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->findAndNotify(Carbon::now(), 0);
        $this->findAndNotify(Carbon::now()->subDay(), 1);
        return 0;
    }

    protected function findAndNotify($date, $outstanding) {
        $matches = Matching::with(['interviews', 'vacancy.creator.profile', 'vacancy', 'candidate'])->whereHas('interviews', function($query) use ($date, $outstanding) {
            $query->whereDate('interview_date', '<', $date);
            $query->where('is_current', 1);
            if ($outstanding == 0) {
                $query->whereNull('outstanding_notification');
            } else {
                $query->where('outstanding_notification', 1);
            }
        })->where('status', Matching::STATUS_INTERVIEW)->get();

        foreach ($matches as $match) {
            $user = $match->vacancy->creator;
            $user->notify(new OutstandingInterview($user, $match));
            $interview = $match->interviews->where('is_current', 1)->first();
            $interview->update(['outstanding_notification' => $outstanding + 1]);
        }
    }

}
