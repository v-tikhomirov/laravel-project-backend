<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    private string $interview_date;
    private string $interview_time;
    private string $company_name;
    private string $user_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($match, array $interviewData)
    {
        $this->interview_date = $interviewData['interview_date'];
        $this->interview_time = $interviewData['interview_time'];
        $this->company_name = $match->company->name;
        $this->user_name = $match->candidate->profile->first_name . ' ' . $match->candidate->profile->last_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Interview')
            ->markdown('emails.interview', [
                'interview_date' => $this->interview_date,
                'interview_time' => $this->interview_time,
                'company_name' => $this->company_name,
                'user_name' => $this->user_name,
            ]);
    }
}
