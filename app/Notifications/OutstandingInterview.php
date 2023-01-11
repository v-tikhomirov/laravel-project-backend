<?php

namespace App\Notifications;

use App\Models\Matching;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutstandingInterview extends Notification
{
    use Queueable;

    protected Matching $match;
    protected User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Matching $match)
    {
        $this->match = $match;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $interview = $this->match->interviews->where('is_current', 1)->first();
        return (new MailMessage)
                    ->line('Hello, '. $this->user->profile->first_name ." ". $this->user->profile->last_name .'!')
                    ->line("We've noticed that you had a scheduled interview for ". $this->match->vacancy->position." at ". Carbon::parse($interview->interview_date)->format('Y d M') .". It would be great if you tell us how it went.")
                    ->action('Go to the app', env('APP_FRONTEND_URL'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $interview = $this->match->interviews->where('is_current', 1)->first();
        return [
            'interview_date' => $interview->interview_date,
            'vacancy_id' => $this->match->vacancy->id,
            'match_id' => $this->match->id,
            'vacancy_position' => $this->match->vacancy->position
        ];
    }
}
