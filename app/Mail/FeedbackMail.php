<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;
    public $feedback;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->feedback = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'You received a feedback from ' . config('app.name');
        
        if($this->feedback->attachment){
            
            $path_to_attachment = storage_path('app/feedbacks') . DIRECTORY_SEPARATOR . $this->feedback->attachment;
            return $this->view('emails.feedback-email')
                ->subject($subject)
                ->attach( $path_to_attachment );
        }

        return $this->view('emails.feedback-email')
                ->subject($subject);

        // ->attachFromStorage( $path_to_attachment );
    }
}
