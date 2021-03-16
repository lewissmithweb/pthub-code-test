<?php

namespace App\Notifications;

use App\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganisationCreated extends Notification
{
    use Queueable;

    /**
     * @var $organisation Organisation
     */
    protected $organisation;

    /**
     * Create a new notification instance.
     *
     * @param Organisation $organisation
     */
    public function __construct(Organisation $organisation)
    {
        $this->organisation = $organisation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting("New Organisation Created")
                    ->line("Organisation Name: " . $this->organisation->name)
                    ->line("Trail end date" . $this->organisation->trial_end->format("d/m/Y"));
    }
}
