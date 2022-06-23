<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketReply extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $message = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.  
     *
     * @return $this
     */
    public function build()
    {
         return $this->subject($this->message['title'])
        ->markdown('emails.ticket-reply')
        ->with(['message' => $this->message]);
    }
}
