<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $data;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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
        //1. use view 
        return (new MailMessage)->view(
            'emails.welcome', ['user' => $this->data]
        );

        //2. return mailable object
        // return (new \App\Mail\WelcomeMail($this->data))
        //         ->to($notifiable->email);

        //https://laravel.com/docs/8.x/notifications#other-mail-notification-formatting-options
        //mail message using lines
        // return (new MailMessage)
        //         ->line('The introduction to the notification.')
        //         ->action('Notification Action', url('/'))
        //         ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
