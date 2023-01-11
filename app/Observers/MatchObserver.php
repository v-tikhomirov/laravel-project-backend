<?php

namespace App\Observers;

use App\Models\Matching;

class MatchObserver
{
    /**
     * Handle the Matching "created" event.
     *
     * @param  \App\Models\Matching  $matching
     * @return void
     */
    public function created(Matching $matching): void
    {
        $matching->history()->create([
            'type' => 'status',
            'action' => 'matched'
        ]);
    }

    /**
     * Handle the Matching "updated" event.
     *
     * @param  \App\Models\Matching  $matching
     * @return void
     */
    public function updated(Matching $matching): void
    {
        $matching->history()->create([
            'type' => 'status',
            'action' => 'updated:'.$matching->status
        ]);
    }
}
