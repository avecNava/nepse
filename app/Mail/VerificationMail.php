<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;
    pubic user;
    public $action;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $action)
    {
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Welcome from ' . config('app.name');
        return $this->view('emails.user-verify')->subject($subject);
    }
}
